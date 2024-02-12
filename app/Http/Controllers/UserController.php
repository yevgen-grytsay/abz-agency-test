<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    const int USERS_PER_PAGE = 5;

    public function index()
    {
        /** @var LengthAwarePaginator $paginator */
        $paginator = User::paginate(self::USERS_PER_PAGE);

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
}
