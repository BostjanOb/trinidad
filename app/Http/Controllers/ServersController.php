<?php

namespace App\Http\Controllers;

use App\Server;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Response;

class ServersController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Server::class, 'server');
    }

    public function index(Request $request)
    {
        $servers = Server::paginate(
            (int)$request->input('per_page', 25)
        );

        return new ResourceCollection($servers);
    }

    public function store(Request $request)
    {
        $serverData = $request->validate(
            [
                'ip'   => 'required|ipv4',
                'name' => 'string|max:255',
            ]
        );

        return new Resource(Server::create($serverData));
    }

    public function show(Server $server)
    {
        return new Resource($server);
    }

    public function update(Request $request, Server $server)
    {
        $server->update(
            $request->validate(
                [
                    'name' => 'string|max:255',
                ]
            )
        );

        return new Resource($server);
    }

    public function destroy(Server $server)
    {
        $server->delete();

        return \Response::make(null, Response::HTTP_NO_CONTENT);
    }
}
