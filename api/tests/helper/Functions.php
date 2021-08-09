<?php


class Functions
{
    function server(): array
    {
        return request()->getServerParams();
    }

    function header(): array
    {
        return array_map('current', request()->getHeaders());
    }

    function query(): array
    {
        return request()->getQueryParams();
    }

    function base_path($path = ''): string
    {
        return base_path($path);
    }

    function redirect($path = ''): void
    {
        redirect($path);
    }

    function user()
    {
        return user();
    }
}
