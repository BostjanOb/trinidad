<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class SiteResourceCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data'    => $this->collection->map(function($i) { $i->addHidden('server'); return $i; }),
            'servers' => $this->collection->pluck('server')->unique()->values(),
        ];
    }
}
