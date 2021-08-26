<?php


namespace Luracast\Restler\OpenApi3\Security;


use Luracast\Restler\Utils\ClassName;

class OAuth2 extends Scheme
{
    protected $type = Scheme::TYPE_OAUTH2;
    /**
     * @var OAuth2Flow[]
     */
    protected array $flows = [];

    public function __construct(OAuth2Flow ...$flows)
    {
        foreach ($flows as $flow) {
            $name = lcfirst(ClassName::short(get_class($flow)));
            $this->flows[$name] = $flow;
        }
    }
}
