<?php


namespace Source\Core;

/**
 * Class Connect
 *
 * @package Source\Core
 */
class Connect
{
    /**
     * @var
     */
    private static $instance;

    /**
     * @var
     */
    private static $fail;

    /**
     * @return \PDO
     */
    public static function getInstance(): \PDO
    {
        try {
            if (empty(self::$instance)) {
                self::$instance = new \PDO(
                    "mysql:host=" . CONF_DB_HOST . ";dbname=" . CONF_DB_NAME . ";charset=utf8mb4",
                    CONF_DB_USER,
                    CONF_DB_PASS,
                    CONF_DB_OPTIONS
                );
            }
        } catch (\PDOException $exception) {
            self::$fail = $exception;
        }

        return self::$instance;
    }

    /**
     * Connect constructor.
     */
    private function __construct()
    {
    }

    /**
     * Connect clone.
     */
    private function __clone()
    {
    }
}
