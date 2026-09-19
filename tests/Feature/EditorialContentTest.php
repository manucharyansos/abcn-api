<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EditorialContentTest extends TestCase
{
    use RefreshDatabase;

    public function test_an_admin_can_manage_services_projects_and_news(): void
    {
        $token = $this->adminToken();

        $service = $this->withToken($token)->postJson('/api/v1/admin/services', [
            ...$this->entryPayload('engineering-support', 'Ինժեներական աջակցություն', 'Engineering support'),
            'show_on_homepage' => true,
        ])->assertCreated()->assertJsonPath('show_on_homepage', true);

        $this->withToken($token)->postJson('/api/v1/admin/projects', [
            ...$this->entryPayload('factory-upgrade', 'Գործարանի արդիականացում', 'Factory upgrade'),
            'show_on_homepage' => true,
            'completed_at' => '2026-09-01',
        ])->assertCreated()->assertJsonPath('completed_at', '2026-09-01');

        $this->withToken($token)->postJson('/api/v1/admin/news', [
            ...$this->entryPayload('new-partnership', 'Նոր գործընկերություն', 'New partnership'),
            'show_on_homepage' => true,
            'published_at' => '2026-09-19',
        ])->assertCreated()->assertJsonPath('slug', 'new-partnership');

        $this->withToken($token)->putJson("/api/v1/admin/services/{$service->json('id')}", [
            ...$this->entryPayload('engineering-support', 'Ինժեներական խորհրդատվություն', 'Engineering consulting'),
            'show_on_homepage' => false,
        ])->assertOk()->assertJsonPath('translations.hy.title', 'Ինժեներական խորհրդատվություն');

        $this->getJson('/api/v1/services')
            ->assertOk()
            ->assertJsonPath('0.slug', 'engineering-support');

        $this->getJson('/api/v1/projects')->assertOk()->assertJsonCount(1);
        $this->getJson('/api/v1/news')->assertOk()->assertJsonCount(1);
    }

    public function test_homepage_only_returns_published_checked_items(): void
    {
        Service::query()->create([
            ...$this->entryPayload('visible-service', 'Տեսանելի ծառայություն', 'Visible service'),
            'show_on_homepage' => true,
        ]);
        Service::query()->create([
            ...$this->entryPayload('hidden-service', 'Թաքնված ծառայություն', 'Hidden service'),
            'show_on_homepage' => false,
        ]);
        Service::query()->create([
            ...$this->entryPayload('draft-service', 'Սևագիր ծառայություն', 'Draft service'),
            'status' => 'draft',
            'show_on_homepage' => true,
        ]);

        Product::query()->create([
            'slug' => 'homepage-product',
            'status' => 'published',
            'featured' => false,
            'show_on_homepage' => true,
            'sort_order' => 0,
            'translations' => [
                'hy' => ['name' => 'Գլխավոր էջի ապրանք'],
                'en' => ['name' => 'Homepage product'],
            ],
        ]);

        $this->getJson('/api/v1/homepage')
            ->assertOk()
            ->assertJsonCount(1, 'services')
            ->assertJsonPath('services.0.slug', 'visible-service')
            ->assertJsonCount(1, 'products')
            ->assertJsonPath('products.0.slug', 'homepage-product');
    }

    public function test_editorial_images_are_limited_to_four(): void
    {
        $token = $this->adminToken();

        $this->withToken($token)->postJson('/api/v1/admin/projects', [
            ...$this->entryPayload('gallery-limit', 'Պատկերասրահ', 'Gallery'),
            'images' => array_map(
                fn (int $index) => ['url' => "/images/project-{$index}.webp"],
                range(1, 5),
            ),
        ])->assertUnprocessable()->assertJsonValidationErrors('images');
    }

    private function adminToken(): string
    {
        $token = 'valid-editorial-admin-token';
        User::factory()->create([
            'role' => 'admin',
            'api_token' => hash('sha256', $token),
        ]);

        return $token;
    }

    private function entryPayload(string $slug, string $titleHy, string $titleEn): array
    {
        return [
            'slug' => $slug,
            'status' => 'published',
            'show_on_homepage' => false,
            'sort_order' => 0,
            'translations' => [
                'hy' => ['title' => $titleHy, 'summary' => 'Կարճ նկարագրություն', 'body' => 'Լրիվ տեքստ'],
                'en' => ['title' => $titleEn, 'summary' => 'Short summary', 'body' => 'Full text'],
            ],
            'images' => [],
        ];
    }
}
