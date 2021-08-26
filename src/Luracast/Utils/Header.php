<?php

namespace Luracast\Restler\Utils;


class Header
{

    /**
     * Pass any content negotiation header such as Accept,
     * Accept-Language to break it up and sort the resulting array by
     * the order of negotiation.
     *
     * @static
     *
     * @param string $accept header value
     *
     * @return array sorted by the priority
     */
    public static function sortByPriority($accept)
    {
        $acceptList = [];
        $accepts = explode(',', strtolower($accept));
        if (!is_array($accepts)) {
            $accepts = [$accepts];
        }
        foreach ($accepts as $pos => $accept) {
            $parts = explode(';', $accept);
            $type = trim(array_shift($parts));
            $parameters = [];
            foreach ($parts as $part) {
                $part = explode('=', $part);
                if (2 !== count($part)) {
                    continue;
                }
                $key = strtolower(trim($part[0]));
                $parameters[$key] = trim($part[1], ' "');
            }
            $quality = (float)($parameters['q'] ?? (1000 - $pos) / 1000);
            $acceptList[$type] = $quality;
        }
        arsort($acceptList);
        return $acceptList;
    }
}
