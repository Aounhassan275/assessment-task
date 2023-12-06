<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PayoutEmail extends Mailable
{
    use Queueable, SerializesModels;

    protected $amount;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(
         $amount
    ) {
        $this->amount = $amount;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('email.payout')->subject('Payout Send')
        ->with([
            'amount' => $this->amount
        ]);
    }
}
