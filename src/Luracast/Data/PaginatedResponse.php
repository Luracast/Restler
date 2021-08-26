<?php


namespace Luracast\Restler\Data;


use JsonSerializable;
use Luracast\Restler\Contracts\GenericResponseInterface;
use Psr\Http\Message\UriInterface;
use ReflectionClass;
use ReflectionException;

class PaginatedResponse implements GenericResponseInterface
{
    private array $data;

    final private function __construct(array $data)
    {
        $this->data = $data;
    }

    public static function fromSerializable(JsonSerializable $prefilled): self
    {
        return new static($prefilled->jsonSerialize());
    }

    public static function fromPrefilled(array $data): self
    {
        return new static($data);
    }

    public static function build(
        UriInterface $baseUrl,
        array $collection,
        int $per_page,
        int $total,
        int $current_page
    ): self {
        $from = $per_page * ($current_page - 1) + 1;
        $to = min($total, $from + $per_page - 1);
        $last_page = ceil($total / $per_page);
        parse_str($baseUrl->getQuery(), $params);
        $uri = $baseUrl->withQuery('');
        $url = function (int $page) use ($params, $uri): string {
            $params['page'] = $page;
            return (string)$uri->withQuery(http_build_query($params));
        };
        return new static(
            [
                'total' => $total,
                'per_page' => $per_page,
                'current_page' => $current_page,
                'last_page' => $last_page,
                'first_page_url' => $url(1),
                'last_page_url' => $url($last_page),
                'next_page_url' => $current_page >= $last_page ? null : $url($current_page + 1),
                'prev_page_url' => $current_page <= 1 ? null : $url($current_page - 1),
                'path' => (string)$uri,
                'from' => $from,
                'to' => $to,
                'data' => $collection,
            ]
        );
    }

    public static function responds(string ...$types): Returns
    {
        try {
            $data = empty($types)
                ? Returns::__set_state(['type' => 'object', 'scalar' => false])
                : Returns::fromClass(new ReflectionClass($types[0]));
        } catch (ReflectionException $e) {
            $data = Returns::__set_state(['type' => 'object', 'scalar' => false]);
        }
        $data->multiple = true;
        $data->nullable = false;
        return Returns::__set_state(
            [
                'type' => 'PaginatedResponse',
                'properties' => [
                    'total' => Returns::__set_state(['type' => 'int']),
                    'per_page' => Returns::__set_state(['type' => 'int']),
                    'current_page' => Returns::__set_state(['type' => 'int']),
                    'last_page' => Returns::__set_state(['type' => 'int']),
                    'first_page_url' => Returns::__set_state(['type' => 'string']),
                    'last_page_url' => Returns::__set_state(['type' => 'string']),
                    'next_page_url' => Returns::__set_state(['type' => 'string']),
                    'prev_page_url' => Returns::__set_state(['type' => 'string']),
                    'path' => Returns::__set_state(['type' => 'string']),
                    'from' => Returns::__set_state(['type' => 'int']),
                    'to' => Returns::__set_state(['type' => 'int']),
                    'data' => $data,
                ]
            ]
        );
    }

    public function jsonSerialize(): array
    {
        return $this->data;
    }
}
