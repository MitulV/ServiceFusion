<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use App\Core\CommonUtil;
use App\Models\SFToken;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Support\Facades\Log;

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
        
        $internalRes=$this->internalJobFunction($internalJobs,$customerName,$accessToken);

        $contractRes=$this->contractJobFunction($contractJobs,$customerName,$accessToken);
        
       

    $csvArray=[];
   $i=1;
    foreach ($internalRes as $value) { 
            $csvArray[$i]['Job Type']='Internal';            
            foreach ($value as $k => $v) {
                if($k!='tasks' && $k!='response' && $k!='duration'){
                    $csvArray[$i][$k]=$v;
                }
            }
            $i++;
    }

    foreach ($contractRes as $value) {
        $csvArray[$i]['Job Type']='Contract';            
            foreach ($value as $k => $v) {
                if($k!='tasks' && $k!='response' && $k!='duration'){
                    $csvArray[$i][$k]=$v;
                }
            }
            $i++;
    }


        $fileName = time().'.'.$request->file('file')->extension(); 
        $filePath=(new FastExcel($csvArray))->export(storage_path('app/xlsx/'.$fileName));
        $headers = ['Content-Type: application/xlsx'];
    	        
        return response()->download($filePath, $fileName, $headers);

        $failedjobs=[];
        $totalInternal=count($internalRes);
        $internalSuccess=0;
        $internalFail=0;
        foreach ($internalRes as $internal) {
            if($internal['response_status']=='success'){
                $internalSuccess+=1;
            }else{
                //dd(($internal['response'])->getBody()->getContents());
                unset($internal['response']);
                unset($internal['tasks']);
                $internal['Job Type']='Internal';
                array_push($failedjobs,$internal);
                $internalFail+=1;
            }
        }

        $totalContract=count($contractRes);
        $contractSuccess=0;
        $contractFail=0;
        foreach ($contractRes as $contract) {
            if($contract['response_status']=='success'){
                $contractSuccess+=1;
            }else{
                $contract['Job Type']='Contract';
                array_push($failedjobs,$contract);
                $contractFail+=1;
            }
        }

        // Here you will get list of failed jobs with $failedjobs variable.

        $reportData=[
            'Total Jobs'=>$totalInternal+$totalContract,
            'Total Internal Jobs'=>$totalInternal,
            'Internal Successfully scheduled jobs'=>$internalSuccess,
            'Internal Failed scheduled jobs'=>$internalFail,
            'Total Contract Jobs'=>$totalContract,
            'Contract Successfully scheduled jobs'=>$contractSuccess,
            'Contract Failed scheduled jobs'=>$contractFail];

            dd($reportData);

        return back()->with('success', 'All Jobs Scheduled Successfully');

    }

    public function contractJobFunction($contractJobs,$customerName,$accessToken){
        $finalContractJobs=[];
        foreach ($contractJobs as $key => $job) {
            if($job=="no jobs scheduled" || $job['task service']==""){
                continue;    
            }
            
            $noOfJobs=count($job['task category']);

            for ($i=0; $i < $noOfJobs; $i++) {
                if($job['task category']=='landscaping' || $job['task category']=='pest control' || $job['task category']=='pool maintenance'){
                    continue;
                } 
                $obj['customer_name']="Sanay Patel Test";//$customerName
                $obj['category']="Recurring Maintenance";
                $obj['status']="Unscheduled";
                $obj['priority']="Normal";
                $obj['description']='Maintenance: '.$job['task service'][$i];
        
                $currentYear = Carbon::now()->year;
                $date = Carbon::createFromFormat('F d', $job['date'][$i]);
                $obj['start_date']=$date->year($currentYear)->format('Y-m-d');
                array_push($finalContractJobs,$obj);
            }
        }
        return $this->postJobs($finalContractJobs,$accessToken);
    }
    
    public function internalJobFunction($internalJobs,$customerName,$accessToken){
        $finalInternalJobs=[];
       
        foreach ($internalJobs as $key => $job) {
            if($job=="no jobs scheduled"){
                continue;    
            }

            $noOfJobs=count($job['task category']);
            
            for ($i=0; $i < $noOfJobs; $i++) { 

                $obj['customer_name']="Sanay Patel Test";//$customerName
                $obj['category']="Recurring Maintenance";
                $obj['status']="Unscheduled";
                $obj['duration']=$job['hours'][$i];
                $obj['priority']="Normal";
                $obj['description']=$job['task service'][$i];
               
                $currentYear = Carbon::now()->year;
                $date = Carbon::createFromFormat('F d', $job['date'][$i]);
                $obj['start_date']=$date->year($currentYear)->format('Y-m-d');
                array_push($finalInternalJobs,$obj);
            }
        }
       return $this->postJobs($this->splitInternalArray($finalInternalJobs),$accessToken);
    }

    public function splitInternalArray($array){
       
        $mergedArray = [];

        foreach ($array as $item) 
        {
            $customerName = $item['customer_name'];
            $category = $item['category'];
            $status = $item['status'];
            $duration = $item['duration'];
            $priority = $item['priority'];
            $description = $item['description'];
            $startDate = $item['start_date'];

            if((isset($mergedArray['category']) && ($mergedArray[$startDate]['category']=='landscaping' || $mergedArray[$startDate]['category']=='pest control' || $mergedArray[$startDate]['category']=='pool maintenance'))){
                
                    $mergedArray[$startDate]['customer_name'] = $customerName;
                    $mergedArray[$startDate]['category'] = $category;
                    $mergedArray[$startDate]['status'] = $status;
                    $mergedArray[$startDate]['priority'] = $priority;
                    $mergedArray[$startDate]['description'] = 'Maintenance '.$description;
                    $mergedArray[$startDate]['duration'] = $duration;
                    $mergedArray[$startDate]['tasks'][] = [
                        'description' => $description,
                        'is_completed' => false,
                    ];
                    $mergedArray[$startDate]['start_date'] = $startDate;
            }else{
                if (isset($mergedArray[$startDate])) {
                    $mergedArray[$startDate]['description'] .= ' chr(13) Maintenance: ' . $description;
                    $mergedArray[$startDate]['duration'] += $duration;
                    $mergedArray[$startDate]['tasks'][] = [
                        'description' => $description,
                        'is_completed' => false,
                    ];
                } else {
                   
                    $mergedArray[$startDate]['customer_name'] = $customerName;
                    $mergedArray[$startDate]['category'] = $category;
                    $mergedArray[$startDate]['status'] = $status;
                    $mergedArray[$startDate]['priority'] = $priority;
                    $mergedArray[$startDate]['description'] = 'Maintenance: '.$description;
                    $mergedArray[$startDate]['duration'] = $duration;
                    $mergedArray[$startDate]['tasks'][] = [
                        'description' => $description,
                        'is_completed' => false,
                    ];
                    $mergedArray[$startDate]['start_date'] = $startDate;
                }
            }

        }

        foreach ($mergedArray as $key => $value) {
            $tasks=$value['tasks'];
            array_push($tasks,[
                'description' => 'Was work completed or is additional work needed?',
                'is_completed' => false,
            ],
            [
                'description' => 'Note time you spent and any product expenses',
                'is_completed' => false,
            ],
            [
                'description' => 'Anything new discovered or mentioned by member',
                'is_completed' => false,
            ],
            [
                'description' => 'Add pre and post photos',
                'is_completed' => false,
            ]);
            
            $mergedArray[$key]['tasks']=$tasks;
        }
        
        $mergedArray = array_values($mergedArray);
        
        return $mergedArray;
    }

    public function postJobs(Array $jobs,$accessToken){ 
       $response=[];
        foreach ($jobs as $job) {
            if(isset($job['duration'])){
                $job['duration']=$job['duration'] * 60 * 60;
            }
            
            $res=Http::withToken($accessToken)->post('https://api.servicefusion.com/v1/jobs', $job);
            
            $job['response']=$res;
            if ($res->getStatusCode() >= 200 && $res->getStatusCode() < 300) {
                $job['Response_status']='successfully created';
            } else {
                $job['Response_status']='failed';
            }
            array_push($response,$job);
        }    
        return $response; 
    }
}
