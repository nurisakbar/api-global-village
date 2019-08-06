<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testExample()
    {
        $this->assertTrue(true);
    }

    public function user_can_register() {
        $data = [
            'name' => 'user test',
            'email' => 'test@gmail.com',
            'password'=>'password'
        ];
        
        $this->post(route('user'), $data)
            ->assertStatus(201)
            ->assertJson($data);
    }


}
