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
        // Generate a cryptographically secure password
        $securePassword = bin2hex(random_bytes(16)); // 32-char hex string

        User::create([
            'name' => 'Admin ShadAlkane',
            'username' => 'admin',
            'email' => 'admin@shadalkane.com',
            'password' => Hash::make($securePassword),
        ]);

        // Output the generated password to console
        $this->command->info('');
        $this->command->info('========================================');
        $this->command->info('  Admin User Created Successfully!');
        $this->command->info('========================================');
        $this->command->info("  Username: admin");
        $this->command->info("  Password: {$securePassword}");
        $this->command->info('========================================');
        $this->command->warn('  SIMPAN PASSWORD INI! Tidak bisa dilihat lagi.');
        $this->command->info('');
    }
}
