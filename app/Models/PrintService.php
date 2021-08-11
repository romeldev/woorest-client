<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Mike42\Escpos\Printer;
use Mike42\Escpos\EscposImage;
use Illuminate\Support\Str;
use Mike42\Escpos\ImagickEscposImage;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;

class PrintService extends Model
{
    // public $connType;
    // public $connName;
    // public $connections;

    public $connType;
    public $connName;
    public $connTarget;

    public $contentType;
    public $contentSource;
    public $contentBody;

    function __construct( $data ) {
        $this->connType = $data['conn_type'];
        $this->connName = $data['conn_name'];
        $this->connTarget = $data['conn_target'];
        $this->contentType = $data['content_type'];
        $this->contentSource = $data['content_source'];
        $this->contentBody = $data['content_body'];
    }


    public static function PrintConnections($connections)
    {
        $data1 = $connections[0];

        $request = [];
        foreach($connections as $data){
            $request[] = (new PrintService($data))->print();
        }

        return $request;
    }

    public function getPrintConnector() {

        $connector = null;
        if( $this->connType == Param::CONN_TYPE_NETWORK ){
            $meta = explode(':', $this->connName);
            $ip = isset($meta[0])? trim($meta[0]): null;
            $port = isset($meta[1])? trim($meta[1]): null;
            $connector = new NetworkPrintConnector($ip, $port);
        }else if( $this->connType == Param::CONN_TYPE_WINDOWS ){
            $connector = new WindowsPrintConnector($this->connName);
        }else if( $this->connType == Param::CONN_TYPE_FILE ){
            $connector = new FilePrintConnector($this->connName);
        }
        if( $connector==null) throw new \Exception("Print connection null");
        return $connector;
    }

    public function print()
    {
        if( $this->contentType == Param::CONTENT_TYPE_IMAGE) {
            return $this->printImage();
        }else {
            return 'NOT_IMPLEMENT';
        }

        // if( $this->contentType == Param::CONTENT_TYPE_PDF) {
        //     return $this->printPdf();
        // }

        // if( $this->contentType == Param::CONTENT_TYPE_TEXT) {
        //     return $this->printText();
        // }
    }

    public function printImage()
    {
        $res = [];

        $targetTxt = $this->connTarget? $this->connTarget: $this->connName;

        try {
            $filepath = $this->generateFile($this->contentType, $this->contentSource, $this->contentBody);
            // $filepath = 'C:\laragon\www\sources\precuenta.jpg';
            // $filepath = 'C:\laragon\www\sources\ticket.jpg';
            // $filepath = 'C:\laragon\www\sources\ticket572.png';
            // $filepath = 'C:\laragon\www\sources\monedas\cinco-soles2.png';
            // $filepath = 'C:\laragon\www\sources\0please2.png';

            $image = EscposImage::load($filepath);

            // dd( Printer::IMG_DOUBLE_HEIGHT );

            $printer = new Printer($this->getPrintConnector());
            $printer->graphics($image);
            $printer->cut();
            $printer->close();
            // unlink($filepath);
            $res['status'] = true;
            $res['message'] = "Print [$targetTxt]: OK!";
        } catch (\Exception $e) {
            $res['status'] = false;
            $res['message'] = "Error [$targetTxt]: ".$e->getMessage();
        }

        return $res;
    }

    

    public function printPdf()
    {
        $filepath = $this->generateFile($type, $source, $content);
        $filepath = "C:\laragon\www\sources\boleta.pdf";

        $pages = ImagickEscposImage::loadPdf($filepath);
        dd($pages);
    }

    public function printText()
    {
        $filepath = $this->generateFile($type, $source, $content);
        $contents = trim(file_get_contents($filepath));
        $lines = explode("\r\n", $contents);
        dd($lines);
    }

    public function generateFile($type, $source, $content)
    {
        $tmpDir = sys_get_temp_dir();
        $filepath = null;
        if($source==Param::CONTENT_SOURCE_URL) {
            $url = $content;
            $info = pathinfo($url);
            $contents = file_get_contents($url);
            $filename = 'file_'.date('U').'.'.$info['extension'];
            $filepath = $tmpDir.'\\'.$filename;
            file_put_contents($filepath, $contents);
            $file = new UploadedFile($filepath, $filename);
            // $filepath = $file->path();

            $filename = $this->generateFilename($type);
            $path = Storage::disk('public')->putFileAs('files', $file, $filename);
            $filepath = Storage::disk('public')->path($path);
        }

        if($source==Param::CONTENT_SOURCE_FILE) {
            $file = $content;
            // $filepath = $file->path();
            $filename = $this->generateFilename($type);
            $path = Storage::disk('public')->putFileAs('files', $file, $filename);
            $filepath = Storage::disk('public')->path($path);
        }

        if($source==Param::CONTENT_SOURCE_BASE64) {
            $contents64 = str_replace('data:image/png;base64,', '', $content);
            $contents = base64_decode($contents64);
            // $filename = 'file_'.date('U'). ($type==Param::TYPE_IMAGE? '.png': ($type==Param::TYPE_PDF?'.pdf': '.txt') );
            // $filepath = $tmpDir.'\\'.$filename;
            // file_put_contents($filepath, $contents);

            $filename = $this->generateFilename($type);
            $path = Storage::disk('public')->put("files/$filename", $contents);
            $filepath = Storage::disk('public')->path("files/$filename");
            // dd($filepath);
        }

        if($source==Param::CONTENT_SOURCE_STRING) {
            $contents = $content;
            $filename = 'file_'.date('U').'.txt';
            $filepath = $tmpDir.'\\'.$filename;
            file_put_contents($filepath, $contents);
        }

        return $filepath;
    }

    public function generateFilename($type)
    {
        $prefix = 'file_';
        $id = date('U');

        $filename = 'unknown';

        if( $type==Param::CONTENT_TYPE_IMAGE){
            $filename = "{$prefix}{$id}.jpg";
        }
        return $filename;
    }
}
