<?php

namespace Tests\Feature;

use App\Models\Position;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class ExampleTest extends TestCase
{
//    use RefreshDatabase;

    public function test_user_not_found(): void
    {
        $response = $this->getJson('/api/users/1');
        $response->dump();

        $response->assertStatus(404);

        $response->assertJson(fn (AssertableJson $json) =>
            $json->whereAllType([
                    'success' => 'boolean',
                    'message' => 'string',
                    'fails' => 'array',
                    'fails.id.0' => 'string',
                ])
            );

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

    public function test_create_user(): void
    {
        $position = new Position();
        $position->name = 'Testers ' . fake()->unique()->name;
        $position->save();

        $response = $this->postJson('/api/users', [
            'name' => fake()->firstName(),
            'email' => fake()->unique()->email(),
            'position_id' => $position->id,
            'phone' => '+380' . fake()->unique()->randomNumber(9),
            'photo' => fake()->imageUrl(),
        ]);

        $response->dump();

        $response->assertStatus(200);
        $response->assertJson(function (AssertableJson $json) {
            $json->whereAllType([
                'success' => 'boolean',
                'user_id' => 'integer',
                'message' => 'string',
            ]);
            $json
                ->where('success', true)
                ->where('message', 'New user successfully registered')
                ->etc();
        });
    }

    public function test_can_not_create_user(): void
    {
        $response = $this->postJson('/api/users', [
            'name' => 'a',
            'email' => 'a',
            'position_id' => 0,
            'phone' => '+380',
            'photo' => 'a',
        ]);

        $response->dump();

        $response->assertStatus(422);
        $response->assertJson(function (AssertableJson $json) {
            $json->whereAllType([
                'success' => 'boolean',
                'message' => 'string',
                'fails' => 'array',
            ]);
            $json
                ->where('success', false)
                ->where('message', 'Validation failed')
                ->where('fails', [
                    'name' => ['The name field must be at least 2 characters.'],
                    'email' => ['The email field must be a valid email address.'],
                    'phone' => ['The phone field format is invalid.'],
                    'position_id' => ['The selected position id is invalid.'],
                ])
            ;
        });

    }
}
