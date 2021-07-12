<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Param extends Model
{
    const TYPE_IMAGE = 'image';     // el archivo es una imagen
    const TYPE_PDF = 'pdf';         // el archivo es un pdf
    const TYPE_TEXT = 'text';       // el archivo es un texto

    const SOURCE_BASE64 = 'base64'; // el archivo esta codificado en un string base64
    const SOURCE_FILE = 'file';     // el archivo es un archivo real (.img, .pdf, .text)
    const SOURCE_URL = 'url';       // el archivo esta en una url
    const SOURCE_STRING = 'string'; // el archivo esta en un string

    const CONN_TYPE_WINDOWS = 1;
    const CONN_TYPE_NETWORK = 2;
    const CONN_TYPE_FILE = 3;

    public static function fileTypes()
    {
        return collect([
            (object) [ 'value' => self::TYPE_IMAGE, 'text' => 'IMAGEN' ],
            (object) [ 'value' => self::TYPE_PDF, 'text' => 'PDF' ],
            (object) [ 'value' => self::TYPE_TEXT, 'text' => 'TEXTO' ],
        ]);
    }

    public static function fileSources()
    {
        return collect([
            (object) [ 'value' => self::SOURCE_STRING, 'text' => 'STRING' ],
            (object) [ 'value' => self::SOURCE_BASE64, 'text' => 'BASE64' ],
            (object) [ 'value' => self::SOURCE_FILE, 'text' => 'ARCHIVO' ],
            (object) [ 'value' => self::SOURCE_URL, 'text' => 'URL' ],
        ]);
    }

    public static function connTypes()
    {
        return collect([
            (object) [ 'value' => self::CONN_TYPE_WINDOWS, 'text' => 'WINDOWS' ],
            (object) [ 'value' => self::CONN_TYPE_NETWORK, 'text' => 'NETWORK' ],
            (object) [ 'value' => self::CONN_TYPE_FILE, 'text' => 'FILE' ],
        ]);
    }
}
