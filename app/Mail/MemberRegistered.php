<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class MemberRegistered extends Mailable
{
    use Queueable, SerializesModels;

    public $member_name;
    public $member_email;
    public $member_company;
    public $member_phone;
    public $member_website;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($member_name, $member_email, $member_company, $member_phone, $member_website)
    {
        $this->member_name = $member_name;
        $this->member_email = $member_email;
        $this->member_company = $member_company;
        $this->member_phone = $member_phone;
        $this->member_website = $member_website;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.member-registered')
            ->subject("New OZM Member Registration")
            ->with([
                'member_name'    => $this->member_name,
                'member_email'   => $this->member_email,
                'member_company' => $this->member_company, 
                'member_phone'   => $this->member_phone,
                'member_website' => $this->member_website
            ]);
    }
}
