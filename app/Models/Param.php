<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Param extends Model
{
    const CONTENT_TYPE_IMAGE = 'image';     // el archivo es una imagen
    const CONTENT_TYPE_PDF = 'pdf';         // el archivo es un pdf
    const CONTENT_TYPE_TEXT = 'text';       // el archivo es un texto

    const CONTENT_SOURCE_BASE64 = 'base64'; // el archivo esta codificado en un string base64
    const CONTENT_SOURCE_FILE = 'file';     // el archivo es un archivo real (.img, .pdf, .text)
    const CONTENT_SOURCE_URL = 'url';       // el archivo esta en una url
    const CONTENT_SOURCE_STRING = 'string'; // el archivo esta en un string

    const CONN_TYPE_WINDOWS = 1;
    const CONN_TYPE_NETWORK = 2;
    const CONN_TYPE_FILE = 3;

    public static function contentTypes()
    {
        return collect([
            (object) [ 'value' => self::CONTENT_TYPE_IMAGE, 'text' => 'IMAGEN' ],
            (object) [ 'value' => self::CONTENT_TYPE_PDF, 'text' => 'PDF' ],
            (object) [ 'value' => self::CONTENT_TYPE_TEXT, 'text' => 'TEXTO' ],
        ]);
    }

    public static function contentSources()
    {
        return collect([
            (object) [ 'value' => self::CONTENT_SOURCE_STRING, 'text' => 'STRING' ],
            (object) [ 'value' => self::CONTENT_SOURCE_BASE64, 'text' => 'BASE64' ],
            (object) [ 'value' => self::CONTENT_SOURCE_FILE, 'text' => 'ARCHIVO' ],
            (object) [ 'value' => self::CONTENT_SOURCE_URL, 'text' => 'URL' ],
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
