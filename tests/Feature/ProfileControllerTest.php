<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProfileControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_requires_auth()
    {
        $this->get('/profil')->assertRedirect('/login');
    }

    public function test_authenticated_user_can_open_profile_page()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
             ->get('/profil')
             ->assertStatus(200);
    }

    public function test_user_can_update_profile()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->post('/profil/update', [
                'name' => 'Updated User',
                'email' => 'updated@example.com',
            ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated User',
            'email' => 'updated@example.com',
        ]);

        $this->assertTrue(in_array($response->status(), [200, 302]));
    }
}
