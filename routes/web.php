
<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::get('/mahasiswa/{nim?}', function (?string $nim = null) {
    try {
        // Mengambil koneksi PDO dari Laravel
        $pdo = DB::connection()->getPdo();

        // Query disesuaikan dengan struktur tabel sebenarnya
        $sql = 'SELECT
                    m.nim,
                    m.nama,
                    m.email,
                    m.semester,
                    p.nama_program_studi,
                    p.fakultas
                FROM mahasiswa AS m
                JOIN program_studi AS p
                    ON p.program_studi_id = m.program_studi_id';

        // Filter berdasarkan NIM jika diberikan
        if ($nim !== null) {
            $sql .= ' WHERE m.nim = :nim';
        }

        $sql .= ' ORDER BY m.nim';

        // Prepared statement PDO
        $statement = $pdo->prepare($sql);

        if ($nim !== null) {
            $statement->execute(['nim' => $nim]);
        } else {
            $statement->execute();
        }

        $daftarMahasiswa = $statement->fetchAll(
            PDO::FETCH_ASSOC
        );

        return view(
            'mahasiswa',
            compact('daftarMahasiswa', 'nim')
        );

    } catch (\Throwable $error) {
        // Simpan detail error ke log Laravel
        report($error);

        return response(
            'Koneksi atau query basis data gagal. Periksa log Laravel.',
            500
        );
    }
});
