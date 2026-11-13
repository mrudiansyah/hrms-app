<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CandidateEmailVerification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $candidateName,
        public string $verificationUrl
    ) {
    }

    public function build()
    {
        return $this->subject('Verifikasi email kandidat')
            ->view('emails.candidate_verification');
    }
}
