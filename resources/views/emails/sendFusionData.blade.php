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
        <span class="cmnfont">Hello {{$fnames}},</span>
    </p>

    <p class="cmnfont">Please find below your Exhale weekly service update.</p>
    <br>
        
    <div style="border-bottom: 1px solid black;padding: 0px;">
        <b class="cmnfont" style="font-size:20px;">FEEDBACK</b>
    </div>
        
    <p class="cmnfont" style="margin-top: 0px;">We’d love to know how we did this week: <a href="https://www.surveymonkey.com/r/DDX98WM">Exhale Feedback</a></p>
    <br><br> 
    <div style="border-bottom: 1px solid black;padding: 0px;">
        <b class="cmnfont" style="font-size:20px;">ON DEMAND PROJECTS</b>
    </div>
        
    <p class="cmnfont" style="margin-top: 0px;margin-bottom: 0px">
        The link below provides information on the status of your outstanding on demand projects. Please let us know if you have any questions, input, or additional projects you’d like for us to add. You can also bookmark this link and be able to reference it anytime – we will keep it updated throughout the week.
    </p>   
    
    <h3 class="cmnfont" style="margin-top: 0px;"><a href="{{$mondayURL}}" target="_blank">On Demand Projects</a></h3>
    
    <br><br>
    <div style="border-bottom: 1px solid black;padding: 0px;">
        <b class="cmnfont" style="font-size:20px;">SCHEDULED SERVICES</b>
    </div>
        
        <p class="cmnfont" style="margin-top: 0px;">Below is a summary of your scheduled services over the next month. If
        we add any services we will be sure to inform you. You will also
        receive an email reminder 48 hours prior to all scheduled services.
        Please note that pool maintenance and landscaping services, if
        applicable, are not listed, as those dates are tentative and may shift
        depending on weather and schedules.
        </p><br>

        @if (empty($jobs))
            <h3 class="cmnfont" style="font-style: italic;color: red;">We currently do not have any jobs scheduled for next 30 days.</h3>
        @else

        @foreach ($jobs as $job)
        <div>
        {{-- {{$loop->iteration}})  --}}
        <b class="cmnfont"><u>{{Carbon\Carbon::parse($job['start_date'])->format('l,F d,Y') }}</u></b><br>
        {{-- Tech Assigned : - <br> --}}
        @if ($job['time_frame_promised_start']!=null && $job['time_frame_promised_end']!=null)
        <span class="cmnfont">Arrival Time Window: {{Carbon\Carbon::parse($job['time_frame_promised_start'])->format('g:i A')}} to {{Carbon\Carbon::parse($job['time_frame_promised_end'])->format('g:i A')}}</span><br>    
        @else
        <span class="cmnfont">Arrival Time Window: -</span>
        @endif
        <span class="cmnfont">{{$job['description']}}</span><br>
        {{-- Duration : {{Carbon\CarbonInterval::seconds($job['duration'])->cascade()->forHumans()}} --}}
        </div>
        @endforeach
            
        @endif    
        
        <br><br>  
        <div style="border-bottom: 1px solid black;padding: 0px;">
            <b class="cmnfont" style="font-size:20px;">OPEN ESTIMATES</b>
        </div>
        <p class="cmnfont" style="margin-top: 0px;">Please find below a summary of your open estimates at this time.</p>

        @if (empty($estimates))
            <h3 style="font-family:sans-serif;font-style: italic;color: red;">There are no open estimates at this time.</h3>
        @else
            
            @foreach ($estimates as $estimate)
                <div>
                <b class="cmnfont">{{$estimate['description']}}</b><br>
                Value: ${{number_format($estimate['total'],2)}}<br>
                <a href="{{$estimate['printWithRates']}}" target="_blank">View Estimate</a><br>
                Status: {{$estimate['status']}}<br><br>
                </div><br>
            @endforeach

        @endif  
        
        <p class="cmnfont">As always, we appreciate and value your business.  Have a wonderful weekend.</p>
        
        <span class="cmnfontwithStyle">Best Regards,</span><br><br>
        @if (strcasecmp($agent,"Brian Furnas")==0)
            <b class="cmnfontwithStyle">{{$agent}}</b><br>
            <b>Home Manager</b><br>
            <b>Phone: 919.332.3564</b><br>
            <b><a href="mailto:brian@exhaleathome.com">brian@exhaleathome.com</a></b><br>
            <b><a href="https://exhaleathome.com/"></a>https://exhaleathome.com/</b><br>
        @else
            <b class="cmnfontwithStyle">{{$agent}}</b><br>
            <b>Home Manager</b><br>
            <b>Mobile: 336.705.4865</b><br>
            <b><a href="mailto:phoebe@exhaleathome.com">phoebe@exhaleathome.com</a></b><br>
            <b><a href="https://exhaleathome.com">https://exhaleathome.com/</a></b><br>
        @endif
</body>
</html>