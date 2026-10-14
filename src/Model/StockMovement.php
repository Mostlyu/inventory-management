<?php

namespace App\Model;

class StockMovement {

    public function __construct(
        public int $id,
        public int $itemId,
        public string $type,
        public int $quantity,
        public ?string $note,
        public string $createdAt,
        public string $updatedAt,
        public ?string $itemName

    ) {}
}