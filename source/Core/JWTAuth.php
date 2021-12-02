<?php


namespace Source\Core;

use Firebase\JWT\JWT;

/**
 * Class JWTAuth
 * @package Source\Core
 */
class JWTAuth
{
    /**
     * @param array $payload
     * @param string $key
     * @param string $alg
     * @param null $keyId
     * @param null $head
     * @return string
     */
    public function encode(
        array $payload,
        string $key = CONF_JWT_KEY,
        string $alg = CONF_JWT_ALG,
        $keyId = null,
        $head = null
    ): string {
        return JWT::encode($payload, $key, $alg, $keyId, $head);
    }

    /**
     * @param string $jwt
     * @param string $key
     * @param array|string[] $algs
     * @return object
     */
    public function decode(string $jwt, string $key = CONF_JWT_KEY, array $algs = CONF_JWT_ALGS): object
    {
        return JWT::decode($jwt, $key, $algs);
    }
}
