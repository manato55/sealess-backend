<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReRegisterPassword extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    protected $msg;
    protected $title;
    protected $tmpl;

    public function __construct($title, $msg ,$tmpl)
    {
        $this->title = $title;
        $this->msg = $msg;
        $this->tmpl = $tmpl;
    }
    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->from('hoge@hoge.com')
            ->subject($this->title)
            ->view("mailContent.{$this->tmpl}")
            ->with([
                'msg' => $this->msg,
            ]);
    }
}
