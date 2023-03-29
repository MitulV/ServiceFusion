<!DOCTYPE html>
<html>
<head>
    <title>Service Fusion</title>
    <style>
        .cmnfont{
            font-family: Calibri;
        }
        .cmnfontwithStyle{
            font-family: sans-serif;
            font-style: oblique
        }
    </style>
</head>
<body>
    <p>
        <span class="cmnfont">Hello,</span>
    </p>
        
    <div style="border-bottom: 1px solid black;padding: 0px;">
        <b class="cmnfont" style="font-size:20px;">Please find below Exhale Scheduled Jobs Summery.</b>
    </div>

    <div>  
        <span class="cmnfont">Total Jobs: {{$data['total_jobs']}}</span><br><br>
        <span class="cmnfont">Total Internal Jobs: {{$data['total_internal_jobs']}}</span><br>
        <span class="cmnfont">Internal Successfully scheduled jobs: {{$data['internal_successfully_scheduled_jobs']}}</span><br>
        <span class="cmnfont">Internal Failed scheduled jobs: {{$data['internal_failed_scheduled_jobs']}}</span><br><br>
        <span class="cmnfont">Total Contract Jobs: {{$data['total_contract_jobs']}}</span><br>
        <span class="cmnfont">Contract Successfully scheduled jobs: {{$data['contract_successfully_scheduled_jobs']}}</span><br>    
        <span class="cmnfont">Contract Failed scheduled jobs: {{$data['contract_failed_scheduled_jobs']}}</span><br>       
    </div><br>

    <p class="cmnfont">As always, we appreciate and value your business.  Have a wonderful weekend.</p>
    
    <span class="cmnfontwithStyle">Best Regards</span><br>
</body>
</html>