<?php

declare(strict_types=1);

namespace Framework\Pagination;

trait UrlParamsTrait
{
    private function addUrlParams(string $url, array $params = [], bool $removeEmpty = false): string
    {
        $parsedUrl = \parse_url($url);
        if (!isset($parsedUrl['query'])) {
            $parsedUrl['query'] = '';
        }
        parse_str($parsedUrl['query'], $parsedParams);
        $params = array_merge($parsedParams, $params);
        if ($removeEmpty) {
            foreach ($params as $key => $param) {
                if (strval($param) === '') {
                    unset ($params[$key]);
                }
            }
        }
        $parsedUrl['query'] = \http_build_query($params);
        $result = $this->buildUrl($parsedUrl);
        return $result;
    }

    private function buildUrl(array $u): string
    {
        $result = '';
        if (!empty($u['host'])) {
            $result = $u['host'];
            if (!empty($u['user']) && !empty($u['pass'])) {
                $result = $u['user'] . ':' . $u['pass'] . '@' . $result;
            }
            if (!empty($u['scheme'])) {
                $result = $u['scheme'] . '://' . $result;
            }
        }
        if (!empty($u['path'])) {
            $result .= $u['path'];
        }
        if (!empty($u['query'])) {
            $result .= '?' . $u['query'];
        }
        if (!empty($u['anchor'])) {
            $result .= '#' . $u['anchor'];
        }
        return $result;
    }
}
