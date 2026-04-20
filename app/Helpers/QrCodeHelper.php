<?php

namespace App\Helpers;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelMedium;
use Endroid\QrCode\Writer\SvgWriter;

class QrCodeHelper
{
    public static function generate($data, $size = 150)
    {
        $result = Builder::create()
            ->writer(new SvgWriter())
            ->data($data)
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(new ErrorCorrectionLevelMedium())
            ->size($size)
            ->margin(10)
            ->build();

        return $result->getString();
    }
}
