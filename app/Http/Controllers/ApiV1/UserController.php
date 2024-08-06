<?php

namespace App\Http\Controllers\ApiV1;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    const int USERS_PER_PAGE = 5;

    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

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
            $userList = $this->userRepository->getUsersForApiWithOffset($offset, $count);

            return UserCollection::make($userList)->additional([
                'success' => true,
            ]);
        }

        $paginator = $this->userRepository->getUsersForApi($count);
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

        $user = $this->userRepository->getById($validator['id']);
        return UserResource::make($user)->additional([
            'success' => true,
        ]);
    }
}
