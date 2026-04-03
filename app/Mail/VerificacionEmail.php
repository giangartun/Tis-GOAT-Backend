<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VerificacionEmail extends Mailable
{
    use Queueable, SerializesModels;

    public string $token;
    public string $nombre;

    public function __construct(string $token, string $nombre)
    {
        $this->token  = $token;
        $this->nombre = $nombre;
    }

    public function build(): self
    {
        return $this->subject('Verifica tu correo')
                    ->view('emails.verificacion');
    }
}
