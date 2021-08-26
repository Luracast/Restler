<?php


namespace Luracast\Restler\OpenApi3\Tags;


use Luracast\Restler\Data\Route;
use Luracast\Restler\Utils\Text;

class TagByBasePath implements Tagger
{
    public static array $descriptions = [
        'root' => 'main api'
    ];

    /**
     * @param Route $route
     * @return string[] in tag => description format
     */
    public static function tags(Route $route): array
    {
        $base = strtok($route->url, '/');
        if (empty($base)) {
            $base = 'root';
        } else {
            $base = Text::slug($base, '');
        }
        if (!$description = self::$descriptions[$base] ?? false) {
            if (!empty($route->resource['summary'])) {
                $description = self::$descriptions[$base] = $route->resource['summary'];
            } else {
                $description = '';
            }
        }
        return [$base => $description];
    }
}
