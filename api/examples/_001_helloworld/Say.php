<?php

/**
 * that walk you through restler examples.
 */
class Say
{
    function hello($to = 'world'):string
    {
        return "Hello $to!";
    }

    function hi(string $to): string
    {
        return "Hi $to!";
    }
}
