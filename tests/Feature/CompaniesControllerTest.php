<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CompaniesControllerTest extends TestCase
{
    public function test_can_list_companies()
    {
        $user = User::factory()->create();

        $response = $this->get('/api/companies?api_token='.$user->api_token);

        $response->assertStatus(200);
    }

    public function test_can_get_company()
    {
        $user = User::factory()->create();
        $company = Company::factory()->create();

        $response = $this->get('/api/companies/'.$company->id.'?api_token='.$user->api_token);

        $response->assertStatus(200);
    }

    public function test_can_create_company()
    {
        $user = User::factory()->create();

        $data = [
            'name' => $this->faker->company,
            'api_token' => $user->api_token
        ];

        $response = $this->post('/api/companies', $data);

        $response->assertStatus(201);
    }

    public function test_can_update_company()
    {
        $user = User::factory()->create();
        $company = Company::factory()->create();

        $data = [
            'name' => $this->faker->company,
            'api_token' => $user->api_token
        ];

        $response = $this->putJson('/api/companies/'.$company->id, $data);

        $response->assertStatus(200);
    }

    public function test_can_delete_company()
    {
        $user = User::factory()->create();
        $company = Company::factory()->create();

        $response = $this->deleteJson('/api/companies/'.$company->id.'?api_token='.$user->api_token);

        $response->assertStatus(200);
    }
}
