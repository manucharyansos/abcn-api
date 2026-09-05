<?php

namespace Tests\Feature;

use App\Models\Media;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_an_admin_can_login_and_open_the_dashboard(): void
    {
        User::factory()->create([
            'email' => 'admin@abcn.test',
            'password' => Hash::make('strong-password'),
            'role' => 'admin',
        ]);

        $login = $this->postJson('/api/v1/admin/login', [
            'email' => 'admin@abcn.test',
            'password' => 'strong-password',
        ])->assertOk()->assertJsonStructure(['token', 'user']);

        $token = $login->json('token');

        $this->withToken($token)
            ->getJson('/api/v1/admin/dashboard')
            ->assertOk()
            ->assertJsonStructure(['counts', 'requests']);
    }

    public function test_dashboard_rejects_an_invalid_token(): void
    {
        $this->withToken('invalid-token')
            ->getJson('/api/v1/admin/dashboard')
            ->assertUnauthorized();
    }

    public function test_an_admin_can_upload_and_delete_a_pdf(): void
    {
        Storage::fake('public');

        $token = 'valid-admin-token';
        User::factory()->create([
            'role' => 'admin',
            'api_token' => hash('sha256', $token),
        ]);

        $response = $this->withToken($token)->post('/api/v1/admin/media', [
            'file' => UploadedFile::fake()->create('catalog.pdf', 120, 'application/pdf'),
            'alt' => ['hy' => 'Կատալոգ', 'en' => 'Catalog'],
        ])->assertCreated()->assertJsonPath('kind', 'document');

        $media = Media::query()->findOrFail($response->json('id'));
        Storage::disk('public')->assertExists($media->path);

        $this->withToken($token)
            ->deleteJson("/api/v1/admin/media/{$media->id}")
            ->assertNoContent();

        Storage::disk('public')->assertMissing($media->path);
    }

    public function test_an_admin_can_create_a_category_and_product(): void
    {
        $token = 'valid-admin-token';
        User::factory()->create([
            'role' => 'admin',
            'api_token' => hash('sha256', $token),
        ]);

        $category = $this->withToken($token)->postJson('/api/v1/admin/product-categories', [
            'slug' => 'circuit-breakers',
            'status' => 'published',
            'sort_order' => 1,
            'translations' => [
                'hy' => ['name' => 'Ավտոմատ անջատիչներ'],
                'en' => ['name' => 'Circuit breakers'],
            ],
        ])->assertCreated();

        $this->withToken($token)->postJson('/api/v1/admin/products', [
            'product_category_id' => $category->json('id'),
            'slug' => 'test-breaker',
            'sku' => 'ABCN-001',
            'status' => 'published',
            'featured' => true,
            'translations' => [
                'hy' => ['name' => 'Փորձնական անջատիչ'],
                'en' => ['name' => 'Test breaker'],
            ],
        ])->assertCreated();

        $this->getJson('/api/v1/products')
            ->assertOk()
            ->assertJsonPath('data.0.slug', 'test-breaker');
    }

    public function test_a_product_cannot_have_more_than_four_images(): void
    {
        $token = 'valid-admin-token';
        User::factory()->create([
            'role' => 'admin',
            'api_token' => hash('sha256', $token),
        ]);

        $this->withToken($token)->postJson('/api/v1/admin/products', [
            'product_category_id' => null,
            'slug' => 'gallery-limit-test',
            'sku' => 'ABCN-GALLERY-TEST',
            'status' => 'draft',
            'featured' => false,
            'translations' => [
                'hy' => ['name' => 'Պատկերասրահի փորձարկում'],
                'en' => ['name' => 'Gallery limit test'],
            ],
            'images' => array_map(
                fn (int $index) => ['url' => "/images/test-{$index}.webp"],
                range(1, 5),
            ),
        ])->assertUnprocessable()->assertJsonValidationErrors('images');
    }
}
