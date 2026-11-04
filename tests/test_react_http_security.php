<?php

declare(strict_types=1);

// Run each decoder case in a child process: vulnerable versions can loop forever.
if (($argv[1] ?? '') !== 'child') {
    foreach (['trailer', 'terminator', 'valid', 'headers', 'headers-fragmented', 'headers-complete', 'headers-legacy'] as $case) {
        $process = proc_open([PHP_BINARY, __FILE__, 'child', $case], [1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $pipes);
        if (!is_resource($process)) {
            throw new RuntimeException('Unable to start regression test');
        }
        $deadline = microtime(true) + 3;
        do {
            $status = proc_get_status($process);
            if (!$status['running']) {
                break;
            }
            usleep(10000);
        } while (microtime(true) < $deadline);
        if ($status['running']) {
            proc_terminate($process, 9);
        }
        $output = stream_get_contents($pipes[1]) . stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);
        proc_close($process);
        if ($status['running'] || $status['exitcode'] !== 0) {
            throw new RuntimeException($case . ' failed' . ($status['running'] ? ' (timeout)' : ': ' . $output));
        }
        echo "PASS $case\n";
    }
    exit(0);
}

require __DIR__ . '/../vendor/autoload.php';

use React\EventLoop\Factory;
use React\Http\Io\ChunkedDecoder;
use React\Http\Io\ClientConnectionManager;
use React\Http\Io\ClientRequestStream;
use React\Http\Message\Request;
use React\Socket\Connector;
use React\Stream\ThroughStream;

$case = $argv[2];
if (strpos($case, 'headers') === 0) {
    $loop = Factory::create();
    $client = new ClientRequestStream(new ClientConnectionManager(new Connector([], $loop), $loop), new Request('GET', 'http://localhost/'));
    $error = null;
    $client->on('error', function (Throwable $e) use (&$error) { $error = $e; });
    // Bounded input, no network or memory exhaustion required.
    $header = $case === 'headers-legacy' ? "HTTP/1.1 200 OK\nX-Fill: " : "HTTP/1.1 200 OK\r\nX-Fill: ";
    $header .= str_repeat('A', 131072);
    if ($case === 'headers-complete') {
        $header .= "\r\n\r\n";
    } elseif ($case === 'headers-legacy') {
        $header .= "\n\n";
    }
    foreach ($case === 'headers-fragmented' ? str_split($header, 8192) : [$header] as $fragment) {
        $client->handleData($fragment);
        if ($error) {
            break;
        }
    }
    if (!$error instanceof OverflowException || strpos($error->getMessage(), 'Maximum response header size') === false || $client->isWritable()) {
        throw new RuntimeException('Oversized unterminated response headers were not rejected');
    }
} else {
    $input = new ThroughStream();
    $decoder = new ChunkedDecoder($input);
    $output = '';
    $ended = false;
    $error = null;
    $decoder->on('data', function ($data) use (&$output) { $output .= $data; });
    $decoder->on('end', function () use (&$ended) { $ended = true; });
    $decoder->on('error', function (Throwable $e) use (&$error) { $error = $e; });
    if ($case === 'valid') {
        foreach (["1\r", "\nA\r", "\n0\r\n", "\r\n"] as $fragment) {
            $input->write($fragment);
        }
        if ($output !== 'A' || !$ended || $error) {
            throw new RuntimeException('Valid fragmented chunked body failed');
        }
    } else {
        $input->write($case === 'trailer' ? "0\r\nab" : "1\r\nAAB");
        $input->end();
        if (!$error || $decoder->isReadable()) {
            throw new RuntimeException('Malformed chunked body was not rejected');
        }
    }
}
