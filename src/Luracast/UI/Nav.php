<?php

namespace Luracast\Restler\UI;

use Luracast\Restler\Core;
use Luracast\Restler\Data\Route;
use Luracast\Restler\Restler;
use Luracast\Restler\Routes;
use Luracast\Restler\Utils\Text;
use Psr\Http\Message\ServerRequestInterface;


/**
 * Utility class for automatically creating data to build an navigation interface
 * based on available routes that are accessible by the current user
 *
 * @category   Framework
 * @package    Restler
 * @author     R.Arul Kumaran <arul@luracast.com>
 */
class Nav
{
    public static string $root = 'home';
    public static $style;
    /**
     * @var array all paths beginning with any of the following will be excluded
     * from documentation. if an empty string is given it will exclude the root
     */
    public static array $excludedPaths = [''];
    /**
     * @var array prefix additional menu items with one of the following syntax
     *            [$path => $text]
     *            [$path]
     *            [$path => ['text' => $text, 'url' => $url, 'trail'=> $trail]]
     */
    public static array $prepends = [];
    /**
     * @var array suffix additional menu items with one of the following syntax
     *            [$path => $text]
     *            [$path]
     *            [$path => ['text' => $text, 'url' => $url, 'trail'=> $trail]]
     */
    public static array $appends = [];
    public static bool $addExtension = true;
    public static array $excludedHttpMethods = ['POST', 'DELETE', 'PUT', 'PATCH'];
    protected static array $tree = [];
    protected static string $extension = '';
    protected static string $activeTrail = '';
    protected static $url;
    private \Luracast\Restler\Core $restler;
    private \Psr\Http\Message\ServerRequestInterface $request;
    private \Luracast\Restler\Data\Route $route;

    public function __construct(ServerRequestInterface $request, Route $route, Core $restler)
    {
        $this->request = $request;
        $this->restler = $restler;
        $this->route = $route;
    }

    public function get($for = '', $activeTrail = null, $dataOnly = false)
    {
        if (empty(static::$tree)) {
            if (static::$addExtension) {
                static::$extension = isset($this->restler->responseFormat)
                    ? '.' . $this->restler->responseFormat->extension()
                    : '.html';
            }
            static::$url = $this->restler->baseUrl;
            if (empty(static::$url)) {
                static::$url = '';
            }
            static::$activeTrail = $activeTrail = empty($activeTrail)
                ? (empty($this->restler->url) || $this->restler->url == 'index'
                    ? static::$root
                    : $this->restler->url
                )
                : $activeTrail;
            static::addUrls(static::$prepends);
            $version = $this->restler->requestedApiVersion;
            $map = Routes::findAll(
                $this->request,
                [$this->restler, 'make'],
                static::$excludedPaths,
                static::$excludedHttpMethods,
                $version
            );
            foreach ($map as $path => $data) {
                foreach ($data as $item) {
                    /** @var Route $route */
                    $route = $item['route'];
                    $access = $item['access'];
                    $url = $route->url;
                    if ($access && !Text::contains($url, '{')) {
                        $label = $route->label ?? null;
                        if (!empty($url)) {
                            $url .= static::$extension;
                        }
                        static::add($url, $label);
                    }
                }
            }
            static::addUrls(static::$appends);
        } elseif (empty($activeTrail)) {
            $activeTrail = static::$activeTrail;
        }
        $tree = static::$tree;
        $activeTrail = explode('/', $activeTrail);
        $nested = &static::nested($tree, $activeTrail);
        if (is_array($nested)) {
            $nested['active'] = true;
        }
        if (!empty($for)) {
            $for = explode('/', $for);
            $tree = static::nested($tree, $for)['children'];
        }
        $tree = array_filter($tree);
        if ($dataOnly) {
            return $tree;
        }
        $tags = Emmet::make('ul.nav.nav-tabs');
        foreach ($tree as $branch) {
            if (empty($branch['children'])) {
                $tags[] = Emmet::make('li[role=presentation]>a[href=$href#]{$text#}', $branch);
            } else {
                $tag = Emmet::make(
                    'li.dropdown[role=presentation]>a.dropdown-toggle[data-toggle=dropdown href=$href# role=button aria-haspopup=true aria-expanded=false]{$text# }>span.caret^ul.dropdown-menu>li*children>a[href=$href#]{$text#}',
                    $branch
                );
                $tags[] = $tag;
            }
        }
        return $tags;
    }

    public static function addUrls(array $urls): array
    {
        foreach ($urls as $url => $label) {
            $trail = null;
            if (is_array($label)) {
                if (isset($label['trail'])) {
                    $trail = $label['trail'];
                }
                if (isset($label['url'])) {
                    $url = $label['url'];
                    $label = isset($label['label']) ? $label['label'] : null;
                } else {
                    $url = current(array_keys($label));
                    $label = current($label);
                }
            }
            if (is_numeric($url)) {
                $url = $label;
                $label = null;
            }
            static::add($url, $label, $trail);
        }
        return static::$tree;
    }

    public static function add($url, $label = null, $trail = null)
    {
        $r = parse_url($url);
        if (is_null($trail)) {
            $trail = isset($r['path']) ? $r['path'] : static::$root;
        }
        //remove / prefix and / suffixes and any extension
        $trail = strtok(trim($trail, '/'), '.');
        $parts = explode('/', $trail);
        if (count($parts) == 1 && empty($parts[0])) {
            $parts = [static::$root];
        }
        if (isset($r['fragment'])) {
            $parts[] = $r['fragment'];
            if (is_null($label)) {
                $label = Text::title($r['fragment']);
            }
        }
        if (empty($r['scheme'])) {
            //relative url found
            if (empty($url)) {
                $label = Text::title(static::$root);
                $url = static::$url;
            } else {
                $url = trim(static::$url, '/') . '/' . $url;
            }
        }
        if (is_null($label)) {
            $label = Text::title(strtok(end($parts), '.'));
        }
        $r['url'] = $url;
        $r['path'] = $trail;
        $r['parts'] = $parts;
        $r['label'] = $label;
        static::build($r);
        return $r;
    }

    public static function build(array $r): void
    {
        $p = &static::$tree;
        $parts = $r['parts'];
        $last = count($parts) - 1;
        foreach ($parts as $i => $part) {
            if ($i == $last) {
                $p[$part]['text'] = $r['label'];
                $p[$part]['href'] = $r['url'];
                $p[$part]['class'] = Text::slug($part);
                /* dynamically do it at run time instead
                if ($r['path'] == static::$activeTrail)
                    $p[$part]['active'] = true;
                */
            } elseif (!isset($p[$part])) {
                $p[$part] = [];
                $p[$part]['text'] = Text::title($part);
                $p[$part]['href'] = '#';
                $p[$part]['children'] = [];
            }
            $p = &$p[$part]['children'];
        }
    }

    protected static function &nested(array &$tree, array $parts): ?array
    {
        if (!empty($parts)) {
            $part = array_shift($parts);
            if (empty($tree[$part])) {
                return $tree[$part];
            } elseif (empty($parts)) {
                return static::nested($tree[$part], $parts);
            } elseif (!empty($tree[$part]['children'])) {
                return static::nested($tree[$part]['children'], $parts);
            }
        } else {
            return $tree;
        }
        $null = null;
        return $null;
    }
}
