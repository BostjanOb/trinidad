<?php

namespace App\Http\Controllers;

use App\Domain;
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
    public function __construct()
    {
        $this->authorizeResource(Site::class, 'site');
    }

    public function index(Request $request)
    {
        $sites = Site::with('server')
            ->when($request->has('server'), function (Builder $query) use ($request) {
                return $query->where('server_id', $request->input('server'));
            })
            ->paginate(
                (int) $request->input('per_page', 25)
            );

        return new SiteResourceCollection($sites);
    }

    public function store(Request $request, UrlResolver $urlResolver)
    {
        $request->validate([
            'url'  => 'required|url',
            'name' => 'string|max:255',
        ]);

        $domain = $urlResolver->domain($request->input('url'));
        $host = $urlResolver->host($request->input('url'));
        $ip = $urlResolver->ip($request->input('url'));

        $server = Server::firstOrCreate(['ip' => $ip]);
        $domain = Domain::firstOrCreate(['domain' => $domain]);

        $site = Site::make([
            'name' => $request->input('name', $host),
            'host' => $host,
        ]);
        $site->server()->associate($server);
        $site->domain()->associate($domain);
        $site->save();

        return new Resource($site);
    }

    public function show(Site $site)
    {
        $site->load('server');

        return new Resource($site);
    }

    public function update(Request $request, Site $site)
    {
        $site->update($request->validate(['name' => 'string|max:255']));

        return new Resource($site);
    }

    public function destroy(Site $site)
    {
        $site->delete();

        return \Response::make(null, Response::HTTP_NO_CONTENT);
    }
}
