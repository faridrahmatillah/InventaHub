<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])) {
            return;
        }

        // Akun Admin: boleh melakukan semua aksi, termasuk menghapus data.
        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin Gudang',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        // Akun Staff: hanya boleh melihat dan menambah data, tidak boleh menghapus.
        User::firstOrCreate(
            ['email' => 'staff@example.com'],
            [
                'name' => 'Staff Gudang',
                'password' => Hash::make('password'),
                'role' => 'staff',
            ]
        );
    }
}
