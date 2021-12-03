<?php

namespace Source\Models\Payer;

use Source\Core\Model;

class PayerAddress extends Model
{
    public function __construct()
    {
        parent::__construct('payers_addresses', ['id'], [
            'payer_id', 'cep', 'street', 'district', 'city', 'state'
        ]);
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