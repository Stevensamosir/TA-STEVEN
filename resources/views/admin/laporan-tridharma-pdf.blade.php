<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: sans-serif; font-size: 11px; }
        h1 { font-size: 16px; margin-bottom: 4px; }
        h2 { font-size: 13px; margin-top: 20px; margin-bottom: 6px; background: #003087; color: white; padding: 4px 8px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        th, td { border: 1px solid #ccc; padding: 4px 6px; text-align: left; }
        th { background: #f0f0f0; }
    </style>
</head>
<body>
    <h1>Laporan Tridharma — Fakultas Vokasi IT Del</h1>
    <p>Periode: {{ $isAllTime ? 'Semua' : ucfirst(str_replace('_',' ',$periode)) }} &middot; Dicetak: {{ now()->format('d M Y H:i') }}</p>

    <h2>Penelitian ({{ $penelitian->count() }} data)</h2>
    <table>
        <thead><tr><th>Nama Dosen</th><th>Prodi</th><th>Judul</th><th>Tahun</th></tr></thead>
        <tbody>
            @forelse($penelitian as $r)
            <tr>
                <td>{{ $r->lecturer->user->name ?? '-' }}</td>
                <td>{{ $r->lecturer->studyProgram->name ?? '-' }}</td>
                <td>{{ $r->title }}</td>
                <td>{{ $r->year }}</td>
            </tr>
            @empty
            <tr><td colspan="4">Tidak ada data.</td></tr>
            @endforelse
        </tbody>
    </table>

    <h2>PKM ({{ $pkm->count() }} data)</h2>
    <table>
        <thead><tr><th>Nama Dosen</th><th>Prodi</th><th>Judul</th><th>Tahun</th></tr></thead>
        <tbody>
            @forelse($pkm as $p)
            <tr>
                <td>{{ $p->lecturers->pluck('user.name')->join(', ') ?: '-' }}</td>
                <td>{{ $p->lecturers->map(fn($l) => $l->studyProgram->name ?? '-')->unique()->join(', ') ?: '-' }}</td>
                <td>{{ $p->title }}</td>
                <td>{{ $p->year }}</td>
            </tr>
            @empty
            <tr><td colspan="4">Tidak ada data.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
