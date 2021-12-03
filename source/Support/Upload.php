<?php


namespace Source\Support;

use CoffeeCode\Uploader\File;
use CoffeeCode\Uploader\Image;
use Exception;

/**
 * Class Upload
 * @package Source\Support
 */
class Upload
{
    /** @var Exception|null $fail */
    private ?Exception $fail = null;

    /** @var string $fileDir */
    private string $fileDir;

    /**
     * Upload constructor.
     * @param string $fileDir
     */
    public function __construct(string $fileDir)
    {
        $this->fileDir = $fileDir;
    }

    /**
     * @param array $arrayImage
     * @param string $name
     * @param int $width
     * @param array|null $quality
     * @return string|null
     */
    public function image(
        array $arrayImage,
        string $name,
        int $width = 2000,
        array $quality = null
    ): ?string {
        $dir = CONF_UPLOAD_IMAGE_DIR . '/' . ($this->fileDir[0] === '/' ? substr($this->fileDir, 1) : $this->fileDir);

        $uploadDir = CONF_UPLOAD_DIR;

        $image = new Image($uploadDir, $dir);
        try {
            return $image->upload($arrayImage, $name, $width, $quality);
        } catch (Exception $exception) {
            $this->fail = $exception;
            return null;
        }
    }

    /**
     * @param array $file
     * @param string $name
     * @return string|null
     */
    public function file(array $file, string $name): ?string
    {
        $dir = CONF_UPLOAD_FILE_DIR . '/' . ($this->fileDir[0] === '/' ? substr($this->fileDir, 1) : $this->fileDir);

        $uploadDir = CONF_UPLOAD_DIR;

        $fileObj = new File($uploadDir, $dir);
        try {
            return $fileObj->upload($file, $name);
        } catch (Exception $exception) {
            $this->fail = $exception;
            return null;
        }
    }

    /**
     * @return mixed
     */
    public function fail(): ?Exception
    {
        return $this->fail;
    }
}
