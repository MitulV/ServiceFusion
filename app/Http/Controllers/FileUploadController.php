<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Rap2hpoutre\FastExcel\FastExcel;



class FileUploadController extends Controller
{
    public function fileUploadPost(Request $request)
    {

        $request->validate([
            'file' => 'required|mimes:xlsx',
        ]);

        


        $file=$request->file;
        $data = (new FastExcel)->import($file);
        dd($data);
        $resultArray=[];
        $i=0;
        foreach ($data as $raw) {
        
        }
             
          $fileName = time().'.'.$file->extension();  
          $filePath=(new FastExcel($resultArray))->export($fileName);
             	 
        $headers = ['Content-Type: application/xlsx'];
    	
        return response()->download($filePath, $fileName, $headers);
    }
}
