<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Display;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tests\TestCase;

class DisplayPhotoSimpleTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $display;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test user
        $this->user = User::create([
            'name' => 'Usuario Test',
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
            'email_verified_at' => now(),
        ]);

        // Create test display
        $this->display = Display::create([
            'name' => 'Display Test',
            'description' => 'Display de prueba',
            'price_per_day' => 100.00,
            'resolution_height' => 1080,
            'resolution_width' => 1920,
            'type' => 'indoor',
            'user_id' => $this->user->id,
        ]);

        // Configure storage for testing
        Storage::fake('public');
    }

    public function test_photo_validation_works()
    {
        $token = JWTAuth::fromUser($this->user);
        
        // Test with invalid file type
        $invalidFile = UploadedFile::fake()->create('document.pdf', 1000);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/displays', [
            'name' => 'Display Test',
            'description' => 'Display de prueba',
            'price_per_day' => 100.00,
            'resolution_height' => 1080,
            'resolution_width' => 1920,
            'type' => 'indoor',
            'photo' => $invalidFile,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['photo']);
    }

    public function test_photo_validation_rejects_oversized_files()
    {
        $token = JWTAuth::fromUser($this->user);
        
        // Create a file larger than 5MB
        $largeFile = UploadedFile::fake()->create('large.jpg', 6000, 'image/jpeg');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/displays', [
            'name' => 'Display Test',
            'description' => 'Display de prueba',
            'price_per_day' => 100.00,
            'resolution_height' => 1080,
            'resolution_width' => 1920,
            'type' => 'indoor',
            'photo' => $largeFile,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['photo']);
    }

    public function test_photo_upload_requires_authentication()
    {
        $photo = UploadedFile::fake()->create('test.jpg', 1000, 'image/jpeg');

        $response = $this->postJson('/api/displays', [
            'name' => 'Display Test',
            'description' => 'Display de prueba',
            'price_per_day' => 100.00,
            'resolution_height' => 1080,
            'resolution_width' => 1920,
            'type' => 'indoor',
            'photo' => $photo,
        ]);

        $response->assertStatus(401);
    }

    public function test_cannot_upload_photo_to_another_users_display()
    {
        // Create another user
        $otherUser = User::create([
            'name' => 'Otro Usuario',
            'email' => 'other@example.com',
            'password' => bcrypt('password123'),
            'email_verified_at' => now(),
        ]);

        $token = JWTAuth::fromUser($otherUser);
        $photo = UploadedFile::fake()->create('test.jpg', 1000, 'image/jpeg');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->putJson("/api/displays/{$this->display->id}", [
            'name' => 'Display Modificado',
            'photo' => $photo,
        ]);

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Display not found or you do not have permission to update it'
            ]);
    }

    public function test_display_resource_includes_photo_urls()
    {
        // Manually set photo paths to test the resource
        $this->display->update([
            'photo_path' => 'displays/1/photo_123.jpg',
            'photo_thumb_path' => 'displays/1/photo_thumb_123.jpg'
        ]);

        $token = JWTAuth::fromUser($this->user);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/displays');

        $response->assertStatus(200);
        
        $displays = $response->json('data');
        $this->assertNotEmpty($displays);
        
        $display = $displays[0];
        $this->assertArrayHasKey('photo_url', $display);
        $this->assertArrayHasKey('photo_thumb_url', $display);
        $this->assertNotNull($display['photo_url']);
        $this->assertNotNull($display['photo_thumb_url']);
    }
}
