<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AboutTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function about_endpoint_returns_correct_information()
    {
        $response = $this->getJson('/api/about');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'app_name',
                     'version',
                     'description',
                     'team',
                     'repository',
                     'contact_email'
                 ]);

        $data = $response->json();
        
        $this->assertEquals('Task Management System', $data['app_name']);
        $this->assertEquals('1.0.0', $data['version']);
        $this->assertStringContainsString('Laravel', $data['description']);
        $this->assertStringContainsString('React', $data['description']);
        $this->assertEquals('https://github.com/jabatayo/task-management', $data['repository']);
        $this->assertEquals('jabatayo@gmail.com', $data['contact_email']);
    }

    /** @test */
    public function about_endpoint_returns_team_information()
    {
        $response = $this->getJson('/api/about');

        $response->assertStatus(200);

        $team = $response->json('team');
        $this->assertIsArray($team);
        $this->assertNotEmpty($team);
        
        $firstMember = $team[0];
        $this->assertArrayHasKey('name', $firstMember);
        $this->assertArrayHasKey('role', $firstMember);
        $this->assertEquals('Jonathan', $firstMember['name']);
        $this->assertEquals('Lead Developer', $firstMember['role']);
    }

    /** @test */
    public function about_endpoint_does_not_require_authentication()
    {
        $response = $this->getJson('/api/about');

        $response->assertStatus(200);
    }

    /** @test */
    public function about_endpoint_returns_consistent_data()
    {
        $response1 = $this->getJson('/api/about');
        $response2 = $this->getJson('/api/about');

        $this->assertEquals($response1->json(), $response2->json());
    }

    /** @test */
    public function about_endpoint_handles_multiple_requests()
    {
        // Make multiple requests to ensure consistency
        for ($i = 0; $i < 5; $i++) {
            $response = $this->getJson('/api/about');
            $response->assertStatus(200);
            
            $data = $response->json();
            $this->assertEquals('Task Management System', $data['app_name']);
            $this->assertEquals('1.0.0', $data['version']);
        }
    }

    /** @test */
    public function about_endpoint_returns_valid_json()
    {
        $response = $this->getJson('/api/about');

        $response->assertStatus(200);
        
        // Verify the response is valid JSON
        $this->assertIsArray($response->json());
        
        // Verify all required fields are present and not null
        $data = $response->json();
        $this->assertNotNull($data['app_name']);
        $this->assertNotNull($data['version']);
        $this->assertNotNull($data['description']);
        $this->assertNotNull($data['team']);
        $this->assertNotNull($data['repository']);
        $this->assertNotNull($data['contact_email']);
    }
} 