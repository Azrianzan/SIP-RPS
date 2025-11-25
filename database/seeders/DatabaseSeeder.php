<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Role;
use App\Models\User;
use App\Models\Sekolah;
use App\Models\Proyek; // Tambahkan import Model Proyek

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ---------------------------------------------------------
        // 1. BUAT ROLES
        // ---------------------------------------------------------
        $roleAdmin = Role::firstOrCreate(['nama_role' => 'Admin']);
        $rolePimpinan = Role::firstOrCreate(['nama_role' => 'Pimpinan']);
        $rolePjl = Role::firstOrCreate(['nama_role' => 'PJL']);

        // ---------------------------------------------------------
        // 2. BUAT USERS (Simpan ke variabel untuk dipakai di proyek)
        // ---------------------------------------------------------
        // User Admin
        $userAdmin = User::create([
            'role_id' => $roleAdmin->id,
            'nama' => 'Admin Disdikbud',
            'email' => 'dosenadmin@gmail.com',
            'password' => Hash::make('admin123'),
        ]);

        // User Pimpinan
        User::create([
            'role_id' => $rolePimpinan->id,
            'nama' => 'Kepala Dinas',
            'email' => 'dosenpimpinan@gmail.com',
            'password' => Hash::make('pimpinan123'),
        ]);

        // User PJL (Penanggung Jawab Lapangan)
        $userPjl = User::create([
            'role_id' => $rolePjl->id,
            'nama' => 'Budi Santoso',
            'email' => 'dosenpjl@gmail.com',
            'password' => Hash::make('pjl123'),
        ]);

        // ---------------------------------------------------------
        // 3. BUAT DATA SEKOLAH (Sesuai Struktur Baru)
        // ---------------------------------------------------------
        $sd1 = Sekolah::create([
            'nama_sekolah' => 'SDN 1 Banjarmasin',
            'jenjang' => 'SD',
            'alamat' => 'Jl. Pangeran Samudera No. 1',
            'kabupaten_kota' => 'Banjarmasin',
            'kecamatan' => 'Banjarmasin Tengah',
            'telepon' => '0511-3356789',
            'email' => 'sdn1bjm@example.com',
            'kepala_sekolah' => 'Drs. Ahmad Fauzi',
            'jumlah_siswa' => 425,
            'kondisi_sekolah' => 'Baik',
        ]);

        $smp2 = Sekolah::create([
            'nama_sekolah' => 'SMPN 2 Banjarbaru',
            'jenjang' => 'SMP',
            'alamat' => 'Jl. Jenderal Sudirman No. 45',
            'kabupaten_kota' => 'Banjarbaru',
            'kecamatan' => 'Banjarbaru Utara',
            'telepon' => '0511-4789012',
            'email' => 'smpn2bjb@example.com',
            'kepala_sekolah' => 'Siti Aminah, S.Pd',
            'jumlah_siswa' => 680,
            'kondisi_sekolah' => 'Cukup',
        ]);

        $sma3 = Sekolah::create([
            'nama_sekolah' => 'SMAN 3 Martapura',
            'jenjang' => 'SMA',
            'alamat' => 'Jl. Brigjen H. Hasan Basri No. 88',
            'kabupaten_kota' => 'Martapura',
            'kecamatan' => 'Martapura Kota',
            'telepon' => '0511-6123456',
            'email' => 'sman3mtp@example.com',
            'kepala_sekolah' => 'Drs. Muhammad Ali',
            'jumlah_siswa' => 890,
            'kondisi_sekolah' => 'Rusak Ringan',
        ]);

        // ---------------------------------------------------------
        // 4. BUAT DATA PROYEK (Relasi ke Sekolah & User)
        // ---------------------------------------------------------
        // Proyek 1: Selesai (Di SDN 1 Banjarmasin)
        Proyek::create([
            'sekolah_id' => $sd1->id,
            'admin_id' => $userAdmin->id,
            'pjl_id' => $userPjl->id,
            'nama_proyek' => 'Perbaikan Atap Gedung Utama',
            'deskripsi' => 'Penggantian seng dan rangka atap yang sudah lapuk dimakan usia.',
            'anggaran' => 150000000, // 150 Juta
            'tanggal_mulai' => '2025-01-10',
            'tanggal_selesai' => '2025-03-10',
            'status_proyek' => 'Selesai',
        ]);

        // Proyek 2: Berjalan (Di SMPN 2 Banjarbaru)
        Proyek::create([
            'sekolah_id' => $smp2->id,
            'admin_id' => $userAdmin->id,
            'pjl_id' => $userPjl->id,
            'nama_proyek' => 'Renovasi Toilet Siswa',
            'deskripsi' => 'Perbaikan sanitasi dan penambahan kran air.',
            'anggaran' => 85000000, // 85 Juta
            'tanggal_mulai' => '2025-06-01',
            'tanggal_selesai' => '2025-08-30',
            'status_proyek' => 'Berjalan',
        ]);

        // Proyek 3: Terlambat (Di SMAN 3 Martapura)
        Proyek::create([
            'sekolah_id' => $sma3->id,
            'admin_id' => $userAdmin->id,
            'pjl_id' => $userPjl->id,
            'nama_proyek' => 'Pembangunan Pagar Sekolah',
            'deskripsi' => 'Pembangunan pagar beton keliling sekolah.',
            'anggaran' => 200000000, // 200 Juta
            'tanggal_mulai' => '2025-04-01',
            'tanggal_selesai' => '2025-07-01', // Seharusnya sudah selesai
            'status_proyek' => 'Terlambat',
        ]);
    }
}