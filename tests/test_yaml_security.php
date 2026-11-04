<?php

declare(strict_types=1);

use Luracast\Restler\MediaTypes\Yaml;
use Luracast\Restler\ResponseHeaders;
use Luracast\Restler\StaticProperties;
use Luracast\Restler\Utils\Convert;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Parser;

require __DIR__ . '/../vendor/autoload.php';

function expectSame($expected, $actual, string $label): void
{
    if ($expected !== $actual) {
        throw new RuntimeException($label);
    }
}

function expectDepthFailure(callable $parse, string $input, string $label): void
{
    try {
        $parse($input);
        throw new RuntimeException($label . ' was accepted');
    } catch (ParseException $e) {
        if (strpos($e->getMessage(), 'Maximum nesting depth') === false) {
            throw new RuntimeException($label . ' missed the inline lexer depth limit', 0, $e);
        }
    }
    echo "PASS $label\n";
}

$parser = new Parser(4);
$valid = ['name' => 'Restler', 'values' => [1, true, null], 'mapping' => ['key' => 'value']];
$input = "name: Restler\nvalues: [1, true, null]\nmapping: {key: value}\n";
expectSame($valid, $parser->parse($input), 'Valid block/inline YAML failed');
echo "PASS valid block, inline sequence and mapping\n";

// Unclosed collections distinguish the vulnerable lexer from the already-bounded
// subsequent inline parser. Inputs are tiny; no stack exhaustion is necessary.
foreach (['sequence' => str_repeat('[', 5), 'mapping' => str_repeat('{a: ', 5), 'mixed' => '[{a: [{b: ['] as $label => $malicious) {
    expectDepthFailure([$parser, 'parse'], $malicious, $label . ' lexer depth limit');
    expectSame($valid, $parser->parse($input), 'Parser state did not recover after ' . $label);
}
echo "PASS parser state recovery\n";

$media = new Yaml(new Convert(new StaticProperties(Convert::class)));
expectSame($valid, $media->decode($input), 'Restler YAML decode failed');
expectSame($valid, $media->decode($media->encode($valid, new ResponseHeaders())), 'Restler YAML round trip failed');
echo "PASS Restler YAML decode/encode round trip\n";
expectDepthFailure([$media, 'decode'], str_repeat('[', Parser::DEFAULT_MAX_NESTING_LEVEL + 1), 'Restler default-limit lexer rejection');
expectSame($valid, $media->decode($input), 'Restler decode did not recover');
echo "PASS Restler decode after rejection\n";
