<?php

/**
 * PROJECT URLs
 */
const CONF_URL_BASE = "http://localhost:8000";
const CONF_URL_TEST = "http://localhost:8000";

/**
 * DATABASE
 */
const CONF_DB_HOST = "db";
const CONF_DB_PASS = "billet.password";
const CONF_DB_NAME = "billet";

if (strpos($_SERVER['HTTP_HOST'], 'localhost')) {
    define("CONF_DB_USER", "root");
} else {
    define("CONF_DB_USER", "root");
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

// JWT
const CONF_JWT_ALG = "HS256";
const CONF_JWT_ALGS = [CONF_JWT_ALG];
const CONF_JWT_KEY = "billet";

/**
 * UPLOAD
 */
const CONF_UPLOAD_DIR = "storage";
const CONF_UPLOAD_IMAGE_DIR = "images";
const CONF_UPLOAD_FILE_DIR = "files";
