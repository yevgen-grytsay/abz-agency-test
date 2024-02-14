<?php

namespace Tests\Feature;

use App\Models\Position;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class ExampleTest extends TestCase
{
//    use RefreshDatabase;

    public function test_user_not_found(): void
    {
        $response = $this->getJson('/api/v1/users/1');
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

        $response = $this->getJson('/api/v1/positions');
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

        $response = $this->getJson('/api/v1/users/' . $user->id);

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

        $response = $this->getJson('/api/v1/users');
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

    public function test_get_user_list_with_offset(): void
    {
        $position = new Position();
        $position->name = 'Testers';
        $position->save();


        $usersCount = User::count();
        if ($usersCount < 10) {
            User::factory()
                ->count(10 - $usersCount)
                ->create([
                    'position_id' => $position->id,
                ]);
        }

        $response = $this->getJson('/api/v1/users?offset=1&count=9');
        $response->dump();

        $response->assertStatus(200);

        $users = User::orderByDesc('id')
            ->take(10)
            ->get();

        $response->assertJson(function (AssertableJson $json) use ($users) {
            $json->has('users', 9);
            $json->whereAllType([
                'success' => 'boolean',
                'users' => 'array',
                'users.0.id' => 'integer',
            ]);
            $json->where('users.0.id', $users[1]->id);
            $json->where('users.8.id', $users[9]->id);
        });
    }

    public function test_create_user(): void
    {
        $position = new Position();
        $position->name = 'Testers ' . fake()->unique()->name;
        $position->save();

//        Storage::fake('photos');

        $response = $this->post('/api/v1/users', [
            'name' => fake()->firstName(),
            'email' => fake()->unique()->email(),
            'position_id' => $position->id,
            'phone' => '+380' . substr(time(), 1, 9), // todo posible conflicts
            'photo_raw' => UploadedFile::fake()->image('photo1.jpg', 70, 70),
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

        $response = $this->getJson('/api/v1/users/'. $response['user_id']);
        $response->dump();
    }

    public function test_can_not_create_user(): void
    {
//        Storage::fake('photos');

        $response = $this->postJson('/api/v1/users', [
            'name' => 'a',
            'email' => 'a',
            'position_id' => 0,
            'phone' => '+380',
            'photo_raw' => UploadedFile::fake()->image('photo1.jpg', 69, 70),
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
                    'photo_raw' => ['The photo raw field has invalid image dimensions.'],
                ])
            ;
        });

    }
}
