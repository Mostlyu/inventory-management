<?php

namespace App\Model;

class User {

    public function __construct(
        public int $id,
        public string $username,
        public string $role,
        public string $passwordHash
    ) {}
}