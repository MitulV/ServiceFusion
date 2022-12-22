<?php

namespace App\Http\Controllers;

use App\Core\CommonUtil;
use App\Mail\sendFusionData;
use App\Models\SFToken;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;


class HomeController extends Controller
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

    public function getCustomers(Request $request){
       
        $accessToken=$this->refreshAccessToken($request);
        $url="https://api.servicefusion.com/v1/customers?per-page=50&filters[tags]=member&sort=-created_at"; 
        $response=CommonUtil::callAPI($url,[],'GET',$accessToken); 
  
        foreach ($response['items'] as $customer) {
            $customerName=$customer['customer_name'];
            $agent=$customer['agent'];
            $email='darshil@admin.com';
            $this->getJobs($customerName,$email,$agent,$accessToken);  
            // if($customerName=="Michael and Hillary Riccobene"){
            //     $this->getJobs($customerName,$email,$agent,$accessToken);  
            // }
            
        }  
        return ['status'=>'ok'];
    }

    public function getJobs($customerName,$email,$agent,$accessToken){
        $lte=Carbon::now()->addDays(30)->toDateString(); 
        $gte=Carbon::now()->toDateString();
        
        $url="https://api.servicefusion.com/v1/jobs?filters[customer_name]=$customerName&filters[start_date][lte]=$lte&filters[start_date][gte]=$gte&access_token=$accessToken";
        
        $response = json_decode(Http::get($url), true);    
        $jobs=$response && $response['items'] ? $response['items'] : [];
        $jobs_new=[];
       foreach($jobs as $job) { 
        if(!str_contains(strtolower($job['description']), 'credit')){
            array_push($jobs_new,$job);
        }   
       }
        $this->getEstimates($customerName,$email,$jobs_new,$agent,$accessToken);
    }

    public function getEstimates($customerName,$email,$jobs,$agent,$accessToken){
        $url="https://api.servicefusion.com/v1/estimates?filters[customer_name]=$customerName&filters[status]=Estimate Provided&access_token=$accessToken";
        $response = json_decode(Http::get($url), true); 
        $estimates=$response && $response['items'] ? $response['items'] : [];
        $this->sendEmail($customerName,$email,$jobs,$agent,$estimates);
    }

    public function sendEmail($customerName,$email,$jobs,$agent,$estimates){
        Mail::to($email)->send(new sendFusionData($customerName,$jobs,$estimates,$agent));
    }
}
