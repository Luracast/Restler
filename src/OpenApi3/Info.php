<?php
namespace Luracast\Restler\OpenApi3;


class Info
{
    public static string $title = 'Restler API Explorer';
    public static string $description = 'Example api documentation brought to you by **restler team**';
    public static $termsOfServiceUrl = null;
    public static string $contactName = 'Restler Support';
    public static string $contactEmail = 'arul@luracast.com';
    public static string $contactUrl = 'https://luracast.com/products/restler';
    public static string $license = 'LGPL-2.1';
    public static string $licenseUrl = 'https://www.gnu.org/licenses/old-licenses/lgpl-2.1.html';

    public static function format($swaggerVersion)
    {
        $swaggerVersion = (int)$swaggerVersion;
        switch ($swaggerVersion) {
            case 1:
                return array_filter(
                    [
                        'title' => static::$title,
                        'description' => static::$description,
                        'termsOfServiceUrl' => static::$termsOfServiceUrl,
                        'contact' => static::$contactEmail,
                        'license' => static::$license,
                        'licenseUrl' => static::$licenseUrl,
                    ]
                );
            case 2:
            case 3:
                return array_filter(
                    [
                        'title' => static::$title,
                        'description' => static::$description,
                        'termsOfService' => static::$termsOfServiceUrl,
                        'contact' => array_filter(
                            [
                                'name' => static::$contactName,
                                'email' => static::$contactEmail,
                                'url' => static::$contactUrl,
                            ]
                        ),
                        'license' => array_filter(
                            [
                                'name' => static::$license,
                                'url' => static::$licenseUrl,
                            ]
                        ),
                    ]
                );
        }
        return [];
    }
}
