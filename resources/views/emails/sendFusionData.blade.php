<!DOCTYPE html>
<html>
<head>
    <title>Service Fusion</title>
</head>
<body>
    <p>
        <h2>Hello {{$fnames}},</h2>
    </p>

    <p>Please find below your Exhale weekly service update.</p>
        
    <h3>FEEDBACK</h3>
        
    <p>We’d love to know how we did this week: Exhale Feedback</p>
        
    <h3>ON DEMAND PROJECTS</h3>
        
    <p>
        The link below provides information on the status of your outstanding on demand projects. Please let us know if you have any questions, input, or additional projects you’d like for us to add. You can also bookmark this link and be able to reference it anytime – we will keep it updated throughout the week.
    </p>   
    
    <h3><a href="{{$mondayURL}}" target="_blank">On Demand Projects</a></h3>
        
      <h3>SCHEDULED SERVICES</h3>
        
        <p>Below is a summary of your scheduled services over the next month. If
        we add any services we will be sure to inform you. You will also
        receive an email reminder 48 hours prior to all scheduled services.
        Please note that pool maintenance and landscaping services, if
        applicable, are not listed, as those dates are tentative and may shift
        depending on weather and schedules.
        </p><br><br>

        @if (empty($jobs))
            <h3>We currently do not have any jobs scheduled for next 30 days.</h3>
        @else

        @foreach ($jobs as $job)
        {{$loop->iteration}}) {{$job['description']}}<br>
        Date: {{Carbon\Carbon::parse($job['start_date'])->format('m-d-Y') }}<br>
        {{-- Tech Assigned : - <br> --}}
        Arrival Time Window: {{Carbon\Carbon::parse($job['time_frame_promised_start'])->format('g:i A')}} to {{Carbon\Carbon::parse($job['time_frame_promised_end'])->format('g:i A')}}<br>
        {{-- Duration : {{Carbon\CarbonInterval::seconds($job['duration'])->cascade()->forHumans()}} --}}
        <br><br>
        @endforeach
            
        @endif    
        
        <br><br><br>
        
        <h3>OPEN ESTIMATES</h3>
        
        <p>Please find below a summary of your open estimates at this time.</p>

        @if (empty($estimates))
            <h3>There are no open estimates at this time.</h3>
        @else
            
            @foreach ($estimates as $estimate)
                {{$loop->iteration}}) Description: {{$estimate['description']}}<br>
                Value: ${{number_format($estimate['total'],2)}}<br>
                View Estimate: <a href="{{$estimate['printWithRates']}}" target="_blank">{{$estimate['printWithRates']}}</a><br>
                Status: {{$estimate['status']}}<br><br>
            @endforeach

        @endif  
            
        <br>
        
        <p>As always, we appreciate and value your business.  Have a wonderful weekend.</p>
        
        <b>Best Regards,</b><br>
        {{$agent}}<br>
        Exhale
</body>
</html>