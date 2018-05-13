<?php

declare(strict_types=1);

namespace Framework\Image;

use claviska\SimpleImage as BaseSimpleImage;

class SimpleImage extends BaseSimpleImage
{
    /**
     * @return string
     * @throws \Exception
     */
    public function suggestExtension(): string
    {
        switch ($this->mimeType) {
            case 'image/jpeg':
                return 'jpg';
                break;
            case 'image/png':
                return 'png';
                break;
            case 'image/gif':
                return 'gif';
                break;
            case 'image/webp':
                return 'webp';
                break;
            default:
                throw new \Exception("Unsupported image type: ".$this->mimeType, self::ERR_UNSUPPORTED_FORMAT);
        }
    }
}
