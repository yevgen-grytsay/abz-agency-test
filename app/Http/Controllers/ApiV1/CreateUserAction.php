<?php

namespace App\Http\Controllers\ApiV1;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateUserRequest;
use App\Services\CreateUserDTO;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;

class CreateUserAction extends Controller
{
    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function __invoke(CreateUserRequest $request): JsonResponse
    {
        $validatedData = $request->validated();

        $user = $this->userService->createUser(new CreateUserDTO(
            $validatedData['name'],
            $validatedData['email'],
            $validatedData['position_id'],
            $validatedData['phone'],
            $validatedData['photo_raw'],
        ));

        return new JsonResponse([
            'success' => true,
            'user_id' => $user->id,
            'message' => 'New user successfully registered',
        ]);
    }
}
