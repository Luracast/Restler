<?php


namespace Luracast\Restler\OpenApi3\Security;


use InvalidArgumentException;

class ApiKeyAuth extends Scheme
{
    public const IN_HEADER = 'header';
    public const IN_QUERY = 'query';
    public const IN_COOKIE = 'cookie';

    protected $type = Scheme::TYPE_API_KEY;
    protected string $name;
    protected string $in;

    /**
     * ApiKeyAuth constructor.
     * @param string $name
     * @param string $in {@choice header,query,cookie}
     * @param string $description
     */
    public function __construct(string $name, string $in = 'header', string $description = '')
    {
        if (!defined(__CLASS__ . '::IN_' . strtoupper($in))) {
            throw new InvalidArgumentException('value for $in should be one of the class constants');
        }
        $this->name = $name;
        $this->in = $in;
        $this->description = $description;
    }
}
