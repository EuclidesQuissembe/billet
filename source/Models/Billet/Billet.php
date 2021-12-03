<?php

namespace Source\Models\Billet;

use Source\Core\Model;

/**
 * Class Billet
 * @package Source\Models
 */
class Billet extends Model
{
    /**
     * User constructor.
     */
    public function __construct()
    {
        parent::__construct('billets', ['id'], ['user_id', 'payer_id', 'due_date', 'price', 'description']);
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