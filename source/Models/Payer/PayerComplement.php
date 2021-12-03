<?php

namespace Source\Models\Payer;

use Source\Core\Model;

class PayerComplement extends Model
{
    public function __construct()
    {
        parent::__construct('payers_complements', ['id'], [ 'payer_id', 'complement' ]);
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