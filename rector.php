<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;
use Rector\TypeDeclaration\Rector\Property\TypedPropertyFromStrictConstructorRector;
use Rector\Php80\Rector\FunctionLike\MixedTypeRector;

return static function (RectorConfig $rectorConfig): void {
    // register single rule
    // $rectorConfig->rule(TypedPropertyFromStrictConstructorRector::class);

    $rectorConfig->paths([
        __DIR__ . '/src',
        // __DIR__ . '/api',
    ]);

    $rectorConfig->skip([
        __DIR__ . '/api/store',
        MixedTypeRector::class,
    ]);

    // here we can define, what sets of rules will be applied
    // tip: use "SetList" class to autocomplete sets with your IDE
    $rectorConfig->sets([
        // SetList::CODE_QUALITY,
        LevelSetList::UP_TO_PHP_81
    ]);
};
