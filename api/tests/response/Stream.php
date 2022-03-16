<?php

class Stream
{
    /**
     * CSV response stream
     * @return object[]
     * @response-format Csv
     */
    public function csv(): array
    {
        return [
            ['id' => 1, 'name' => 'John Doe'],
            ['id' => 2, 'name' => 'Jane Doe'],
        ];
    }
}
