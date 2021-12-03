<?php

namespace Source\Models;

use Source\Core\Model;

/**
 * Class User
 * @package Source\Models
 */
class User extends Model
{
    /**
     * User constructor.
     */
    public function __construct()
    {
        parent::__construct('users', ['id'], ['first_name', 'last_name', 'email', 'password']);
    }

    public function bootstrap(string $firstName, string $lastName, string $email, string $password): User
    {
        $this->first_name = $firstName;
        $this->last_name = $lastName;
        $this->email = $email;
        $this->password = $password;
        return $this;
    }

    public function findByEmail(string $email, string $columns = '*'): ?User
    {
        return $this->find('email = :email', "email={$email}", $columns)->fetch();
    }

    public function save(): bool
    {
        if (!$this->required()) {
            $this->message->error("Informe todos os campos");
            return false;
        }

        if (!is_email($this->email)) {
            $this->message->error("Email com formato incorrecto");
            return false;
        }

        if (!is_password($this->password)) {
            $min = CONF_PASSWORD_MIN_LEN;
            $max = CONF_PASSWORD_MAX_LEN;

            $this->message->error("A senha deve estar no intervalo de {$min} à {$max} caracteres");
            return false;
        } else {
            $this->password_hash = passwd($this->password);
            unset($this->data->password);
        }

        /** Update user */
        if ($this->id) {
            $userId = $this->id;

            if ($this->find('email = :e AND id != :i', "e={$this->email}&i={$userId}", 'id')->fetch()) {
                $this->message->error("Este endereço de e-mail já se encontra cadastrado");
                return false;
            }

            $this->update($this->safe(), 'id = :id', "id={$userId}");

            if ($this->fail()) {
                $this->message->error("Houve um erro ao atualizar, por favor verifique os dados");
                return false;
            }
        }

        /** Create user */
        if (!$this->id) {
            if ($this->findByEmail($this->email)) {
                $this->message->error("Este endereço de e-mail já se encontra cadastrado");
                return false;
            }

            $userId = $this->create($this->safe());
            if ($this->fail()) {
                $this->message->error("Houve um erro ao cadastrar, por favor verifique os dados");
                return false;
            }
        }

        $this->data = $this->findById($userId)->data;
        return true;
    }

    /**
     * @param array $data
     * @return array
     */
    public static function filter(array $data): array
    {
        $filter = [];
        foreach ($data as $key => $value) {
            $filter[$key] = (!is_null($value) ? filter_var($value, FILTER_SANITIZE_STRIPPED) : null);
        }
        return $filter;
    }
}