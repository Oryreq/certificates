<?php

namespace App\Service;

use ConvertApi\ConvertApi;
use ConvertApi\ResultFile;

class PdfConverter
{
    private const API_CREDENTIALS = 'secret_sSJf9kU6VaHCbWBg';


    public function __construct()
    {
        ConvertApi::setApiCredentials(self::API_CREDENTIALS);
    }

    public function convertFromDocx(string $filePath): ResultFile
    {
        $result = ConvertApi::convert('pdf', [
            'File' => "$filePath".'.docx',
            'PageRange' => '1',
            'PageOrientation' => 'portrait',
            'PageSize' => 'a4',
        ], 'docx'
        );
        return $result->getFile();
    }
}