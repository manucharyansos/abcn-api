<?php

namespace Tests\Feature;

use App\Models\ContactRequest;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_public_inquiry_can_be_submitted(): void
    {
        $response = $this->postJson('/api/v1/contact-requests', [
            'locale' => 'hy',
            'name' => 'Test Contact',
            'company' => 'Example LLC',
            'email' => 'contact@example.com',
            'phone' => '+374 99 000000',
            'message' => 'We need an engineering consultation.',
        ]);

        $response->assertCreated()->assertJsonStructure(['message', 'id']);
        $this->assertDatabaseHas(ContactRequest::class, [
            'email' => 'contact@example.com',
            'status' => 'new',
        ]);
    }

    public function test_contact_fields_are_validated(): void
    {
        $this->postJson('/api/v1/contact-requests', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['locale', 'name', 'email', 'phone', 'message']);
    }

    public function test_a_product_quote_keeps_the_selected_product_context(): void
    {
        $product = Product::query()->create([
            'slug' => 'demo-breaker',
            'sku' => 'ABCN-DEMO-100',
            'status' => 'published',
            'featured' => false,
            'sort_order' => 1,
            'translations' => [
                'hy' => ['name' => 'Փորձնական անջատիչ'],
                'en' => ['name' => 'Demo breaker'],
            ],
        ]);

        $this->postJson('/api/v1/contact-requests', [
            'locale' => 'hy',
            'name' => 'Product Contact',
            'email' => 'product@example.com',
            'phone' => '+374 99 111111',
            'message' => 'Խնդրում եմ ուղարկել գնային առաջարկ։',
            'product_slug' => $product->slug,
            'quantity' => 24,
        ])->assertCreated();

        $this->assertDatabaseHas(ContactRequest::class, [
            'email' => 'product@example.com',
            'request_type' => 'product_quote',
            'product_id' => $product->id,
            'product_name' => 'Փորձնական անջատիչ',
            'product_sku' => 'ABCN-DEMO-100',
            'quantity' => 24,
        ]);
    }

    public function test_a_quote_cannot_reference_an_unpublished_product(): void
    {
        $product = Product::query()->create([
            'slug' => 'draft-breaker',
            'status' => 'draft',
            'featured' => false,
            'sort_order' => 1,
            'translations' => [
                'hy' => ['name' => 'Սևագիր ապրանք'],
                'en' => ['name' => 'Draft product'],
            ],
        ]);

        $this->postJson('/api/v1/contact-requests', [
            'locale' => 'hy',
            'name' => 'Product Contact',
            'email' => 'product@example.com',
            'phone' => '+374 99 111111',
            'message' => 'Խնդրում եմ ուղարկել գնային առաջարկ։',
            'product_slug' => $product->slug,
        ])->assertUnprocessable()->assertJsonValidationErrors('product_slug');
    }

    public function test_an_admin_can_filter_product_quote_requests(): void
    {
        $token = 'valid-admin-token';
        User::factory()->create([
            'role' => 'admin',
            'api_token' => hash('sha256', $token),
        ]);

        ContactRequest::query()->create([
            'locale' => 'hy',
            'request_type' => 'general',
            'name' => 'General Contact',
            'email' => 'general@example.com',
            'phone' => '+374 99 222222',
            'message' => 'Ընդհանուր հարցում։',
            'status' => 'new',
        ]);
        ContactRequest::query()->create([
            'locale' => 'hy',
            'request_type' => 'product_quote',
            'product_name' => 'Փորձնական անջատիչ',
            'product_sku' => 'ABCN-DEMO-100',
            'name' => 'Quote Contact',
            'email' => 'quote@example.com',
            'phone' => '+374 99 333333',
            'message' => 'Գնային առաջարկ։',
            'status' => 'new',
        ]);

        $this->withToken($token)
            ->getJson('/api/v1/admin/contact-requests?type=product_quote')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.product_sku', 'ABCN-DEMO-100');
    }
}
