<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class UsersControllerTest extends TestCase
{
    public function test_can_list_users()
    {
        $user = User::factory()->create();

        $response = $this->getJson('/api/users?api_token='.$user->api_token);

        $response->assertStatus(200);
    }

    public function test_can_get_user()
    {
        $user = User::factory()->create();

        $response = $this->getJson('/api/users/'.$user->id.'?api_token='.$user->api_token);

        $response->assertStatus(200);
    }

    public function test_can_create_user()
    {
        $company = Company::factory()->create();
        $password = Hash::make('password');

        $data = [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => $password,
            'password_confirmation' => $password,
            'api_token' => Str::random(60),
            'company_id' => $company->id
        ];

        $response = $this->postJson('/api/users', $data);

        $response->assertStatus(201);
    }

    public function test_can_update_user()
    {
        $user = User::factory()->create();
        $company = Company::factory()->create();

        $data = [
            'name' => $user->name,
            'email' => $this->faker->unique()->safeEmail, // alterando e-mail
            'password' => $user->password,
            'password_confirmation' => $user->password,
            'api_token' => $user->api_token,
            'company_id' => $company->id
        ];

        $response = $this->putJson('/api/users/'.$user->id, $data);

        $response->assertStatus(200);
    }

    public function test_can_delete_user()
    {
        $user = User::factory()->create();

        $response = $this->deleteJson('/api/users/'.$user->id.'?api_token='.$user->api_token);

        $response->assertStatus(200);
    }
}
