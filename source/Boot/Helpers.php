<?php

/**
 * ################
 * ###   URLs   ###
 * ################
 */

/**
 * @param string|null $path
 *
 * @return string
 */
function url(string $path = null): string
{
    if (strpos($_SERVER['HTTP_HOST'], "localhost")) {
        if ($path) {
            return CONF_URL_TEST . "/" . ($path[0] == "/" ? substr($path, 1) : $path);
        }
        return CONF_URL_TEST;
    }

    if ($path) {
        return CONF_URL_BASE . "/" . ($path[0] == "/" ? substr($path, 1) : $path);
    }
    return CONF_URL_BASE;
}

/**
 * ####################
 * ###   VALIDATE   ###
 * ####################
 */

/**
 * @param string $email
 * @return bool
 */
function is_email(string $email): bool
{
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

/**
 * @param string $password
 * @return bool
 */
function is_password(string $password): bool
{
    if (password_get_info($password)['algo']
        || strlen($password) >= CONF_PASSWORD_MIN_LEN && strlen($password) <= CONF_PASSWORD_MAX_LEN) {
        return true;
    }

    return false;
}

/**
 * ####################
 * ###   PASSWORD   ###
 * ####################
 */

/**
 * @param string $password
 * @return string
 */
function passwd(string $password): string
{
    if (password_get_info($password)['algo']) {
        return $password;
    }

    return password_hash($password, CONF_PASSWORD_ALGO, CONF_PASSWORD_OPTIONS);
}

/**
 * @param string $password
 * @param string $hash
 * @return string
 */
function passwd_verify(string $password, string $hash): string
{
    return password_verify($password, $hash);
}

/**
 * @param string $hash
 * @return bool
 */
function passwd_hash(string $hash): bool
{
    return password_needs_rehash($hash, CONF_PASSWORD_ALGO, CONF_PASSWORD_OPTIONS);
}

/**
 * ##################
 * ###   STRING   ###
 * ##################
 */

/**
 * @param string $string
 * @return string
 */
function str_slug(string $string): string
{
    $string = filter_var(mb_strtolower($string), FILTER_SANITIZE_STRIPPED);
    $formats = 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜüÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûýýþÿRr"!@#$%&*()_-+={[}]/?;:.,\\\'<>°ºª';
    $replace = 'aaaaaaaceeeeiiiidnoooooouuuuuybsaaaaaaaceeeeiiiidnoooooouuuyybyRr                                 ';

    return str_replace(
        ["-----", "----", "---", "--"],
        "-",
        str_replace(
            " ",
            "-",
            trim(strtr(utf8_decode($string), utf8_decode($formats), $replace))
        )
    );
}

/**
 * @param string $file
 *
 * @return string
 */
function storage(string $file): string
{
    if (strpos($_SERVER['HTTP_HOST'], "localhost")) {
        return CONF_URL_TEST . "/storage/" . ($file[0] == "/" ? substr($file, 1) : $file);
    }

    return CONF_URL_BASE . "/storage/" . ($file[0] == "/" ? substr($file, 1) : $file);
}
