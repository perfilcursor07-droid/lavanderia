<?php

namespace App\Services;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelMedium;
use Endroid\QrCode\Writer\SvgWriter;

class QrCodeService
{
    protected $size = 150;

    public function size($size)
    {
        $this->size = $size;
        return $this;
    }

    public function generate($data)
    {
        $result = Builder::create()
            ->writer(new SvgWriter())
            ->data($data)
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(new ErrorCorrectionLevelMedium())
            ->size($this->size)
            ->margin(10)
            ->build();

        // Reset size for next use
        $this->size = 150;

        return $result->getString();
    }
}
