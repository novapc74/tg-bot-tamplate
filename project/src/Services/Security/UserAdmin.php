<?php

namespace App\Services\Security;

class UserAdmin
{
    public function getPlainPassword(): string
    {

    }

    public function getName(): string
    {

    }

    public function getRoles(): array
    {
        return ['ROLE_ADMIN'];
    }

    public function isAdmin(): bool
    {
        return in_array('ROLE_ADMIN', $this->getRoles());
    }
}
