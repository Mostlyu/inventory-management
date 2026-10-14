<?php

namespace App\Model;

class Category {

public function __construct(
        public int $id,
        public string $name,
        public ?string $description = null
    ) {}
}