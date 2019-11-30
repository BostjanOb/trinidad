<?php

namespace App\Http\Controllers;

use App\Domain;
use App\Rules\DomainValidator;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Response;

class DomainsController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Domain::class, 'domain');
    }

    public function index(Request $request)
    {
        $servers = Domain::paginate(
            (int)$request->input('per_page', 25)
        );

        return new ResourceCollection($servers);
    }

    public function store(Request $request)
    {
        $data = $request->validate(
            [
                'domain' => ['required', new DomainValidator(),],
            ]
        );

        return new Resource(Domain::create($data));
    }

    public function show(Domain $domain)
    {
        return new Resource($domain);
    }

    public function destroy(Domain $domain)
    {
        $domain->delete();

        return \Response::make(null, Response::HTTP_NO_CONTENT);
    }
}
