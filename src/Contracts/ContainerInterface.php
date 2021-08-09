<?php

namespace Luracast\Restler\Contracts;

use Psr\Container\ContainerInterface as PsrContainer;

interface ContainerInterface extends PsrContainer
{
    public function __construct(&$config = []);

    public function init(&$config): void;

    public function make($abstract, array $parameters = []);

    public function setPropertyInitializer(callable $function): void;

    public function instance($abstract, $instance);
}
