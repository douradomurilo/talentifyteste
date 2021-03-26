<?php

namespace Tests\Feature;

use App\Models\Job;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class JobsControllerTest extends TestCase
{
    public function test_can_list_jobs()
    {
        $response = $this->getJson('/api/jobs');

        $response->assertStatus(200);
    }

    public function test_can_get_job()
    {
        $job = Job::factory()->create();

        $response = $this->getJson('/api/jobs/'.$job->id);

        $response->assertStatus(200);
    }

    public function test_can_create_job()
    {
        $user = User::factory()->create();
        
        $data = [
            'title' => $this->faker->sentence(),
            'description' => $this->faker->paragraph(),
            'status' => 'active',
            'address' => $this->faker->address,
            'salary' => $this->faker->randomFloat(2),
            'api_token' => $user->api_token
        ];

        $response = $this->postJson('/api/jobs', $data);

        $response->assertStatus(201);
    }

    public function test_can_update_job()
    {
        $user = User::factory()->create();        
        $job = Job::factory()->create(['user_id' => $user->id]);

        $data = [
            'title' => $job->title,
            'description' => $job->description,
            'status' => 'inactive', // alterando status
            'address' => $job->address,
            'salary' => $job->salary,
            'api_token' => $user->api_token
        ];

        $response = $this->putJson('/api/jobs/'.$job->id, $data);

        $response->assertStatus(200);
    }

    public function test_can_delete_job()
    {
        $user = User::factory()->create();
        
        $job = Job::factory()->create(['user_id' => $user->id]);

        $data = ['api_token' => $user->api_token];

        $response = $this->deleteJson('/api/jobs/'.$job->id, $data);

        $response->assertStatus(200);
    }
}
