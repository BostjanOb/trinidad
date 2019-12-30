<?php

namespace App\Checkers\Site;

use App\Checkers\Checker;
use App\Checkers\Exceptions\Alert;
use App\UrlResolver;
use Carbon\Carbon;

class HostingServer implements Checker
{
    /**
     * @var UrlResolver
     */
    private UrlResolver $urlResolver;

    public function __construct(UrlResolver $urlResolver)
    {
        $this->urlResolver = $urlResolver;
    }

    public function check($model, array $arguments = [])
    {
        $ip = app(UrlResolver::class)->ip($model->host);

        if ($ip !== $model->server->ip) {
            $model->associateServerFromIp($ip)
                ->save();

            throw Alert::create("Site \"{$model->name}\" moved to new server ({$model->server->name} ; ip: {$model->server->ip})")
                ->markResolved();
        }
    }

    public function nextRun(): ?Carbon
    {
        return null;
    }
}
