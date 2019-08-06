<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testUserIndex()
    {
        $response = $this->json('GET', '/user');
        $response
            ->assertStatus(200);
    }

    public function testUserRegister()
    {
        $data = [
            'name' => 'user test',
            'password' => 'password',
            'email'=>'usertest@gmail.com'
        ];
        $this->post('/user', $data)
            ->assertStatus(201);
            //->assertJson($data);
    }
}
