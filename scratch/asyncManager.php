<?php declare(strict_types=1);

use function PHPSTORM_META\type;
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
        //var_dump([$status, $headers, $body]);
        return new Response($status, $headers, $body);
    } catch (Throwable $t) {
        var_dump($t);
    }
}

/**
 * @param Generator $h
 */
function process(Generator $h)
{
    sleep(2);
    foreach ($h as $k => $v) {
        sleep(2);
        //echo "$k = '$v'\n";
        /*
        switch ($k) {
            case 'status':
                $v = 404;
                break;
            default:
        }
        */
        $h->send($v);
    }
    /**
     * @var Response
     */
    $r = $h->getReturn();
    if ($r) {
        var_dump($r);
        //$rr = new Response();
        var_dump($r->getBody());
    }
}


function async(Generator $generator, iAsyncTaskManager $task)
{
    if ($generator->valid()) {
        $key = $generator->key();
        $value = $generator->current();
        if ($generator->valid()) {
            $task::handle($key, $value, function ($newValue) use ($generator, $task, $value) {
                $generator->send($newValue ?? $value);
                async($generator, $task);
            });
        }
    } else {
        $task::result($generator->getReturn());
    }
}

//process(flow());
/*
$h = flow();
echo '========================================================================' . PHP_EOL;
echo $h->key() . PHP_EOL;
var_dump($v = $h->current());
echo PHP_EOL . PHP_EOL, PHP_EOL;
while ($h->valid()) {
    //$h->next();
    $h->send($v);
    if ($h->valid()) {
        echo $h->key() . PHP_EOL;
        var_dump($v = $h->current());
        echo PHP_EOL . PHP_EOL, PHP_EOL;
    }
}
function asyncCallable(Generator $generator, Callable $steps)
{
    $key = $generator->key();
    $value = $generator->current();
    $value = $steps($key, $value);
    while ($generator->valid()) {
        $generator->send($value);
        if ($generator->valid()) {
            $key = $generator->key();
            $value = $generator->current();
            $value = $steps($key, $value);
        }
    }
    return $generator->getReturn();
}
*/

interface iAsyncTaskManager
{
    public static function handle(string $name, $value, callable $next): void;

    public static function result($value): void;
}


$loop = React\EventLoop\Factory::create();


$manager = new Class implements iAsyncTaskManager
{

    public static function handle(string $name, $value, callable $next): void
    {
        global $loop;
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
    }

    public static function result($value): void
    {
        echo PHP_EOL . PHP_EOL, PHP_EOL;
        var_dump((string)$value->getBody());
        var_dump($value);
    }
};

echo '========================================================================' . PHP_EOL;
echo 'Running async flow' . PHP_EOL;
async(flow(), $manager);
$loop->run();
