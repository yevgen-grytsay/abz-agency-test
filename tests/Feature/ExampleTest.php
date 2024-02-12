<?php

namespace Tests\Feature;

use App\Models\Position;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_not_found(): void
    {
        $response = $this->getJson('/api/users/1');

        $response->assertStatus(404);

        $response->assertJson(fn (AssertableJson $json) =>
            $json->whereAllType([
                    'success' => 'boolean',
                    'message' => 'string',
                    'fails' => 'array',
                    'fails.id.0' => 'string',
                ])
            );

        $response->dump();
    }

    public function test_get_positions(): void
    {
        $position = new Position();
        $position->name = 'Testers';
        $position->save();

        $response = $this->getJson('/api/positions');
        $response->dump();

        $response->assertStatus(200);

        $response->assertJson(fn (AssertableJson $json) =>
            $json->whereAllType([
                'success' => 'boolean',
                'positions' => 'array',
                'positions.0.id' => 'integer',
                'positions.0.name' => 'string',
            ])
        );
    }

    public function test_get_user(): void
    {
        $position = new Position();
        $position->name = 'Testers';
        $position->save();

        $user = User::factory()->create([
            'position_id' => $position->id,
        ]);

        $response = $this->getJson('/api/users/' . $user->id);

        $response->assertStatus(200);

        $response->assertJson(fn (AssertableJson $json) =>
            $json->whereAllType([
                'success' => 'boolean',
                'user' => 'array',
                'user.id' => 'integer',
            ])
        );

        $response->dump();
    }

    public function test_get_user_list(): void
    {
        $position = new Position();
        $position->name = 'Testers';
        $position->save();

        $user = User::factory()->create([
            'position_id' => $position->id,
        ]);

        $response = $this->getJson('/api/users');
        $response->dump();

        $response->assertStatus(200);

        $response->assertJson(function (AssertableJson $json) {
            $json->whereAllType([
                'success' => 'boolean',
                'users' => 'array',
                'users.0.id' => 'integer',
            ])
            ->etc();
        });


    }
}
