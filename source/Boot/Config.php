<?php

use PHPMailer\PHPMailer\PHPMailer;

/**
 * PROJECT URLs
 */
const CONF_URL_BASE = "http://localhost:8000";
const CONF_URL_TEST = "http://localhost:8000";


/**
 * DATABASE
 */
const CONF_DB_HOST = "localhost";
const CONF_DB_PASS = "";
const CONF_DB_NAME = "";

if (strpos($_SERVER['HTTP_HOST'], 'localhost')) {
    define("CONF_DB_USER", "root");
} else {
    define("CONF_DB_USER", "");
}

const CONF_DB_OPTIONS = [
    \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
    \PDO::ATTR_CASE => \PDO::CASE_NATURAL
];


/**
 * PASSWORD
 */
const CONF_PASSWORD_ALGO = PASSWORD_DEFAULT;
const CONF_PASSWORD_MAX_LEN = 40;
const CONF_PASSWORD_MIN_LEN = 8;
const CONF_PASSWORD_OPTIONS = ['cost' => 10];



/**
 * DATE
 */
const CONF_DATE_FORMAT = 'd \d\e M \d\e Y  à\s H:i:s';
const CONF_DATE_SERVER = 'Y-m-d H:i:s';
const CONF_DATE_TIMEZONE = 'Africa/Luanda';