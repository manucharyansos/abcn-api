<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Page;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $email = env('ADMIN_EMAIL');
        $password = env('ADMIN_PASSWORD');

        if ($email && $password) {
            User::query()->updateOrCreate(
                ['email' => $email],
                [
                    'name' => env('ADMIN_NAME', 'ABCN Administrator'),
                    'password' => $password,
                    'role' => 'admin',
                ],
            );
        }

        foreach (['home', 'about', 'solutions', 'products', 'contact'] as $slug) {
            Page::query()->firstOrCreate(
                ['slug' => $slug],
                [
                    'status' => 'published',
                    'content' => [
                        'hy' => ['title' => $slug],
                        'en' => ['title' => ucfirst($slug)],
                    ],
                    'meta' => [],
                ],
            );
        }
    }
}
