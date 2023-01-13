<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Address;

class sendFusionData extends Mailable
{
    use Queueable, SerializesModels;
    protected $customerName,$jobs,$estimates,$agent,$mondayURL,$fnames,$agentEmail;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($customerName,$jobs,$estimates,$agent,$mondayURL,$fnames,$agentEmail)
    {
        $this->customerName = $customerName;
        $this->jobs = $jobs;
        $this->estimates = $estimates;
        $this->agent = $agent;
        $this->mondayURL = $mondayURL;
        $this->fnames = $fnames;
        $this->agentEmail=$agentEmail;
    }


    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: 'Weekly Exhale Update: Upcoming Scheduled Services and Open Estimates',
            from: new Address($this->agentEmail, $this->agent),
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            view: 'emails.sendFusionData',
            with: [
                'customerName' => $this->customerName,
                'jobs'=> $this->jobs,
                'estimates'=>$this->estimates,
                'agent'=> $this->agent,
                'mondayURL'=>$this->mondayURL,
                'fnames'=>$this->fnames
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }
}
