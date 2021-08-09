<?php


namespace Luracast\Restler\Data;


use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type as GraphQLType;
use Luracast\Restler\Exceptions\HttpException;
use Luracast\Restler\GraphQL\GraphQL;
use Luracast\Restler\Routes;
use Luracast\Restler\Utils\ClassName;
use Luracast\Restler\Utils\CommentParser;
use ReflectionNamedType;

class Returns extends Type
{
    /** @var string */
    public $label;

    public static function fromReturnType(?ReflectionNamedType $reflectionType, ?array $metadata, array $scope): self
    {
        $instance = new static();
        $types = $metadata['type'] ?? ['array'];
        $itemTypes = $metadata[CommentParser::$embeddedDataName]['type'] ?? ['object'];
        $instance->description = $metadata['description'] ?? '';
        $instance->format = $metadata[CommentParser::$embeddedDataName]['format'] ?? '';
        $instance->label = $metadata[CommentParser::$embeddedDataName]['label'] ?? null;
        $instance->apply($reflectionType, $types, $itemTypes, $scope);
        return $instance;
    }

    public function toGraphQL()
    {
        if (in_array($this->type, GraphQL::INVALID_TYPES)) {
            throw new HttpException(500, 'Return value with data type `' . $this->type . '` is not supported');
        }
        $type = null;
        if ($this->scalar) {
            $type = call_user_func([GraphQLType::class, $this->type]);
        } else {
            $class = ClassName::short($this->type);
            if (isset(GraphQL::$definitions[$class])) {
                $type = GraphQL::$definitions[$class];
            } else {
                $config = ['name' => $class, 'fields' => []];
                if (is_array($this->properties)) {
                    /** @var Type $property */
                    foreach ($this->properties as $name => $property) {
                        $subType = $property->type !== 'bool' && in_array(
                            $name,
                            Routes::$prefixingParameterNames
                        )
                            ? GraphQLType::id()
                            : $property->toGraphQL();
                        $config['fields'][$name] = ['type' => $subType];
                        if (GraphQL::$showDescriptions && $property->description) {
                            $config['fields'][$name]['description'] = $property->description;
                        }
                    }
                }
                $type = $this instanceof Param
                    ? new InputObjectType($config)
                    : new ObjectType($config);
            }
            GraphQL::$definitions[$class] = $type;
        }
        if (!$this->nullable) {
            $type = GraphQLType::nonNull($type);
        }
        if ($this->multiple) {
            $type = GraphQLType::listOf($type);
        }
        return $type;
    }
}
