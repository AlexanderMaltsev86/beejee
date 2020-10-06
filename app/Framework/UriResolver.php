<?php


namespace App\Framework;


class UriResolver {
    /**
     * @var string
     */
    private $basePath;

    /**
     * UriResolver constructor.
     * @param string $basePath
     */
    public function __construct(string $basePath) {
        $this->basePath = $basePath;
    }

    public function getPath(string $relativePath) {
        if (empty($relativePath) || $relativePath === '/') {
            return $this->basePath . '/';
        } else {
            if (substr($relativePath, 0, 1) === '/') {
                $relativePath = substr($relativePath, 1, strlen($relativePath));
            }
            return $this->basePath . '/' . $relativePath;
        }
    }
}