<?php

namespace Luracast\Restler;

use JsonSerializable;
use Luracast\Restler\Contracts\UserIdentificationInterface;
use Psr\Http\Message\ServerRequestInterface;

class UserIdentifier implements UserIdentificationInterface, JsonSerializable
{
    public const HEADERS_NGINX = [
        'x-real-ip',
        'forwarded',
        'x-forwarded-for',
        'x-forwarded',
        'x-cluster-client-ip',
        'client-ip',
    ];
    public const HEADERS_CLOUDFLARE = [
        'cf-connecting-ip',
        'true-client-ip',
        'forwarded',
        'x-forwarded-for',
        'x-forwarded',
        'x-cluster-client-ip',
        'client-ip',
    ];

    public const HEADERS_COMMON = [
        'client-ip',
        'x-forwarded-for',
        'x-forwarded',
        'x-cluster-client-ip',
        'cf-connecting-ip',
    ];

    public const BROWSERS = [
        'MSIE' => 'Internet Explorer',
        'Trident' => 'Internet Explorer',
        'Edge' => 'Microsoft Edge',
        'EdgA' => 'Microsoft Edge',
        'Firefox' => 'Firefox',
        'Opera Mini' => 'Opera Mini',
        'Opera' => 'Opera',
        'OPR' => 'Opera',
        'Chrome' => 'Chrome',
        'CriOS' => 'Chrome',
        'Mobile Safari' => 'Mobile Safari',
        'Safari' => 'Safari',
        'Googlebot' => 'Googlebot',
    ];

    public const PLATFORMS = [
        'Intel Mac' => 'Intel Mac',
        'Macintosh' => 'Macintosh',
        'iPhone' => 'iPhone',
        'iPad' => 'iPad',
        'Android' => 'Android',
        'Linux' => 'Linux',
        'Windows' => 'Windows',
        'CrOS' => 'Chrome OS',
        'Googlebot' => 'Googlebot',
    ];

    public static array $headersToInspect = self::HEADERS_COMMON;
    public static array $attributesToInspect = ['client_ip', 'ip'];
    protected ?string $id = null;
    protected ?string $cacheId = null;
    protected ?string $ipAddress;
    protected string $browser = 'Unknown';
    protected string $platform = 'Unknown';
    private \Psr\Http\Message\ServerRequestInterface $request;

    public function __construct(ServerRequestInterface $request)
    {
        $this->request = $request;
        $this->ipAddress = $this->getIpAddress();
        if ($agent = $this->request->getHeaderLine('user-agent')) {
            foreach (self::BROWSERS as $name => $value) {
                if (false !== strpos($agent, $name)) {
                    $this->browser = $value;
                    break;
                }
            }
            foreach (self::PLATFORMS as $name => $value) {
                if (false !== strpos($agent, $name)) {
                    $this->platform = $value;
                    break;
                }
            }
        }
    }

    public function getIpAddress(bool $ignoreProxies = false): string
    {
        foreach (static::$attributesToInspect as $attribute) {
            if ($ip = $this->request->getAttribute($attribute, false)) {
                return $ip;
            }
        }
        if (!$ignoreProxies) {
            foreach (static::$headersToInspect as $header) {
                if ($ips = $this->request->getHeaderLine($header)) {
                    if ($ip = $this->filterIP($ips)) {
                        return $ip;
                    }
                }
            }
        }
        $server = $this->request->getServerParams();
        if ($ips = $server['REMOTE_ADDR'] ?? []) {
            if ($ip = $this->filterIP($ips, false)) {
                return $ip;
            }
        }
        return '127.0.0.1';
    }

    private function filterIP(string $ips, bool $denyPrivateAndLocal = true): string
    {
        $options = FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6;
        if ($denyPrivateAndLocal) {
            $options |= FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE;
        }
        foreach (explode(',', $ips) as $ip) {
            $ip = trim($ip); // just to be safe
            if (false !== ($result = filter_var($ip, FILTER_VALIDATE_IP, $options))) {
                return $ip;
            }
        }
        return '';
    }

    /**
     * Authentication classes should call this method
     *
     * @param string $id user id as identified by the authentication classes
     *
     * @return void
     */
    public function setUniqueIdentifier(string $id): void
    {
        $this->id = $id;
    }

    /**
     * User identity to be used for caching purpose
     *
     * When the dynamic cache service places an object in the cache, it needs to
     * label it with a unique identifying string known as a cache ID. This
     * method gives that identifier
     *
     * @return string
     */
    public function getCacheIdentifier(): string
    {
        return $this->cacheId ?: $this->getUniqueIdentifier();
    }

    public function getUniqueIdentifier(bool $includePlatform = false): string
    {
        return $this->id ?: base64_encode('ip:' . $this->ipAddress . ($includePlatform ? '-' . $this->platform : ''));
    }

    /**
     * User identity for caching purpose
     *
     * In a role based access control system this will be based on role
     *
     * @param string $id
     *
     * @return void
     */
    public function setCacheIdentifier(string $id): void
    {
        $this->cacheId = $id;
    }

    public function getPlatform(): ?string
    {
        return $this->platform;
    }

    public function getBrowser(): ?string
    {
        return $this->browser;
    }

    public function jsonSerialize()
    {
        $arr = get_object_vars($this);
        if (empty($arr['id'])) {
            $arr['id'] = $this->getUniqueIdentifier();
        }
        if (empty($arr['cacheId'])) {
            $arr['cacheId'] = $arr['id'];
        }
        unset($arr['request']);
        return $arr;
    }
}
