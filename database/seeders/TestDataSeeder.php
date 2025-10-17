<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Display;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear tables
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('displays')->truncate();
        DB::table('users')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Create test users
        $users = [
            [
                'name' => 'Usuario Test 1',
                'email' => 'test1@example.com',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Usuario Test 2', 
                'email' => 'test2@example.com',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]
        ];

        $createdUsers = [];
        foreach ($users as $userData) {
            $user = User::create($userData);
            $createdUsers[] = $user;
        }

        // Create test displays
        $displays = [
            // User 1 - 5 displays
            [
                'name' => 'Display LED 4K - Centro',
                'description' => 'Pantalla LED 4K ubicada en el centro comercial',
                'price_per_day' => 150.00,
                'resolution_height' => 2160,
                'resolution_width' => 3840,
                'type' => 'indoor',
                'user_id' => $createdUsers[0]->id,
            ],
            [
                'name' => 'Display Exterior Times Square',
                'description' => 'Pantalla exterior resistente al clima en Times Square',
                'price_per_day' => 300.00,
                'resolution_height' => 1080,
                'resolution_width' => 1920,
                'type' => 'outdoor',
                'user_id' => $createdUsers[0]->id,
            ],
            [
                'name' => 'Display LED Shopping Mall',
                'description' => 'Pantalla LED para centro comercial con alta resolución',
                'price_per_day' => 200.00,
                'resolution_height' => 1440,
                'resolution_width' => 2560,
                'type' => 'indoor',
                'user_id' => $createdUsers[0]->id,
            ],
            [
                'name' => 'Display Digital Billboard',
                'description' => 'Cartelera digital para publicidad exterior',
                'price_per_day' => 250.00,
                'resolution_height' => 1080,
                'resolution_width' => 1920,
                'type' => 'outdoor',
                'user_id' => $createdUsers[0]->id,
            ],
            [
                'name' => 'Display LED Corporativo',
                'description' => 'Pantalla LED para oficinas corporativas',
                'price_per_day' => 120.00,
                'resolution_height' => 1080,
                'resolution_width' => 1920,
                'type' => 'indoor',
                'user_id' => $createdUsers[0]->id,
            ],
            
            // User 2 - 5 displays
            [
                'name' => 'Display LED Stadium',
                'description' => 'Pantalla LED para estadio deportivo',
                'price_per_day' => 500.00,
                'resolution_height' => 2160,
                'resolution_width' => 3840,
                'type' => 'outdoor',
                'user_id' => $createdUsers[1]->id,
            ],
            [
                'name' => 'Display Interior Oficina',
                'description' => 'Pantalla interior para oficinas modernas',
                'price_per_day' => 80.00,
                'resolution_height' => 1080,
                'resolution_width' => 1920,
                'type' => 'indoor',
                'user_id' => $createdUsers[1]->id,
            ],
            [
                'name' => 'Display LED Aeropuerto',
                'description' => 'Pantalla LED para terminal de aeropuerto',
                'price_per_day' => 400.00,
                'resolution_height' => 1440,
                'resolution_width' => 2560,
                'type' => 'indoor',
                'user_id' => $createdUsers[1]->id,
            ],
            [
                'name' => 'Display Exterior Carretera',
                'description' => 'Cartelera exterior en carretera principal',
                'price_per_day' => 180.00,
                'resolution_height' => 1080,
                'resolution_width' => 1920,
                'type' => 'outdoor',
                'user_id' => $createdUsers[1]->id,
            ],
            [
                'name' => 'Display LED Restaurante',
                'description' => 'Pantalla LED para restaurante de lujo',
                'price_per_day' => 90.00,
                'resolution_height' => 1080,
                'resolution_width' => 1920,
                'type' => 'indoor',
                'user_id' => $createdUsers[1]->id,
            ],
        ];

        foreach ($displays as $displayData) {
            Display::create($displayData);
        }

        // Show created data for Postman
        $this->command->info('=== TEST DATA CREATED ===');
        $this->command->info('');
        
        foreach ($createdUsers as $index => $user) {
            $this->command->info("USER " . ($index + 1) . ":");
            $this->command->info("  Email: {$user->email}");
            $this->command->info("  Password: password123");
            $this->command->info("  ID: {$user->id}");
            $this->command->info('');
        }

        $this->command->info('=== DISPLAYS CREATED ===');
        $displaysByUser = Display::with('user')->get()->groupBy('user_id');
        
        foreach ($displaysByUser as $userId => $userDisplays) {
            $user = $userDisplays->first()->user;
            $this->command->info("Displays for {$user->email} ({$userDisplays->count()} displays):");
            foreach ($userDisplays as $display) {
                $this->command->info("  - ID: {$display->id} | {$display->name} | {$display->type} | \${$display->price_per_day}");
            }
            $this->command->info('');
        }

        $this->command->info('=== POSTMAN COMMANDS ===');
        $this->command->info('');
        $this->command->info('1. Login User 1:');
        $this->command->info('   POST http://localhost:8080/api/login');
        $this->command->info('   Body: {"email": "test1@example.com", "password": "password123"}');
        $this->command->info('');
        $this->command->info('2. Login User 2:');
        $this->command->info('   POST http://localhost:8080/api/login');
        $this->command->info('   Body: {"email": "test2@example.com", "password": "password123"}');
        $this->command->info('');
        $this->command->info('3. View authenticated user displays:');
        $this->command->info('   GET http://localhost:8080/api/displays');
        $this->command->info('   Header: Authorization: Bearer [token]');
    }
}
