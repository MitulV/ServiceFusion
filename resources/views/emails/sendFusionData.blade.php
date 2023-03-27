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
    
        
    <div style="border-bottom: 1px solid black;padding: 0px;">
        <b class="cmnfont" style="font-size:20px;">FEEDBACK</b>
    </div>
        
    <p class="cmnfont" style="margin-top: 0px;">We’d love to know how we did this week and your feedback is crucial to our improvement: <a href="https://www.surveymonkey.com/r/DDX98WM">Exhale Feedback</a></p>
     
    <div style="border-bottom: 1px solid black;padding: 0px;">
        <b class="cmnfont" style="font-size:20px;">ON DEMAND PROJECTS</b>
    </div>
    <p class="cmnfont" style="margin-top: 0px;margin-bottom: 0px">
        The link below provides information on the status of your outstanding on demand projects. You can also bookmark this link and be able to reference it anytime – we will keep it updated throughout the week.
    </p>   
    
    <p class="cmnfont" style="margin-top: 0px;"><a href="{{$mondayURL}}" target="_blank">On Demand Projects</a></p>
    
    <div style="border-bottom: 1px solid black;padding: 0px;">
        <b class="cmnfont" style="font-size:20px;">SCHEDULED SERVICES</b>
    </div>
     <p class="cmnfont" style="margin-top: 0px;">Below is a summary of your scheduled services over the next month.
        If we add any services, we will be sure to inform you. You will also receive an email reminder 48 hours prior to all scheduled services. Please note that pool maintenance and landscaping services, if applicable, are not listed, as those dates are tentative and may shift depending on weather and schedules. 
    </p>

    @if (empty($jobs))
        <h3 class="cmnfont" style="color: red;">We currently do not have any jobs scheduled for next 30 days.</h3>
    @else
        @foreach ($jobs as $job)
        <div>
                <b class="cmnfont"><u>{{Carbon\Carbon::parse($job['start_date'])->format('l, F d, Y') }}</u></b><br>
                @if ($job['time_frame_promised_start']!=null && $job['time_frame_promised_end']!=null)
                    <span class="cmnfont">Arrival Time Window: {{Carbon\Carbon::parse($job['time_frame_promised_start'])->format('g:i A')}} to {{Carbon\Carbon::parse($job['time_frame_promised_end'])->format('g:i A')}}</span><br>    
                @else
                    <span class="cmnfont">Arrival Time Window: -</span>
                @endif
                <span class="cmnfont">{{$job['description']}}</span><br><br>

                @foreach ($job['visits'] as $visit)
                    <div>
                        <b class="cmnfont"><u>{{Carbon\Carbon::parse($visit['start_date'])->format('l, F d, Y') }}</u></b><br>
                        <b class="cmnfont">This is a return visit</b><br>
                        @if ($visit['time_frame_promised_start']!=null && $visit['time_frame_promised_end']!=null)
                            <span class="cmnfont">Arrival Time Window: {{Carbon\Carbon::parse($visit['time_frame_promised_start'])->format('g:i A')}} to {{Carbon\Carbon::parse($visit['time_frame_promised_end'])->format('g:i A')}}</span><br>    
                        @else
                            <span class="cmnfont">Arrival Time Window: -</span>
                        @endif
                        <span class="cmnfont">{{$visit['notes_for_techs']}}</span><br>
                    </div><br>
                @endforeach

        </div><br>
        @endforeach
        
    @endif
    <br>
     
    <div style="border-bottom: 1px solid black;padding: 0px;">
        <b class="cmnfont" style="font-size:20px;">OPEN ESTIMATES</b>
    </div>
    <p class="cmnfont" style="margin-top: 0px;">Please find below a summary of your open estimates at this time.</p>

    @if (empty($estimates))
        <h3 class="cmnfont" style="color: red;">There are no open estimates at this time.</h3>
    @else
        @foreach ($estimates as $estimate)
            <div>
            <b class="cmnfont">{{$estimate['description']}}</b><br>
            Value: ${{number_format($estimate['total'],2)}}<br>
            Status: {{$estimate['status']}}
            </div><br>
        @endforeach
    @endif  
        
    <p class="cmnfont">As always, we appreciate and value your business.  Have a wonderful weekend.</p>
    
    <span class="cmnfontwithStyle">Best Regards,</span><br>
    @if (strcasecmp($agent,"Brian Furnas")==0)
        <b class="cmnfont">{{strtolower($agent)}}</b><br>
        <b class="cmnfont">Home Manager</b><br>
        <span class="cmnfont">Mobile: 919.332.3564</span><br>
        <b class="cmnfont"><a href="mailto:brian@exhaleathome.com">brian@exhaleathome.com</a></b><br>
        <b class="cmnfont"><a href="https://exhaleathome.com/">exhaleathome.com</a></b><br>
    @else
        <b class="cmnfont">{{strtolower($agent)}}</b><br>
        <b class="cmnfont">Home Manager</b><br>
        <span class="cmnfont">Mobile: 336.705.4865</span><br>
        <b class="cmnfont"><a href="mailto:phoebe@exhaleathome.com">phoebe@exhaleathome.com</a></b><br>
        <b class="cmnfont"><a href="https://exhaleathome.com">exhaleathome.com</a></b><br>
    @endif
</body>
</html>