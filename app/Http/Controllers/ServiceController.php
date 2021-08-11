<?php

namespace App\Http\Controllers;

use App\Models\Param;
use App\Models\PrintService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ServiceController extends Controller
{
    public function print(Request $request)
    {
        // return $request->connections;

        if( !is_array($request->connections)){
            if( $data = json_decode($request->connections) ){
                $request['connections'] = collect($data)->map(function($item){
                    return (array)$item;
                })->toArray();
            }
        }

        if( isset($request['content']) && $request['content']=='null') $request['content'] = null;

        $contentTypes = Param::contentTypes()->pluck('value')->toArray();
        $contentSources = Param::contentSources()->where('value', '<>', Param::CONTENT_SOURCE_STRING)->pluck('value')->toArray();
        $contentSourcesAll = Param::contentSources()->pluck('value')->toArray();
        $connTypes = Param::connTypes()->pluck('value')->toArray();

        $rules = [
            'connections' => 'required|array|min:1',
            'connections.*.conn_type' => 'required|in:'.implode(',', $connTypes),
            'connections.*.conn_name' => 'required',
            'connections.*.content_type' => 'required|in:'.implode(',', $contentTypes),
        ];

        foreach($request->connections as $key => $conn)
        {
            $contentType = isset($conn['content_type'])? $conn['content_type']: null;
            $contentSource = isset($conn['content_source'])? $conn['content_source']: null;

            if( $contentType==Param::CONTENT_TYPE_IMAGE || $contentType==Param::CONTENT_TYPE_PDF) {
                $rules["connections.$key.content_source"] = 'required|in:'.implode(',', $contentSources);
            }

            if( $contentType==Param::CONTENT_TYPE_TEXT) {
                $rules["connections.$key.content_source"] = 'required|in:'.implode(',', $contentSourcesAll);
            }
    
            if( $contentType==Param::CONTENT_TYPE_IMAGE ) {
                if ( $contentSource==Param::CONTENT_SOURCE_BASE64){
                    $rules["connections.$key.content_body"] = 'required';
                }else if ( $contentSource==Param::CONTENT_SOURCE_URL){
                    $rules["connections.$key.content_body"] = 'required|url';
                }else if ( $contentSource==Param::CONTENT_SOURCE_FILE){
                    $rules["connections.$key.content_body"] = 'required|file|max:5120|mimes:jpg,png,jpeg';
                }
            }
    
            if( $contentType==Param::CONTENT_TYPE_PDF ) {
                if ( $contentSource==Param::CONTENT_SOURCE_BASE64){
                    $rules["connections.$key.content_body"] =  'required';
                }else if ( $contentSource==Param::CONTENT_SOURCE_URL){
                    $rules["connections.$key.content_body"] = 'required|url';
                }else if ( $contentSource==Param::CONTENT_SOURCE_FILE){
                    $rules["connections.$key.content_body"] = 'required|file|max:5120|mimes:pdf';
                }
            }
    
            if( $contentType==Param::CONTENT_TYPE_TEXT ) {
                if ( $contentSource==Param::CONTENT_SOURCE_BASE64 ||  $contentSource==Param::CONTENT_SOURCE_STRING){
                    $rules["connections.$key.content_body"] = 'required';
                }else if ( $contentSource==Param::CONTENT_SOURCE_URL){
                    $rules["connections.$key.content_body"] = 'required|url';
                }else if ( $contentSource==Param::CONTENT_SOURCE_FILE){
                    $rules["connections.$key.content_body"] = 'required|file|max:5120|mimes:txt';
                }
            }
        }

        $validator = Validator::make($request->all(), $rules);

        if( $validator->fails() ) return response()->json($validator->messages(), 422);

        $response = PrintService::PrintConnections($request->connections);

        return $response;
    }

    public function printOld(Request $request)
    {
        // return $request->connections;

        if( !is_array($request->connections)){
            if( $data = json_decode($request->connections) ){
                $request['connections'] = collect($data)->map(function($item){
                    return (array)$item;
                })->toArray();
            }
        }
        
        if( isset($request['content']) && $request['content']=='null') $request['content'] = null;

        $fileTypes = Param::fileTypes()->pluck('value')->toArray();
        
        $fileSources = Param::fileSources()->where('value', '<>', Param::SOURCE_STRING)->pluck('value')->toArray();
        $fileSourcesAll = Param::fileSources()->pluck('value')->toArray();
        $connTypes = Param::connTypes()->pluck('value')->toArray();

        $rules = [
            'type' => 'required|in:'.implode(',', $fileTypes),
            'connections' => 'required|array|min:1',
            'connections.*.conn_type' => 'required|in:'.implode(',', $connTypes),
            'connections.*.conn_type' => 'required',
        ];

        if( $request->type==Param::TYPE_IMAGE || $request->type==Param::TYPE_PDF) {
            $rules['source'] = 'required|in:'.implode(',', $fileSources);
        }

        if( $request->type==Param::TYPE_TEXT) {
            $rules['source'] = 'required|in:'.implode(',', $fileSourcesAll);
        }

        if( $request->type==Param::TYPE_IMAGE ) {
            if ($request->source==Param::SOURCE_BASE64){
                $rules['content'] = 'required';
            }else if ($request->source==Param::SOURCE_URL){
                $rules['content'] = 'required|url';
            }else if ($request->source==Param::SOURCE_FILE){
                $rules['content'] = 'required|file|max:5120|mimes:jpg,png,jpeg';
            }
        }

        if( $request->type==Param::TYPE_PDF ) {
            if ($request->source==Param::SOURCE_BASE64){
                $rules['content'] =  'required';
            }else if ($request->source==Param::SOURCE_URL){
                $rules['content'] = 'required|url';
            }else if ($request->source==Param::SOURCE_FILE){
                $rules['content'] = 'required|file|max:5120|mimes:pdf';
            }
        }

        if( $request->type==Param::TYPE_TEXT ) {
            if ($request->source==Param::SOURCE_BASE64 || $request->source==Param::SOURCE_STRING){
                $rules['content'] = 'required';
            }else if ($request->source==Param::SOURCE_URL){
                $rules['content'] = 'required|url';
            }else if ($request->source==Param::SOURCE_FILE){
                $rules['content'] = 'required|file|max:5120|mimes:txt';
            }
        }

        $validator = Validator::make($request->all(), $rules);

        if( $validator->fails() ) return response()->json($validator->messages(), 422);

        return (new PrintService($request->connections))->print($request->type, $request->source, $request->content);
    }
    
}
