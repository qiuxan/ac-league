<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class UserRequestReceived extends Mailable
{
    use Queueable, SerializesModels;

    public $request_full_name;    
    public $request_email;    
    public $request_subject;    
    public $request_message;    

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($request_full_name, $request_email, $request_subject, $request_message)
    {
        $this->request_full_name = $request_full_name;    
        $this->request_email = $request_email;    
        $this->request_subject = $request_subject;    
        $this->request_message = $request_message;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {        
        return $this->markdown('emails.user-request-received')
            ->subject("OZM User Request")
            ->with([
                'request_full_name' => $this->request_full_name,
                'request_email'     => $this->request_email,
                'request_subject'   => $this->request_subject,
                'request_message'   => $this->request_message
            ]);
    }
}
