<?php
namespace Framework\Twig\Functions;

class FileWithMtime extends \Twig_SimpleFunction
{
    public function __construct()
    {
        parent::__construct(
            'file_with_mtime',
            function ($filePath) {
                $file = $_SERVER['DOCUMENT_ROOT'] . '/' . explode('?', $filePath)[0];
                if (is_file($file)) {
                    if (strpos($filePath, '?') === false) {
                        return $filePath . '?mtime='.filemtime($file);
                    } else {
                        return $filePath . '&mtime='.filemtime($file);
                    }
                } else {
                    return $filePath;
                }
            },
            ['is_safe' => ['html' => true]]
        );
    }
}
