<?php


declare(strict_types=1);

use GraphQL\Type\Definition\Type as GraphQLType;
use Luracast\Restler\GraphQL\GraphQL;
use Luracast\Restler\Routes;
use ratelimited\Authors as RateLimitedAuthors;
use v2\BodyMassIndex as BMI2;


Routes::mapApiClasses([GraphQL::class]);
GraphQL::addAuthenticator(AccessControl::class);
GraphQL::$mutations['sum'] = [
    'type' => GraphQLType::int(),
    'args' => [
        'x' => ['type' => GraphQLType::int(), 'defaultValue' => 5],
        'y' => GraphQLType::nonNull(GraphQL::enum([
            'name' => 'SelectedEnum',
            'description' => 'selected range of numbers',
            'values' => ['five' => 5, 'seven' => 7, 'nine' => 9]
        ])),
    ],
    'resolve' => function ($root, $args) {
        return $args['x'] + $args['y'];
    },
];
GraphQL::$mutations['sumUp'] = [
    'type' => GraphQLType::int(),
    'args' => [
        'numbers' => GraphQLType::listOf(GraphQLType::int())
    ],
    'resolve' => function ($root, $args) {
        return array_sum($args['numbers']);
    },
];
GraphQL::$queries['echo'] = [
    'type' => GraphQLType::string(),
    'args' => [
        'message' => ['type' => GraphQLType::nonNull(GraphQLType::string()), 'defaultValue' => 'Hello'],
    ],
    'resolve' => function ($root, $args) {
        return $root['prefix'] . $args['message'];
    }
];

GraphQL::mapApiClasses([
    RateLimitedAuthors::class,
    Say::class,
    BMI2::class,
    Access::class,
    Tasks::class,
]);
GraphQL::addMethod(new ReflectionMethod(Math::class, 'add'));
GraphQL::addMethod(new ReflectionMethod(Math::class, 'sum2'));
GraphQL::addMethod(new ReflectionMethod(Type::class, 'postEnumerator'));
