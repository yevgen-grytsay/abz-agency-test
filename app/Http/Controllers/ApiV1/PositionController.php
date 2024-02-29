<?php

namespace App\Http\Controllers\ApiV1;

use App\Http\Controllers\Controller;
use App\Http\Resources\PositionCollection;
use App\Repositories\PositionRepository;
use Illuminate\Http\Resources\Json\JsonResource;
use Symfony\Component\HttpFoundation\Response;

class PositionController extends Controller
{
    private PositionRepository $positionRepository;

    public function __construct(PositionRepository $positionRepository)
    {
        $this->positionRepository = $positionRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $positionCollection = $this->positionRepository->getAll();
        if ($positionCollection->isEmpty()) {
            return response()->json(
                new JsonResource([
                    'success' => false,
                    'message' => 'Positions not found',
                ]),
                Response::HTTP_UNPROCESSABLE_ENTITY,
            );
        }

        return PositionCollection::make($positionCollection)->additional([
            'success' => true,
        ]);
    }
}
