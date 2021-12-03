<?php

namespace Source\Core;

use Source\Models\User;

/**
 * Class Access
 * @package Source\App
 */
class Auth
{
    /**
     * @var User|null
     */
    private static $user;

    /**
     * Access constructor.
     * @param string $userId
     */
    public function __construct(string $userId)
    {
        self::$user = (new User())->findById($userId);
    }

    /**
     * @return User|null
     */
    public static function user(): ?User
    {
        return self::$user;
    }
}