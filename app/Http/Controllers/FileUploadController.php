<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use App\Core\CommonUtil;
use App\Models\SFToken;


class FileUploadController extends Controller
{
    public function refreshAccessToken(Request $request){
        
        $tokenOBJ=SFToken::find(1);

        $url="https://api.servicefusion.com/oauth/access_token";
        $data=[
            "grant_type"=>"refresh_token",
            "refresh_token"=>$tokenOBJ->refresh_token
        ];
        
        $res=CommonUtil::callAPI($url,json_encode($data),'POST',$tokenOBJ->access_token); 

        $tokenOBJ->update([
            "access_token"=>$res['access_token'],
            "refresh_token"=>$res['refresh_token']
        ]);

        return $res['access_token'];
        
    }

    public function fileUploadPost(Request $request)
    {

        $request->validate([
            'file' => 'required|mimes:xlsx',
        ]);

        $path=Storage::path($request->file('file')->store('xlsx'));

        $response = Http::get('http://127.0.0.1:5000/createjob', [
            'path' => $path,
            'sheet' => 'Planning Calendar',
        ]);

        $data=json_decode($response,true);
        

        $internalJobs=json_decode($data['internal_jobs'],true);
        $contractJobs=json_decode($data['contract_jobs'],true);
        $customerName=json_decode($data['customer_name'],true);
        unset($internalJobs['total hours']);
       
        $accessToken=$this->refreshAccessToken($request);
       
        foreach ($internalJobs as $key => $job) {
            if($job=="no jobs scheduled"){
                continue;    
            }

            $noOfJobs=count($job['task category']);

            for ($i=0; $i < $noOfJobs; $i++) { 

                $obj['customer_name']="Sanay Patel Test";//$customerName
                $obj['category']="Recurring Maintenance";
                $obj['status']="Unscheduled";
                $obj['duration']=(int)$job['hours'][$i];
                $obj['priority']="Normal";
                $obj['description']=$job['task service'][$i];
                
                $currentYear = Carbon::now()->year;
                $date = Carbon::createFromFormat('F d', $job['date'][$i]);
                $obj['start_date']=$date->year($currentYear)->format('Y-m-d');
                $response = Http::withToken($accessToken)->post('https://api.servicefusion.com/v1/jobs', $obj);
            }
        }

        foreach ($contractJobs as $key => $job) {
            if($job=="no jobs scheduled"){
                continue;    
            }
            
            $noOfJobs=count($job['task category']);

            for ($i=0; $i < $noOfJobs; $i++) { 
                $obj['customer_name']="Sanay Patel Test";//$customerName
                $obj['category']="Recurring Maintenance";
                $obj['status']="Unscheduled";
                //$obj['duration']=(int)$job['hours'][$i];
                $obj['priority']="Normal";
                $obj['description']=$job['task service'][$i];
                
                $currentYear = Carbon::now()->year;
                $date = Carbon::createFromFormat('F d', $key." 30");
                $obj['start_date']=$date->year($currentYear)->format('Y-m-d');
               
                $response = Http::withToken($accessToken)->post('https://api.servicefusion.com/v1/jobs', $obj);
                //dd($response->getBody()->getContents());
               
            }
        }

        return back()->with('success', 'All Jobs Scheduled Successfully');

    }
}
