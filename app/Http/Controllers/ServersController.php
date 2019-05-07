<?php

namespace App\Http\Controllers;

use App\Server;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Response;

class ServersController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('index', Server::class);

        $servers = Server::paginate(
            (int)$request->input('per_page', 25)
        );

        return new ResourceCollection($servers);
    }

    public function store(Request $request)
    {
        $this->authorize('create', Server::class);

        $serverData = $request->validate([
            'ip'   => 'required|ipv4',
            'name' => 'string|max:255',
        ]);

        return new Resource(Server::create($serverData));
    }

    public function show(Server $server)
    {
        $this->authorize('view', $server);

        return new Resource($server);
    }

    public function update(Request $request, Server $server)
    {
        $this->authorize('update', $server);

        $server->update($request->validate([
            'name' => 'string|max:255',
        ]));

        return new Resource($server);
    }

    public function destroy(Server $server)
    {
        $this->authorize('delete', $server);

        $server->delete();

        return \Response::make(null, Response::HTTP_NO_CONTENT);
    }
}
