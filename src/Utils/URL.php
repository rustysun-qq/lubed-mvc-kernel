<?php
namespace Lubed\MVCKernel\Utils;

final class URL {
    protected static $domain;
    protected static $main_domain;

    protected static function protocol(bool $method = FALSE):?string {
        if ($method === 'cli') {
            return NULL;
        }
        if (!empty($_SERVER['HTTPS']) AND $_SERVER['HTTPS'] === 'on') {
            return 'https';
        }
        return 'http';
    }

    public static function redirect(string $uri = '', string $status = '302') {
        $codes = [
            'refresh' => 'Refresh',
            '300'     => 'Multiple Choices',
            '301'     => 'Moved Permanently',
            '302'     => 'Found',
            '303'     => 'See Other',
            '304'     => 'Not Modified',
            '305'     => 'Use Proxy',
            '307'     => 'Temporary Redirect'
        ];
        $status = isset($codes[$status]) ? $status : '302';

        if (FALSE === strpos($uri, '://')) {
            $uri = URL::site($uri);
        }

        if ($status === 'refresh') {
            header('Refresh: 0; url=' . $uri);
        } else {
            header('HTTP/1.1 ' . $status . ' ' . $codes[$status]);
            header('Location: ' . $uri);
        }
        //TODO::.....
        $output='';
        exit('<h1>' . $status . ' - ' . $codes[$status] . '</h1>' . $output);
    }

    public static function site(string $site = '')
    {
        $schema = self::protocol() . '://';

        if (!$schema || !self::$domain || !self::$domain) {
            return '';
        }
        if (!$site) {
            return $schema . self::$main_domain . self::$domain;
        }

        return $schema . $site . self::$domain;
    }

    public static function create(string $path, $site = '', array $params = []) {
        $site_url = '';
        if ($site && is_string($site)) {
            $site_url = self::site($site);
        } else if ($site && is_array($site)) {
            $params = $site;
        }
        $url = $path;
        if ($params) {
            $url .= '?' . http_build_query($params);
        }
        return $site_url . $url;
    }

    public static function setDomain(string $domain, string $main_domain) {
        self::$domain = $domain;
        self::$main_domain = $main_domain;
    }
}
