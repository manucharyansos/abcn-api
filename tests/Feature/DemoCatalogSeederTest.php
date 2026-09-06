<?php

namespace Tests\Feature;

use App\Models\ProductCategory;
use Database\Seeders\DemoCatalogSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DemoCatalogSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_demo_catalog_is_repeatable_and_available_through_public_endpoints(): void
    {
        $this->seed(DemoCatalogSeeder::class);
        $this->seed(DemoCatalogSeeder::class);

        $this->assertDatabaseCount('product_categories', 8);
        $this->assertDatabaseCount('products', 8);
        $this->assertDatabaseCount('product_filter_attributes', 16);

        $this->getJson('/api/v1/product-categories')
            ->assertOk()
            ->assertJsonCount(3)
            ->assertJsonPath('0.slug', 'demo-low-voltage')
            ->assertJsonPath('0.children.0.slug', 'demo-main-power-distribution')
            ->assertJsonCount(3, '0.children');

        $this->getJson('/api/v1/products')
            ->assertOk()
            ->assertJsonPath('total', 8)
            ->assertJsonPath('data.0.slug', 'demo-acb-4000')
            ->assertJsonPath('data.0.documents.0.url', '/documents/abcn-demo-product-sheet.pdf');

        $this->getJson('/api/v1/products/demo-ev-wallbox-22')
            ->assertOk()
            ->assertJsonPath('images.0.url', '/images/products/demo-ev-charger.webp')
            ->assertJsonCount(4, 'images')
            ->assertJsonPath('category.slug', 'demo-ac-chargers');

        $this->getJson('/api/v1/products/demo-acb-4000')
            ->assertOk()
            ->assertJsonCount(1, 'related_products')
            ->assertJsonPath('related_products.0.slug', 'demo-mccb-250');

        $lowVoltage = ProductCategory::query()->where('slug', 'demo-low-voltage')->firstOrFail();
        $filtered = $this->getJson("/api/v1/products?category={$lowVoltage->id}&filters[poles]=3p-4p&locale=hy")
            ->assertOk()
            ->assertJsonPath('total', 2)
            ->assertJsonPath('data.0.slug', 'demo-acb-4000')
            ->assertJsonPath('data.1.slug', 'demo-mccb-250');

        $this->assertContains('poles', collect($filtered->json('facets'))->pluck('key')->all());

        $this->getJson('/api/v1/products?search=wallbox')
            ->assertOk()
            ->assertJsonPath('total', 1)
            ->assertJsonPath('data.0.slug', 'demo-ev-wallbox-22');

        $this->getJson('/api/v1/products/compare?slugs[]=demo-mccb-250&slugs[]=demo-acb-4000')
            ->assertOk()
            ->assertJsonCount(2)
            ->assertJsonPath('0.slug', 'demo-mccb-250')
            ->assertJsonPath('1.slug', 'demo-acb-4000');

        $this->getJson('/api/v1/products/compare?slugs[]=demo-acb-4000')
            ->assertUnprocessable()
            ->assertJsonValidationErrors('slugs');
    }
}
