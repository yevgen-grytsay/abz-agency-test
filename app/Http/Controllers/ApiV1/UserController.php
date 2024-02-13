<?php

namespace App\Http\Controllers\ApiV1;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    const int USERS_PER_PAGE = 5;

    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'count' => ['nullable', 'integer', 'min:1', 'max:100'],
        ])->validate();

        $count = $validator['count'] ?? self::USERS_PER_PAGE;

        /** @var LengthAwarePaginator $paginator */
        $paginator = User::paginate($count);

        if ($paginator->isEmpty()) {
            return response()->json(
                new JsonResource([
                    'success' => false,
                    'message' => 'Page not found',
                ]),
                Response::HTTP_UNPROCESSABLE_ENTITY,
            );
        }

        return UserCollection::make($paginator)->additional([
            'success' => true,
        ]);
    }

    public function show(string $id)
    {
        $validator = Validator::make(['id' => $id], [
            'id' => ['required', 'integer'],
        ])->validate();


//        if ($validator->fails()) {
//            // ...
//        }
//        $request = new Request();
//        $validated = $request->validate([
//            'title' => 'required|unique:posts|max:255',
//            'body' => 'required',
//        ]);

        return UserResource::make(User::findOrFail($validator['id']))->additional([
            'success' => true,
        ]);
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'min:2', 'max:60'],
            'email' => ['required', 'email', 'unique:users,email'],
            'phone' => ['required', 'regex:/^\+380[0-9]{9}$/', 'unique:users,phone'],
            'position_id' => [
                'required',
                Rule::exists('positions', 'id'),
            ],
            'photo' => ['required'], // todo implement
        ]);

        $validatedData = $validator->validate();

        $user = new User($validatedData);
        $user->save();

        return new JsonResponse([
            'success' => true,
            'user_id' => $user->id,
            'message' => 'New user successfully registered',
        ]);
    }
}
