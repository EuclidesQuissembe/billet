<?php


namespace Source\Core;

/**
 * Class Api
 * @package Source\Core
 */
class Api
{
    /** @var $response */
    protected $response;

    /**
     * @param int $code
     * @param bool|null $success
     * @param string|null $message
     * @return Api|null
     */
    protected function call(int $code, bool $success, ?string $message = null): ?Api
    {
        http_response_code($code);

        $this->response = [
            "success" => $success,
            "message" => $message ?: null,
        ];

        return $this;
    }

    /**
     * @param null $data
     * @param string $prop
     * @return Api
     */
    protected function back($data = null, string $prop = 'data'): Api
    {
        if (!empty($this->response)) {
            $this->response = ($data ? array_merge($this->response, [$prop => (array)$data]) : $this->response);
            echo json_encode($this->response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);
            return $this;
        }

        echo json_encode([$prop => $data], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);
        return $this;
    }
}