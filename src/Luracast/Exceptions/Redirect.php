<?php
namespace Luracast\Restler\Exceptions;


use Luracast\Restler\Utils\Text;

class Redirect extends HttpException
{
    public function __construct(string $location, int $httpStatusCode = 302, array $headers = [])
    {
        parent::__construct($httpStatusCode);
        foreach ($headers as $header => $value) {
            $this->setHeader($header, $value);
        }
        if (!Text::beginsWith($location, 'http') && !Text::beginsWith($location, '/')) {
            $location = base_path($location);
        }
        $this->setHeader('Location', $location);
    }
}
