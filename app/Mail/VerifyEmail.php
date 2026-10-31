<?php

namespace App\Mail;

use Carbon\Carbon;
use App\AppConstants;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class VerifyEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    public $user;

    public function __construct($user)
    {

        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->to($this->user->email)
            ->from(config('app.noreplyEmail'))
            ->subject(config('app.siteName') . ' Verify Your Email')
            ->view('emails.users.verifyEmail')
            ->with([
                'firstname' => $this->user->firstname,
                'email' => base64_encode($this->user->email),
                'year' => Carbon::now()->year,
            ]);
    }
}
