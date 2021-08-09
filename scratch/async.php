<?php declare(strict_types=1);

use React\Http\Response;

include __DIR__ . "/../vendor/autoload.php";

function getStatus(): int
{
    //echo "!status\n";
    return 200;
}

function getHeaders(): array
{
    //echo "!headers\n";
    return ['header' => 'present'];
}

function getBody(): string
{
    //echo "!body\n";
    return 'Hello World!';
}

function flow(): Generator
{
    try {
        $status = yield 'status' => getStatus();
        $headers = yield 'headers' => getHeaders();
        $body = yield 'body' => getBody();
        return new Response($status, $headers, $body);
    } catch (Throwable $t) {
        var_dump($t);
    }
}

function async(Generator $generator, callable $steps, callable $result)
{
    if ($generator->valid()) {
        $key = $generator->key();
        $value = $generator->current();
        if ($generator->valid()) {
            $steps($key, $value, function ($newValue) use ($generator, $steps, $result, $value) {
                $generator->send($newValue ?? $value);
                async($generator, $steps, $result);
            });
        }
    } else {
        $result($generator->getReturn());
    }
}

$loop = React\EventLoop\Factory::create();
echo '========================================================================' . PHP_EOL;
echo 'Running async flow' . PHP_EOL;
async(
    flow(),
    function (string $name, $value, callable $next) use ($loop) {
        if (is_array($value)) {
            echo "    $name  =  " . str_replace(PHP_EOL, ' ', var_export($value, true)) . PHP_EOL;
            $loop->addTimer(0.8, function () use ($next, $value) {
                $next($value);
            });

        } else {
            $v2 = readline("    $name = [ \033[0;32m{$value}\033[0m ] ");
            $loop->addTimer(0.8, function () use ($next, $value, $v2) {
                $next(empty($v2) ? $value : $v2);
            });
        }
    },
    function ($result) {
        echo PHP_EOL . PHP_EOL, PHP_EOL;
        var_dump((string)$result->getBody());
        var_dump($result);
    }
);
$loop->run();