<?php

namespace App\Services\Customer;

class CookieService
{
    private const DEFAULT_PATH = '/api/customer/token';
    private const DEFAULT_MAX_AGE = 63072000;  // 2 year

    /**
     * Build cookie string
     * 
     * @param string $name Cookie name 
     * @param string $value Cookie value
     * @param array $options Optional overrides
     * @return string
     */
    public static function buildCookie(
        string $name,
        string $value,
        array $options = []
    ): string {
        $isProduction = app()->environment('production');
        $isSecure = $isProduction;
        $defaults = [
            'path' => self::DEFAULT_PATH,
            'domain' => config('session.domain'),
            'max_age' => self::DEFAULT_MAX_AGE,
            'secure' => $isSecure,
            'http_only' => true,
            'same_site' => $isSecure ? 'None' : 'Lax',
            'partitioned' => $isSecure,
        ];

        // Merge with custom options
        $config = array_merge($defaults, $options);

        $expires = gmdate('D, d M Y H:i:s T', time() + $config['max_age']);

        // Build cookie parts
        $parts = [
            $name . '=' . $value,
            'Path=' . $config['path'],
            'Domain=' . $config['domain'],
            'Expires=' . $expires,
            'Max-Age=' . $config['max_age'],
        ];

        // Conditional flags
        if ($config['secure']) {
            $parts[] = 'Secure';
        }

        if ($config['http_only']) {
            $parts[] = 'HttpOnly';
        }

        if ($config['same_site']) {
            $parts[] = 'SameSite=' . $config['same_site'];
        }

        if ($config['partitioned']) {
            $parts[] = 'Partitioned';
        }

        return implode('; ', $parts);
    }

    /**
     * Set multiple cookies on response
     * 
     * @param $response Response object
     * @param array $cookies ['name' => 'value', ...]
     * @param array $options Optional overrides
     * @return Response
     */
    public static function setCookies($response, array $cookies, array $options = [])
    {
        $cookieHeaders = [];

        foreach ($cookies as $name => $value) {
            $cookieHeaders[] = self::buildCookie($name, $value, $options);
        }

        // Set multiple Set-Cookie headers
        $response->headers->set('Set-Cookie', $cookieHeaders, false);

        return $response;
    }

    /**
     * Set single cookie on response
     * 
     * @param $response Response object
     * @param string $name Cookie name
     * @param string $value Cookie value
     * @param array $options Optional overrides
     * @return Response
     */
    public static function setCookie($response, string $name, string $value, array $options = [])
    {
        $cookie = self::buildCookie($name, $value, $options);
        $response->headers->set('Set-Cookie', $cookie, false);
        return $response;
    }
}