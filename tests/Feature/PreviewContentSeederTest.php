<?php

namespace Tests\Feature;

use Database\Seeders\PreviewContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PreviewContentSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_preview_content_seeder_is_repeatable_and_populates_admin_sections(): void
    {
        Storage::fake('public');

        $this->seed(PreviewContentSeeder::class);
        $this->seed(PreviewContentSeeder::class);

        $this->assertDatabaseCount('product_categories', 8);
        $this->assertDatabaseCount('products', 8);
        $this->assertDatabaseCount('services', 6);
        $this->assertDatabaseCount('projects', 6);
        $this->assertDatabaseCount('news_articles', 6);
        $this->assertDatabaseCount('team_members', 4);
        $this->assertDatabaseCount('contact_requests', 4);
        $this->assertDatabaseCount('media', 17);

        Storage::disk('public')->assertExists('media/preview/service-design.svg');
        Storage::disk('public')->assertExists('media/preview/project-ev.svg');
        Storage::disk('public')->assertExists('media/preview/team-04.svg');

        $this->getJson('/api/v1/services')
            ->assertOk()
            ->assertJsonCount(6)
            ->assertJsonPath('0.slug', 'preview-commissioning');

        $this->getJson('/api/v1/projects')
            ->assertOk()
            ->assertJsonCount(6);

        $this->getJson('/api/v1/news')
            ->assertOk()
            ->assertJsonCount(6);

        $this->getJson('/api/v1/team')
            ->assertOk()
            ->assertJsonCount(4);

        $this->getJson('/api/v1/products')
            ->assertOk()
            ->assertJsonPath('total', 8);
    }
}
