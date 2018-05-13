<?php

namespace Framework\PHP;

class UploadMaxDetector
{
    public static function bytesExtract($str): int
    {
        $str = trim($str);
        if (preg_match('#^(\d+)([KMGT])?B?$#i', $str, $matches)) {
            $lastChar = isset($matches[2]) ? strtoupper($matches[2]) : null;
            $num = intval($matches[1]);
            switch ($lastChar) {
                case 'T':
                    $num *= 1024;
                // The 'G' modifier is available since PHP 5.1.0
                case 'G' :
                    $num *= 1024;
                case 'M' :
                    $num *= 1024;
                case 'K' :
                    $num *= 1024;
                default:
            }

            return $num;
        } else {
            throw new \InvalidArgumentException("Expected format \\d+[KMGT]?/i, ".$str." given");
        }
    }

    public static function bytesFormat(int $num, int $precision = 1): string
    {
        $KB = 1024;
        $MB = 1024 * $KB;
        $GB = 1024 * $MB;
        $TB = 1024 * $GB;

        if ($num > $TB) {
            return number_format($num / $TB, $precision).' TB';
        }

        if ($num > $GB) {
            return number_format($num / $GB, $precision).' GB';
        }

        if ($num > $MB) {
            return number_format($num / $MB, $precision).' MB';
        }

        if ($num > $KB) {
            return number_format($num / $KB, $precision).' KB';
        }

        return $num . ' B';
    }

}
