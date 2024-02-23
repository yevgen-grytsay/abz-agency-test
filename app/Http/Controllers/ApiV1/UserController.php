<?php

namespace App\Http\Controllers\ApiV1;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateUserRequest;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\Jobs\TestJob;
use App\Jobs\TestJob2;
use App\Models\Position;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    const int USERS_PER_PAGE = 5;

    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'count' => ['nullable', 'integer', 'min:1', 'max:100'],
            'offset' => ['nullable', 'integer', 'min:0'],
            'page' => ['nullable', 'integer', 'min:1'],
        ])->validate();

        $count = $validator['count'] ?? self::USERS_PER_PAGE;
        $offset = $validator['offset'] ?? null;

        if ($offset !== null) {
            $userList = User::with('position')
                ->orderByDesc('id')
                ->offset($offset)
                ->limit($count)
                ->get();

            return UserCollection::make($userList)->additional([
                'success' => true,
            ]);
        }

        /** @var LengthAwarePaginator $paginator */
        $paginator = User::with('position')
            ->orderByDesc('id')
            ->paginate($count);

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

    public function create(CreateUserRequest $request)
    {
        $validatedData = $request->validated();

        $user = new User();
        $user->name = $validatedData['name'];
        $user->email = $validatedData['email'];
        $user->position()->associate(
            Position::query()
                ->findOrFail($validatedData['position_id'])
        );
        $user->phone = $validatedData['phone'];
        $photoPath = $request->photo_raw->store('photos');
//        $user->photo = $photoPath;
        $user->photo = 'empty';
        $user->save();

        TestJob::dispatch($user, $photoPath);

        return new JsonResponse([
            'success' => true,
            'user_id' => $user->id,
            'message' => 'New user successfully registered',
        ]);
    }
}
