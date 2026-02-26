<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProjectApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_list_own_projects(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();

        Sanctum::actingAs($user);
        $response = $this
            ->getJson('/api/projects');

        $response->assertStatus(200);
        $response->assertJsonCount(0);

        $user->projects()->create(['name' => 'My Project', 'description' => 'Desc']);
        $other->projects()->create(['name' => 'Other Project']);

        Sanctum::actingAs($user);
        $response2 = $this
            ->getJson('/api/projects');

        $response2->assertStatus(200);
        $response2->assertJsonCount(1);
        $response2->assertJsonPath('0.name', 'My Project');
    }

    public function test_authenticated_user_can_create_project(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);
        $response = $this->postJson('/api/projects', [
                'name' => 'New Project',
                'description' => 'A description',
            ]);

        $response->assertStatus(201);
        $response->assertJsonPath('name', 'New Project');
        $response->assertJsonPath('description', 'A description');
        $this->assertDatabaseHas('projects', [
            'user_id' => $user->id,
            'name' => 'New Project',
        ]);
    }

    public function test_unauthenticated_user_cannot_list_projects(): void
    {
        $response = $this->getJson('/api/projects');
        $response->assertStatus(401);
    }
}
