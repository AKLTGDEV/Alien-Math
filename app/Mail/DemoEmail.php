<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class DemoEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The demo object instance.
     *
     * @var Demo
     */
    public $demo;
    public $mailtype;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($demo, $mailtype)
    {
        $this->demo = $demo;
        $this->mailtype = $mailtype;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if ($this->mailtype == "demo") {
            return $this->from('admin@crowdoubt.tech')
                ->view('mails.demo');
                
        } else if ($this->mailtype == "welcome") {
            return $this->from('admin@crowdoubt.tech')
                ->view('mails.welcome');
        }
    }
}
