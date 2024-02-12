<?php

namespace App\Http\Controllers;

use App\Http\Resources\PositionCollection;
use App\Models\Position;
use Illuminate\Http\Resources\Json\JsonResource;
use Symfony\Component\HttpFoundation\Response;

class PositionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $positionCollection = Position::all();
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
