<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IauthTest extends TestCase
{
    use RefreshDatabase ;
    //
    /**
     * A basic feature test example.
     */
    public function test_validate_form_verify_request(): void
    {
        $response = $this->post('/api/verify-request', [
            'mobile' => ''
        ]);

        $response->assertStatus(422);

        $response->assertJson([
            'status' => 'error',
        ]);
    }

    public function test_success_verify_request(): void
    {
        User::factory()->create([
            'mobile' => '9114030262'
        ]);

        $response = $this->post('/api/verify-request', [
            'mobile' => '9114030262'
        ]);

        $response->assertStatus(200);

        $response->assertJson([
            'status' => 'success',
        ]);
    }


    public function test_validation_form_verify(): void
    {
        $response = $this->post('/api/verify', [
            'mobile' => '',
            'otp_code' => '1111',
        ]);

        $response->assertStatus(422);

        $response->assertJson([
            'status' => 'error',
        ]);
    }

    public function test_success_verify(): void
    {
        $user = User::factory()->create([
            'mobile' => '9114030262'
        ]);

        $response = $this->post('/api/verify', [
            'mobile' => '9114030262',
            'otp_code' => 1111,
        ]);

        $response->assertStatus(200);

        $response->assertJson([
            'status' => 'success',
        ]);

    }

    public function test_validation_faild_update(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->patch('/api/update', [

        ]);

        $response->assertStatus(422);

        $response->assertJson([
            'status' => 'error',
        ]);
    }

    public function test_success_update(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->patch('/api/update', [
            'first_name' => 'reza',
            'last_name' => 'alavi',
        ]);

        $user = User::where('mobile', $user->mobile)->first();

        $this->assertTrue($user->first_name === 'reza');

        $response->assertStatus(200);

        $response->assertJson([
            'status' => 'success',
        ]);
    }


    public function test_get_profile(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->call('GET', '/api/profile');

        $data = json_decode($response->getContent(), true);

        $response->assertStatus(200);

        $this->assertTrue($user->first_name === $data['data']['user']['first_name']);

        $response->assertJson([
            'status' => 'success',
        ]);
    }

    public function test_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->call('GET', '/api/logout');

        $data = json_decode($response->getContent(), true);
        $response->assertStatus(200);

        $this->assertTrue('خروج با موفقیت انجام شد' === $data['message']);

        $response->assertJson([
            'status' => 'success',
        ]);
    }
}
