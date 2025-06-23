<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Contact;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class ContactTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $token;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create roles
        Role::create(['name' => 'Administrator']);
        Role::create(['name' => 'Regular User']);

        // Create user
        $this->user = User::factory()->create();
        $this->user->assignRole('Regular User');

        // Create token
        $this->token = $this->user->createToken('test-token')->plainTextToken;
    }

    /** @test */
    public function user_can_submit_contact_form()
    {
        $contactData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'message' => 'This is a test message for the contact form.'
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/contact', $contactData);

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Thank you for contacting us! We will get back to you soon.']);

        $this->assertDatabaseHas('contacts', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'message' => 'This is a test message for the contact form.',
            'user_id' => $this->user->id
        ]);
    }

    /** @test */
    public function contact_form_requires_name()
    {
        $contactData = [
            'email' => 'john@example.com',
            'message' => 'This is a test message.'
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/contact', $contactData);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['name']);
    }

    /** @test */
    public function contact_form_requires_email()
    {
        $contactData = [
            'name' => 'John Doe',
            'message' => 'This is a test message.'
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/contact', $contactData);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['email']);
    }

    /** @test */
    public function contact_form_requires_message()
    {
        $contactData = [
            'name' => 'John Doe',
            'email' => 'john@example.com'
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/contact', $contactData);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['message']);
    }

    /** @test */
    public function contact_form_validates_email_format()
    {
        $contactData = [
            'name' => 'John Doe',
            'email' => 'invalid-email',
            'message' => 'This is a test message.'
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/contact', $contactData);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['email']);
    }

    /** @test */
    public function contact_form_validates_name_length()
    {
        $contactData = [
            'name' => str_repeat('a', 256), // Exceeds 255 character limit
            'email' => 'john@example.com',
            'message' => 'This is a test message.'
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/contact', $contactData);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['name']);
    }

    /** @test */
    public function contact_form_validates_email_length()
    {
        $contactData = [
            'name' => 'John Doe',
            'email' => str_repeat('a', 250) . '@example.com', // Exceeds 255 character limit
            'message' => 'This is a test message.'
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/contact', $contactData);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['email']);
    }

    /** @test */
    public function contact_form_validates_message_length()
    {
        $contactData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'message' => str_repeat('a', 2001) // Exceeds 2000 character limit
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/contact', $contactData);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['message']);
    }

    /** @test */
    public function contact_form_requires_authentication()
    {
        $contactData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'message' => 'This is a test message.'
        ];

        $response = $this->postJson('/api/contact', $contactData);

        $response->assertStatus(401)
                 ->assertJson(['message' => 'Unauthenticated.']);
    }

    /** @test */
    public function rate_limiting_is_enforced_on_contact_form()
    {
        $contactData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'message' => 'This is a test message.'
        ];

        // Make 11 requests (exceeding the 10 per minute limit)
        for ($i = 0; $i < 11; $i++) {
            $response = $this->withHeaders([
                'Authorization' => 'Bearer ' . $this->token,
            ])->postJson('/api/contact', $contactData);
        }

        $response->assertStatus(429)
                 ->assertJson(['message' => 'Too Many Attempts.']);
    }

    /** @test */
    public function contact_form_stores_user_id_correctly()
    {
        $contactData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'message' => 'This is a test message.'
        ];

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/contact', $contactData);

        $this->assertDatabaseHas('contacts', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'user_id' => $this->user->id
        ]);
    }

    /** @test */
    public function contact_form_handles_special_characters()
    {
        $contactData = [
            'name' => 'José María O\'Connor',
            'email' => 'jose.maria@example.com',
            'message' => 'This message contains special characters: áéíóú ñ & < > " \''
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/contact', $contactData);

        $response->assertStatus(200);

        $this->assertDatabaseHas('contacts', [
            'name' => 'José María O\'Connor',
            'email' => 'jose.maria@example.com',
            'message' => 'This message contains special characters: áéíóú ñ & < > " \''
        ]);
    }

    /** @test */
    public function contact_form_handles_long_messages()
    {
        $contactData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'message' => str_repeat('This is a long message. ', 50) // 1000 characters
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/contact', $contactData);

        $response->assertStatus(200);

        $this->assertDatabaseHas('contacts', [
            'name' => 'John Doe',
            'email' => 'john@example.com'
        ]);
    }

    /** @test */
    public function contact_form_handles_empty_strings()
    {
        $contactData = [
            'name' => '',
            'email' => '',
            'message' => ''
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/contact', $contactData);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['name', 'email', 'message']);
    }

    /** @test */
    public function contact_form_handles_whitespace_only()
    {
        $contactData = [
            'name' => '   ',
            'email' => '   ',
            'message' => '   '
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/contact', $contactData);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['name', 'email', 'message']);
    }

    /** @test */
    public function contact_form_accepts_valid_email_formats()
    {
        $validEmails = [
            'test@example.com',
            'test.name@example.com',
            'test+name@example.com',
            'test@subdomain.example.com',
            'test@example.co.uk'
        ];

        foreach ($validEmails as $email) {
            $contactData = [
                'name' => 'John Doe',
                'email' => $email,
                'message' => 'This is a test message.'
            ];

            $response = $this->withHeaders([
                'Authorization' => 'Bearer ' . $this->token,
            ])->postJson('/api/contact', $contactData);

            $response->assertStatus(200);
        }
    }

    /** @test */
    public function contact_form_rejects_invalid_email_formats()
    {
        $invalidEmails = [
            'invalid-email',
            '@example.com',
            'test@',
            'test..test@example.com',
            'test@.com'
        ];

        foreach ($invalidEmails as $email) {
            $contactData = [
                'name' => 'John Doe',
                'email' => $email,
                'message' => 'This is a test message.'
            ];

            $response = $this->withHeaders([
                'Authorization' => 'Bearer ' . $this->token,
            ])->postJson('/api/contact', $contactData);

            $response->assertStatus(422)
                     ->assertJsonValidationErrors(['email']);
        }
    }

    /** @test */
    public function contact_form_creates_multiple_entries()
    {
        $contactData1 = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'message' => 'First message.'
        ];

        $contactData2 = [
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'message' => 'Second message.'
        ];

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/contact', $contactData1);

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/contact', $contactData2);

        $this->assertDatabaseCount('contacts', 2);
        $this->assertDatabaseHas('contacts', [
            'name' => 'John Doe',
            'email' => 'john@example.com'
        ]);
        $this->assertDatabaseHas('contacts', [
            'name' => 'Jane Smith',
            'email' => 'jane@example.com'
        ]);
    }
} 