<?php

namespace app\database\entity;

use Exception;

class UserEntity extends Entity
{
    public function emailIsValid()
    {
        if (!isset($this->attributes['email'])) {
            throw new Exception('Email field does not exist');
        }

        return filter_var($this->attributes['email'], FILTER_VALIDATE_EMAIL);
    }

    public function setPasswordHash(string $password)
    {
        $this->attributes['password'] = password_hash($password, PASSWORD_DEFAULT);
    }
}
