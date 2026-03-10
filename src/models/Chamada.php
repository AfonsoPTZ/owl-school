<?php

namespace App\Models;

class Chamada
{
    public ?int $id;
    public string $data;

    public function __construct(
        string $data,
        ?int $id = null
    ) {
        $this->id = $id;
        $this->data = $data;
    }
}
