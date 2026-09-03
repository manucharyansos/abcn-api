<?php

namespace Tests\Feature;

use App\Models\ContactRequest;
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
}
