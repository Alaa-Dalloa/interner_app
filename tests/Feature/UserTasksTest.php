<?php

namespace Tests\Feature;
use App\Models\User;
use App\Models\Group;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTasksTest extends TestCase
{
    public function testUserTasks()
    {
         $this->withoutExceptionHandling();
        for ($i = 1; $i <= 100; $i++) {
           /*$user = User::make();
             $response = $this->post('/api/auth/registerS', [
                'name' => 'User' . $i,
                'email' => 'user' . $i . '@example.com',
                'password' => 'password',
                'password_confirmation' => 'password',
                'user_type' => 'user_type',
                'group_name' => 'group_name',
            ]);
            $response->assertStatus(201);

            $response = $this->post('/api/auth/login', [
                'email' => 'user'.$i.'@example.com',
                'password' => 'password',
            ]);
            $response->assertStatus(200);*/

            $group = Group::make();
            $response = $this->post('/api/auth/AddGroup', [
                'group_name' => 'group_name'.$i,
            ]);
            $response->assertStatus(200);
        }
    }
}