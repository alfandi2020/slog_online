<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Controller;
use PicoPrime\BarcodeGen\BarcodeGenerator;

class BarcodeController extends Controller
{
    public function show($text, $size = 50, $scale = 1)
    {
        $codeType = 'code128';
        $orientation = 'horizontal';
        $barcode = new BarcodeGenerator();
        return $barcode
            ->generate(compact('text', 'size', 'orientation', 'codeType', 'scale'))
            ->response('png');
    }
}
