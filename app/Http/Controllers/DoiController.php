<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class DoiController extends Controller
{
    /**
     * Fetch metadata dari Crossref API berdasarkan DOI
     * Endpoint: GET /api/doi?doi=10.xxxx/xxxxx
     */
    public function fetch(Request $request)
    {
        $doi = trim($request->input('doi', ''));

        if (empty($doi)) {
            return response()->json(['error' => 'DOI tidak boleh kosong.'], 422);
        }

        // Bersihkan input DOI — bisa berupa URL atau DOI langsung
        $doi = $this->cleanDoi($doi);

        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'User-Agent' => 'SistemProfilDosenVokasi/1.0 (mailto:admin@del.ac.id)',
                ])
                ->get("https://api.crossref.org/works/{$doi}");

            if ($response->failed()) {
                return response()->json([
                    'error' => 'DOI tidak ditemukan di Crossref. Pastikan DOI benar.'
                ], 404);
            }

            $data = $response->json()['message'] ?? null;

            if (!$data) {
                return response()->json(['error' => 'Tidak dapat membaca data dari Crossref.'], 500);
            }

            // Ambil tahun publikasi
            $year = null;
            if (isset($data['published']['date-parts'][0][0])) {
                $year = (int) $data['published']['date-parts'][0][0];
            } elseif (isset($data['created']['date-parts'][0][0])) {
                $year = (int) $data['created']['date-parts'][0][0];
            }

            // Ambil judul
            $title = $data['title'][0] ?? null;
            if (!$title) {
                return response()->json(['error' => 'Judul tidak ditemukan di metadata DOI ini.'], 404);
            }

            // Ambil nama jurnal/penerbit
            $publisher = null;
            if (!empty($data['container-title'])) {
                $publisher = $data['container-title'][0];
            } elseif (!empty($data['publisher'])) {
                $publisher = $data['publisher'];
            } elseif (!empty($data['institution'][0]['name'])) {
                $publisher = $data['institution'][0]['name'];
            }

            // Ambil tipe dokumen
            $type = $data['type'] ?? 'journal-article';

            // Ambil penulis
            $authors = [];
            if (!empty($data['author'])) {
                foreach ($data['author'] as $author) {
                    $name = trim(($author['given'] ?? '') . ' ' . ($author['family'] ?? ''));
                    if ($name) $authors[] = $name;
                }
            }

            return response()->json([
                'success' => true,
                'doi'     => $doi,
                'data'    => [
                    'title'         => $title,
                    'year'          => $year,
                    'publisher'     => $publisher,
                    'publisher_url' => "https://doi.org/{$doi}",
                    'type'          => $type,
                    'authors'       => $authors,
                ]
            ]);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('DOI lookup gagal (Crossref)', [
                'doi' => $doi,
                'pesan_asli' => $e->getMessage(),
            ]);
            return response()->json([
                'error' => 'Gagal menghubungi Crossref API. Periksa koneksi internet.'
            ], 500);
        }
    }

    /**
     * Bersihkan input DOI:
     * - https://doi.org/10.xxxx/xxx → 10.xxxx/xxx
     * - doi:10.xxxx/xxx             → 10.xxxx/xxx
     * - 10.xxxx/xxx                 → 10.xxxx/xxx (tidak diubah)
     */
    private function cleanDoi(string $doi): string
    {
        // Hapus prefix URL
        $doi = preg_replace('#^https?://(dx\.)?doi\.org/#i', '', $doi);
        // Hapus prefix "doi:"
        $doi = preg_replace('/^doi:/i', '', $doi);
        return trim($doi);
    }
}
