<?php

namespace App;

class UrlResolver
{
    public function domain($url): string
    {
        return $this->getUrlComponents($url)['host'];
    }

    public function ip(string $url): string
    {
        return gethostbyname($this->getUrlComponents($url)['host']);
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
