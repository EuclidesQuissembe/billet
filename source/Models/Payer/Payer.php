<?php

namespace Source\Models\Payer;

use Source\Core\Model;

class Payer extends Model
{
    public function __construct()
    {
        parent::__construct('payers', ['id'], [
            'user_id', 'name', 'email', 'cell_phone', 'day', 'month', 'year'
        ]);
    }

    public function bootstrap(
        string $userId,
        string $name,
        string $email,
        string $cellPhone,
        int $day,
        int $month,
        int $year
    ): Payer
    {
        $this->user_id = $userId;
        $this->name = $name;
        $this->email = $email;
        $this->cell_phone = $cellPhone;
        $this->day = $day;
        $this->month = $month;
        $this->year = $year;
        return $this;
    }

    public function findByEmail(string $email, string $columns = '*'): ?Payer
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


        /** Update payer */
        if ($this->id) {
            $payerId = $this->id;

            if ($this->find('email = :e AND id != :i', "e={$this->email}&i={$payerId}", 'id')->fetch()) {
                $this->message->error("Este endereço de e-mail já se encontra cadastrado");
                return false;
            }

            $this->update($this->safe(), 'id = :id', "id={$payerId}");

            if ($this->fail()) {
                $this->message->error("Houve um erro ao atualizar, por favor verifique os dados");
                return false;
            }
        }

        /** Create payer */
        if (!$this->id) {
            if ($this->findByEmail($this->email)) {
                $this->message->error("Este endereço de e-mail já se encontra cadastrado");
                return false;
            }

            $payerId = $this->create($this->safe());
            if ($this->fail()) {
                $this->message->error("Houve um erro ao cadastrar, por favor verifique os dados");
                return false;
            }
        }

        $this->data = $this->findById($payerId)->data;
        return true;
    }

    public static function filter(array $data): array
    {
        $filter = [];
        foreach ($data as $key => $value) {
            $filter[$key] = (!is_null($value) ? filter_var($value, FILTER_SANITIZE_STRIPPED) : null);
        }
        return $filter;
    }
}