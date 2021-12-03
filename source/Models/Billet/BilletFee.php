<?php

namespace Source\Models\Billet;

use Source\Core\Model;

/**
 * Class BilletFee
 * @package Source\Models
 */
class BilletFee extends Model
{
    /**
     * User constructor.
     */
    public function __construct()
    {
        parent::__construct('billets_fees', ['id'], ['billet_id', 'type', 'value']);
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