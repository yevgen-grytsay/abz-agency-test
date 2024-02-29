<?php

namespace App\Repositories;

use App\Models\Position;
use Illuminate\Database\Eloquent\Collection;

class PositionRepository
{
    public function getAll(): Collection
    {
        return Position::all();
    }
}
