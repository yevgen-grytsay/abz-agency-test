<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;

readonly class CreateUserDTO
{
    public function __construct(
        public string $name,
        public string $email,
        public int $position_id,
        public string $phone,
        public UploadedFile $photo_raw,
    ) {
    }
}
