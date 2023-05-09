<?php

namespace App\Http\Controllers;

use App\Core\CommonUtil;
use App\Mail\sendFusionData;
use App\Models\SFToken;
use Carbon\Carbon;
use Illuminate\Http\Request;
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
        $url="https://api.servicefusion.com/v1/customers?per-page=50&filters[tags]=member&sort=-created_at&expand=contacts,contacts.emails,custom_fields"; 
        $response=CommonUtil::callAPI($url,[],'GET',$accessToken); 
       
        foreach ($response['items'] as $customer) 
        {
            $sendFlag=false;
            $customerName=$customer['customer_name'];
            $mondayURL=null;
            
            foreach ($customer['custom_fields'] as $field)
            {
                if($field['name']=="Monday.com On Demand Projects")
                {
                    if($field['value']!=null)
                    {
                        $mondayURL=$field['value'];
                    }
                }
                if($field['name']=="1-Membership Start (Target/Actual)" || $field['name']=="1-Membership Start" || $field['name']=="Membership Start")
                {
                    if((Carbon::parse($field['value']))->lt(Carbon::now()))
                    {
                        $sendFlag=true;
                    }
                }
            }
           
            if($mondayURL==null){
                $sendFlag=false;
            }

            $agent=$customer['agent'];

            $fnames='';

            if(count($customer['contacts'])==1){
                $fnames=$customer['contacts'][0]['fname'];
            }elseif(count($customer['contacts'])==2){
                $fnames=$customer['contacts'][0]['fname']." and ".$customer['contacts'][1]['fname'];
            }elseif (count($customer['contacts'])==3) {
                $fnames=$customer['contacts'][0]['fname'].", ".$customer['contacts'][1]['fname']." and ".$customer['contacts'][2]['fname'];
            }


            $email=[];

            foreach ($customer['contacts'] as $contact) {
                foreach ($contact['emails'] as $contact_email) {
                    if(!is_null($contact_email['email'])){
                        array_push($email,$contact_email['email']);
                    }
                }
            } 
            
            if($sendFlag){
                //$customerName="Sanay Patel";
                    $this->getJobs($customerName,$email,$agent,$accessToken,$mondayURL,$fnames); 
            }
             
        }  
        return back()->with('success', 'Emails Sent successfully');
    }

    public function getJobs($customerName,$email,$agent,$accessToken,$mondayURL,$fnames){
    
    $lte=Carbon::now()->addDays(30)->toDateString(); 
    $gte=Carbon::now()->toDateString();
    
    $url="https://api.servicefusion.com/v1/jobs?filters[customer_name]=$customerName&filters[start_date][lte]=$lte&filters[start_date][gte]=$gte&access_token=$accessToken&filters[status]=Scheduled&sort=start_date";
    $response = json_decode(Http::get($url), true);    
    $jobs=$response && $response['items'] ? $response['items'] : [];

    $returnVisit_Url="https://api.servicefusion.com/v1/jobs?filters[customer_name]=$customerName&filters[status]=Scheduled, Partially Completed, Started&access_token=$accessToken&filters[start_date][lte]=$gte&expand=visits";
    $returnVisit_response = json_decode(Http::get($returnVisit_Url), true);
    $returnVisit_jobs=$returnVisit_response && $returnVisit_response['items'] ? $returnVisit_response['items'] : [];
   
    $jobs_new=[];
    foreach($jobs as $job) 
    { 
        if(!str_contains(strtolower($job['description']), 'credit')){
            array_push($jobs_new,$job);
        }
    }

    foreach($returnVisit_jobs as $job) { 
            if(!empty($job['visits'])){
                foreach($job['visits'] as $visit){
                    if(Carbon::parse($visit['start_date']) >= $gte && Carbon::parse($visit['start_date']) < $lte)
                    {
                        $job['start_date']=$visit['start_date'];
                        $job['time_frame_promised_start']=$visit['time_frame_promised_start'];
                        $job['time_frame_promised_end']=$visit['time_frame_promised_end'];
                        $job['is_return_visit']=true;
                        array_push($jobs_new,$job);
                    }
                }
            }
       }

    $this->getEstimates($customerName,$email,$jobs_new,$agent,$accessToken,$mondayURL,$fnames);

}

    public function getEstimates($customerName,$email,$jobs,$agent,$accessToken,$mondayURL,$fnames){
        $url="https://api.servicefusion.com/v1/estimates?filters[customer_name]=$customerName&filters[status]=Estimate Provided&access_token=$accessToken&expand=printable_work_order";
        $response = json_decode(Http::get($url), true); 
        $estimates=$response && $response['items'] ? $response['items'] : [];
        $estimates_new=[];
        foreach($estimates as $estimate) 
        { 
            if(Carbon::parse($estimate['created_at'])->gt(Carbon::now()->subDays(365))){
                $printWithRates='-';
                foreach ($estimate['printable_work_order'] as $printRate) {
                    if($printRate['name']=='Print With Rates'){
                        $printWithRates=$printRate['url'];
                    }
                }
                $estimate['printWithRates']=$printWithRates;
                array_push($estimates_new,$estimate);
            }   
        }
        
        $this->sendEmail($customerName,$email,$jobs,$agent,$estimates_new,$mondayURL,$fnames);
    }

  

    public function sendEmail($customerName,$email,$jobs,$agent,$estimates,$mondayURL,$fnames){
        $agentEmail='';
        $serviceEmail='';
        if(strcasecmp($agent,"Brian Furnas")==0){
            $agentEmail="brian@exhaleathome.com";
            $serviceEmail="eboni@exhaleathome.com";
        }else{
            $agentEmail="bill@exhaleathome.com";
            $serviceEmail="phoebe@exhaleathome.com";
        }

        //For Live Temp
        if(strcasecmp($agent,"Brian Furnas")==0){
            Mail::mailer('smtp')->to($email)->bcc(['kinjal@exhaleathome.com',$agentEmail,$serviceEmail])->send(new sendFusionData($customerName,$jobs,$estimates,$agent,$mondayURL,$fnames,$agentEmail));    
        }else{
            Mail::mailer('smtp')->to($email)->bcc(['kinjal@exhaleathome.com',$agentEmail,$serviceEmail])->send(new sendFusionData($customerName,$jobs,$estimates,$agent,$mondayURL,$fnames,$agentEmail));    
        }

    }
}
 