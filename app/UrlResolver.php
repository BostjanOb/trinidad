<?php

namespace App;

use Pdp\CurlHttpClient;
use Pdp\Manager;

class UrlResolver
{
    public function host($url): string
    {
        return $this->getUrlComponents($url)['host'];
    }

    public function ip(string $url): string
    {
        return gethostbyname($this->getUrlComponents($url)['host']);
    }

    public function domain(string $url)
    {
        $manager = new Manager(\Cache::store(), new CurlHttpClient());

        return $manager->getRules()->resolve($this->host($url))->getRegistrableDomain();
    }

    private function getUrlComponents(string $url): array
    {
        $urlComponents = parse_url($url);
        if ($urlComponents === false) {
            throw new \InvalidArgumentException('Invalid URL');
        }

        return $urlComponents;
    }
}
