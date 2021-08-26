<?php


namespace Luracast\Restler\Data;


use Luracast\Restler\Contracts\GenericResponseInterface;
use Luracast\Restler\Exceptions\HttpException;
use Throwable;

class ErrorResponse implements GenericResponseInterface
{
    public array $response = [];

    public function __construct(Throwable $exception, bool $debug = false)
    {
        $code = $exception->getCode();
        $this->response['error'] = [
            'code' => $code > 99 ? $code : 500,
            'message' => $exception->getMessage()
        ];
        if ($exception instanceof HttpException) {
            $this->response += $exception->getDetails();
        }
        if ($debug) {
            $innerException = $exception;
            while ($prev = $innerException->getPrevious()) {
                $innerException = $prev;
            }
            $trace = array_slice($innerException->getTrace(), 0, 10);
            $this->response['debug'] = [
                'source' => !method_exists($exception, 'getSource') ? 'internal' : $exception->getSource(),
                'trace' => array_map([static::class, 'simplifyTrace'], $trace)
            ];
        }
    }

    public static function responds(string ...$types): Returns
    {
        return Returns::__set_state(
            [
                'type' => 'ErrorResponse',
                'scalar' => false,
                'properties' =>
                    [
                        'error' => Returns::__set_state(
                            [
                                'type' => 'Error',
                                'scalar' => false,
                                'properties' =>
                                    [
                                        'code' => Returns::__set_state(['type' => 'int']),
                                        'message' => Returns::__set_state(
                                            [
                                                'type' => 'string',
                                                'pattern' => '/^[a-zA-Z0-9?><;,{}[\]\-_+=!@#$%\^&*|\']*$/',
                                            ]
                                        ),
                                    ],
                            ]
                        ),
                        'debug' => Returns::__set_state(
                            [
                                'type' => 'Debug',
                                'scalar' => false,
                                'description' => '',
                                'properties' =>
                                    [
                                        'source' => Returns::__set_state(
                                            ['type' => 'string', 'pattern' => '/[\s\S]*/']
                                        ),
                                        'trace' => Returns::__set_state(
                                            [
                                                'type' => 'Trace',
                                                'scalar' => false,
                                                'properties' =>
                                                    [
                                                        'file' => Returns::__set_state(
                                                            [
                                                                'type' => 'string',
                                                                'pattern' => '/[\s\S]*/',
                                                                'multiple' => false,
                                                                'nullable' => false,
                                                                'scalar' => true
                                                            ]
                                                        ),
                                                        'function' => Returns::__set_state(
                                                            [
                                                                'type' => 'string',
                                                                'pattern' => '/[\s\S]*/',
                                                                'multiple' => false,
                                                                'nullable' => false,
                                                                'scalar' => true
                                                            ]
                                                        ),
                                                        'args' => Returns::__set_state(
                                                            [
                                                                'type' => 'string',
                                                                'pattern' => '/[\s\S]*/',
                                                                'multiple' => true,
                                                                'nullable' => false,
                                                                'scalar' => true
                                                            ]
                                                        ),
                                                    ],
                                            ]
                                        ),
                                    ],


                            ]
                        ),
                    ],
            ]
        );
    }

    private static function simplifyTrace(array $trace): array
    {
        $parts = explode('\\', $trace['class'] ?? '');
        $class = array_pop($parts);
        $parts = explode('/', $trace['file'] ?? '');
        return [
            'file' => array_pop($parts) . (isset($trace['line']) ? ':' . $trace['line'] : ''),
            'function' => $class . ($trace['type'] ?? '') . $trace['function'],
            'args' => array_map([static::class, 'simplifyTraceArgs'], $trace['args'] ?? []),
        ];
    }

    private static function simplifyTraceArgs($argument)
    {
        if (is_object($argument)) {
            return 'new ' . get_class($argument) . '()';
        }
        if (is_array($argument)) {
            return array_map(__METHOD__, $argument);
        }
        if (is_resource($argument)) {
            return 'resource(' . get_resource_type($argument) . ')';
        }
        return $argument;
    }

    public function __toString()
    {
        return json_encode($this->jsonSerialize());
    }

    public function jsonSerialize(): array
    {
        return $this->response;
    }
}

