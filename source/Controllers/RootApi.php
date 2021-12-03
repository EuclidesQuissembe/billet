<?php


namespace Source\Controllers;

use Firebase\JWT\SignatureInvalidException;
use Source\Core\Api;
use Source\Core\Auth;
use Source\Core\JWTAuth;

/**
 * Class Api
 * @package Source\App
 */
class RootApi extends Api
{
    /**
     * RootApi constructor.
     */
    public function __construct()
    {
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
        header("Access-Control-Max-Age: 3600");
        header(
            "Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With"
        );

        $headers = filter_var_array(getallheaders(), FILTER_SANITIZE_STRIPPED);

        if (!empty($headers['Authorization'])) {
            $pattern = "/Bearer\s(\S+)/";
            if (preg_match($pattern, $headers['Authorization'], $matches)) {
                $token = $matches[1];
            } else {
                $this->call(
                    500,
                    false,
                    "Parâmetro de autorização com formato incorrecto"
                )->back();
                die;
            }

            try {
                $payload = (new JWTAuth())->decode($token);

                (new Auth($payload->user_id));
            } catch (SignatureInvalidException $exception) {
                $this->call(
                    401,
                    false,
                    $exception->getMessage()
                )->back();
                die;
            } catch (\Exception $exception) {
                $this->call(
                    500,
                    false,
                    $exception->getMessage()
                )->back();
                die;
            }
        }
    }
}