<?php

namespace App\Http\Controllers;

use App\Http\Resources\SiteResourceCollection;
use App\Server;
use App\Site;
use App\UrlResolver;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Http\Response;

class SitesController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('index', Site::class);

        $sites = Site::with('server')
            ->when($request->has('server'), function (Builder $query) use ($request) {
                return $query->where('server_id', $request->input('server'));
            })
            ->paginate(
                (int)$request->input('per_page', 25)
            );

        return new SiteResourceCollection($sites);
    }

    public function store(Request $request, UrlResolver $urlResolver)
    {
        $this->authorize('create', Site::class);

        $request->validate([
            'url'  => 'required|url',
            'name' => 'string|max:255',
        ]);

        $server = Server::firstOrCreate(['ip' => $urlResolver->ip($request->input('url'))]);

        $domain = $urlResolver->domain($request->input('url'));
        $site = Site::make([
            'name'   => $request->input('name', $domain),
            'domain' => $domain,
        ]);
        $site->server()->associate($server);
        $site->save();

        return new Resource($site);
    }

    public function show(Site $site)
    {
        $this->authorize('view', $site);

        $site->load('server');

        return new Resource($site);
    }

    public function update(Request $request, Site $site)
    {
        $this->authorize('update', $site);

        $site->update($request->validate(['name' => 'string|max:255']));

        return new Resource($site);
    }

    public function destroy(Site $site)
    {
        $this->authorize('delete', $site);

        $site->delete();

        return \Response::make(null, Response::HTTP_NO_CONTENT);
    }
}