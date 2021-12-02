<?php


namespace Source\Support;

/**
 * Class Media
 *
 * @package Source\Support
 */
class Media extends \CoffeeCode\Uploader\Media
{

    /**
     * @var string[]
     */
    protected static $allowTypes = [
        ""
    ];

    /**
     * @var string[]
     */
    protected static $extensions = [
        ""
    ];

    /**
     * Media constructor.
     * @param $fileDir
     * @param bool $isApi
     */
    public function __construct($fileDir, bool $isApi = false)
    {
        $dir = CONF_UPLOAD_MEDIA_DIR . "/" . $fileDir;

        $uploadDir = $isApi ? "../" . CONF_UPLOAD_DIR : CONF_UPLOAD_DIR;

        parent::__construct($uploadDir, $dir, true);
    }

    /**
     * @param array $media
     * @param string $name
     * @return string
     * @throws \Exception
     */
    public function upload(array $media, string $name): string
    {
        $this->ext = mb_strtolower(pathinfo($media['name'])['extension']);

        if (in_array($media['type'], static::$allowTypes) || in_array($this->ext, static::$extensions)) {
            throw new \Exception("Format not allowed");
        }

        $this->name($name);
        move_uploaded_file($media['tmp_name'], "{$this->path}/{$this->name}");
        return "{$this->path}/{$this->name}";
    }
}
