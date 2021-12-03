<?php


namespace Source\Controllers;

use Source\Core\JWTAuth;
use Source\Models\User;

/**
 * Class Access
 * @package Source\App
 */
class Access extends RootApi
{
    public function login(): void
    {
        $data = filter_var_array((array)(json_decode(file_get_contents("php://input"))), FILTER_SANITIZE_STRIPPED);

        if (empty($data['email']) || empty($data['password'])) {
            $this->call(400, false, "O email e a password são obrigatórios")->back();
            return;
        }

        $user = (new User())->findByEmail($data['email']);

        if (!$user) {
            $this->call(500, false, "Inválido email ou password")->back();
            return;
        }

        $equalPassword = passwd_verify($data['password'], $user->password_hash);

        if (!$equalPassword) {
            $this->call(500, false, "Inválido email ou password")->back();
            return;
        }

        // JWT (JSON Web Token)
        $payload = [
            'iss' => 'localhost/api',
            'user_id' => $user->id
        ];

        $token = (new JWTAuth())->encode($payload);

        unset($user->data()->password_hash);

        $this->call(200, true)->back([
            'token' => $token,
            'user' => $user->data()
        ]);
    }

    /**
     * API INDEX
     */
    public function register(): void
    {
        $data = filter_var_array((array)(json_decode(file_get_contents("php://input"))), FILTER_SANITIZE_STRIPPED);

        if (in_array("", $data)) {
            $this->call(400, false, 'Informe todos os campos')->back();
            return;
        }

        $user = new User();

        $user->bootstrap(
            $data['first_name'] ?? "",
            $data['last_name'] ?? "",
            $data['email'] ?? "",
            $data['password'] ?? "",
        );

        if (!$user->save()) {
            $this->call(500, false, $user->message()->json())->back();
            return;
        }

        // JWT (JSON Web Token)
        $payload = [
            'iat' => time(),
            'exp' => '10s',
            'iss' => 'localhost/api',
            'user_id' => $user->id
        ];

        $token = (new JWTAuth())->encode($payload);

        unset($user->data()->password_hash);

        $this->call(201, true)->back([
            'token' => $token,
            'user' => $user->data()
        ]);
    }
}