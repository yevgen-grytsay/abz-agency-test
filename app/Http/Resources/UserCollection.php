<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Arr;

class UserCollection extends ResourceCollection
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'users';

    public function paginationInformation($request, $paginated, $default)
    {
        return [
            'links' => [
                'next_url' => Arr::get($default, 'links.next'),
                'prev_url' => Arr::get($default, 'links.prev'),
            ],
            'page' => $default['meta']['current_page'],
            'total_pages' => $default['meta']['last_page'],
            'total_users' => $default['meta']['total'],
            'count' => count($this->collection),
        ];
    }
}
