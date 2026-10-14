<?php

namespace App\Model;

class Item {

    public function __construct(
        public int $id,
        public string $name,
        public ?string $description,
        public int $categoryId,
        public string $sku,
        public string $createdAt,
        public string $updatedAt,
        public ?string $categoryName = null
    ) {}
}