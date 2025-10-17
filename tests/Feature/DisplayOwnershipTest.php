<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Display;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tests\TestCase;

class DisplayOwnershipTest extends TestCase
{
    use RefreshDatabase;

    protected $user1;
    protected $user2;
    protected $display1;
    protected $display2;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create two test users
        $this->user1 = User::create([
            'name' => 'Usuario Test 1',
            'email' => 'test1@example.com',
            'password' => bcrypt('password123'),
            'email_verified_at' => now(),
        ]);

        $this->user2 = User::create([
            'name' => 'Usuario Test 2',
            'email' => 'test2@example.com',
            'password' => bcrypt('password123'),
            'email_verified_at' => now(),
        ]);

        // Create displays for each user
        $this->display1 = Display::create([
            'name' => 'Display Usuario 1',
            'description' => 'Display del primer usuario',
            'price_per_day' => 100.00,
            'resolution_height' => 1080,
            'resolution_width' => 1920,
            'type' => 'indoor',
            'user_id' => $this->user1->id,
        ]);

        $this->display2 = Display::create([
            'name' => 'Display Usuario 2',
            'description' => 'Display del segundo usuario',
            'price_per_day' => 200.00,
            'resolution_height' => 2160,
            'resolution_width' => 3840,
            'type' => 'outdoor',
            'user_id' => $this->user2->id,
        ]);
    }

    public function test_user_can_only_see_their_own_displays_in_the_listing()
    {
        $token = JWTAuth::fromUser($this->user1);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/displays');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        // Verify that only sees their display
        $displays = $response->json('data');
        $this->assertCount(1, $displays);
        $this->assertEquals($this->display1->id, $displays[0]['id']);
        $this->assertEquals('Display Usuario 1', $displays[0]['name']);
    }

    public function test_user_cannot_see_displays_from_another_user()
    {
        $token = JWTAuth::fromUser($this->user1);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/displays');

        $response->assertStatus(200);

        $displays = $response->json('data');
        
        // Verify that does NOT contain the other user's display
        $displayIds = collect($displays)->pluck('id')->toArray();
        $this->assertNotContains($this->display2->id, $displayIds);
    }

    public function test_user_cannot_access_specific_display_from_another_user()
    {
        $token = JWTAuth::fromUser($this->user1);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson("/api/displays/{$this->display2->id}");

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Display not found or you do not have permission to view it'
            ]);
    }

    public function test_user_can_access_their_own_specific_display()
    {
        $token = JWTAuth::fromUser($this->user1);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson("/api/displays/{$this->display1->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $display = $response->json('data');
        $this->assertEquals($this->display1->id, $display['id']);
        $this->assertEquals('Display Usuario 1', $display['name']);
    }

    public function test_user_cannot_update_display_from_another_user()
    {
        $token = JWTAuth::fromUser($this->user1);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->putJson("/api/displays/{$this->display2->id}", [
            'name' => 'Display Modificado',
            'description' => 'Descripción modificada',
            'price_per_day' => 150.00,
            'resolution_height' => 1080,
            'resolution_width' => 1920,
            'type' => 'indoor',
        ]);

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Display not found or you do not have permission to update it'
            ]);
    }

    public function test_user_can_update_their_own_display()
    {
        $token = JWTAuth::fromUser($this->user1);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->putJson("/api/displays/{$this->display1->id}", [
            'name' => 'Display Modificado',
            'description' => 'Descripción modificada',
            'price_per_day' => 150.00,
            'resolution_height' => 1080,
            'resolution_width' => 1920,
            'type' => 'indoor',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Display updated successfully'
            ]);

        $display = $response->json('data');
        $this->assertEquals('Display Modificado', $display['name']);
        $this->assertEquals(150.00, $display['price_per_day']);
    }

    public function test_user_cannot_delete_display_from_another_user()
    {
        $token = JWTAuth::fromUser($this->user1);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->deleteJson("/api/displays/{$this->display2->id}");

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Display not found or you do not have permission to delete it'
            ]);

        // Verify that the display still exists
        $this->assertDatabaseHas('displays', [
            'id' => $this->display2->id,
            'name' => 'Display Usuario 2'
        ]);
    }

    public function test_user_can_delete_their_own_display()
    {
        $token = JWTAuth::fromUser($this->user1);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->deleteJson("/api/displays/{$this->display1->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Display deleted successfully'
            ]);

        // Verify that the display was deleted
        $this->assertDatabaseMissing('displays', [
            'id' => $this->display1->id
        ]);
    }

    public function test_new_display_is_automatically_assigned_to_authenticated_user()
    {
        $token = JWTAuth::fromUser($this->user1);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/displays', [
            'name' => 'Nuevo Display',
            'description' => 'Display creado por usuario 1',
            'price_per_day' => 300.00,
            'resolution_height' => 1440,
            'resolution_width' => 2560,
            'type' => 'outdoor',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Display created successfully'
            ]);

        $display = $response->json('data');
        $this->assertEquals('Nuevo Display', $display['name']);
        
        // Verify in database that it was assigned to the correct user
        $this->assertDatabaseHas('displays', [
            'name' => 'Nuevo Display',
            'user_id' => $this->user1->id
        ]);
    }

    public function test_endpoints_require_authentication()
    {
        // Without token
        $response = $this->getJson('/api/displays');
        $response->assertStatus(401);

        $response = $this->getJson("/api/displays/{$this->display1->id}");
        $response->assertStatus(401);

        $response = $this->postJson('/api/displays', []);
        $response->assertStatus(401);

        $response = $this->putJson("/api/displays/{$this->display1->id}", []);
        $response->assertStatus(401);

        $response = $this->deleteJson("/api/displays/{$this->display1->id}");
        $response->assertStatus(401);
    }
}
