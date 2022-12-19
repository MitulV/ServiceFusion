<?php

namespace App\Core;

use Symfony\Component\HttpFoundation\File\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Config;
use App\Models\Common\Exception;
use Illuminate\Support\Facades\DB;
use App\Models\Common\FlagMgmt;
use PDF;

class CommonService
{

    public function convertBase64ToFileObject($encodedString)
    {
        $fileData = base64_decode($encodedString);
        $tmpFilePath = sys_get_temp_dir() . '/' . Str::uuid()->toString();
        file_put_contents($tmpFilePath, $fileData);
        $tmpFile = new File($tmpFilePath);

        $file = new UploadedFile(
            $tmpFile->getPathname(),
            $tmpFile->getFilename(),
            $tmpFile->getMimeType(),
            0,
            true
        );
        return $file;
    }

    public function uploadAttachment($file, $extension, $key, $host)
    {
        $filename = $key . '-' . time() . '.' . $extension;
        $path = $file->storeAs('public/'.$key, $filename);
        $url = $host . $this->getLabel('PROJECT_FOLDER_PREFIX') . '/public' . storage::url($path);
        return $url;
    }

    public function generatePDFAttachment($data, $host, $view, $fileName)
    {
        try {
            $pdf = PDF::loadView($view, $data);
            $pdfName = 'public/Invoice/' . $fileName;
            Storage::put($pdfName, $pdf->output());
            $url = $host . $this->getLabel('PROJECT_FOLDER_PREFIX') . '/public' . Storage::url($pdfName);
            return $url;
        } catch (\Exception $e) {
            throw new \Exception("Error While Generating PDF Attachment");
        }
    }

    public static function getConstant($key)
    {
        $path="constants.projectConstants.".$key;
        $constant=Config::get($path);
        if(!is_null($constant))
        {
        return $constant;
        }
        else
            {
                throw new \Exception("Constant not found");
            }
      }

    public static function getQuery($key)
    {
        $path="queries.projectQueries.".$key;
        $query=Config::get($path);
        if(!is_null($query))
        {
            return $query;
        }
        else
        {
            throw new \Exception("Query not found");
        }
    }

    public static function getLabel($key)
    {
        $path="labels.projectLabels.".$key;
        $label=Config::get($path);
        if(!is_null($label))
        {
            return $label;
        }
        else
        {
            throw new \Exception("Label not found");
        }
    }

    public static function getFlagValue($key)
    {
        $value=FlagMgmt::select('value')->where('key',$key)->first();
        if(empty($value)){
            return '';
        }
        return $value->value;
    }

public function uniqueArrayOfObjets($array){

        $keys=[];
        $uniqueKeys=[];
        $result=[];

    foreach ($array as $arr){
        array_push($keys,$arr->product_id);
    }
    $uniqueKeys=array_unique($keys);

    foreach ($array as $arr)
         {
             if(in_array($arr->product_id,$uniqueKeys))
             {
                array_push($result,$arr);
                $key=array_search($arr->product_id, $uniqueKeys);
                unset($uniqueKeys[$key]);
             }
        }
        return $result;
    }

    public function storeException($request,$exception){
        return Exception::create([
            'request_api'=>$request->path(),
            'request_data'=>json_encode($request->all()),
            'exception_message'=>$exception->getMessage(),
            'file'=>$exception->getFile(),
            'line'=>$exception->getLine()
        ]);
    }

    public function paginate($query,$total,$limit,$page){
        $pages = ceil($total / $limit);
        $offset = ($page - 1)  * $limit;
        return $query.' limit '.$limit.' offset '.$offset; 
    }

    public static function broadcastPushNotification($tokens,$title,$body){
        try {

            $data = [
                "registration_ids" => $tokens,
                "notification" => [ "title" => $title, "body" => $body ]
            ];
    
            $dataString = json_encode($data);
    
            $headers = [
                'Authorization: key=' . env("FIREBASE_API_KEY"),
                'Content-Type: application/json',
            ];
    
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
    
            $response = curl_exec($ch);
            
            return ($response);

        }catch (\Exception $e) {
            return $e;
        } 
    }
}
