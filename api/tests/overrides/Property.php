<?php

use Luracast\Restler\Utils\Convert;

class Property
{
    /**
     * @class Convert {@separatorChar _}
     *
     * @return array {@type associative}
     */
    public function transform(): array
    {
        return [
            'author_name' => 'Arul',
            'author_email' => 'arul@luracast.com'
        ];
    }

}