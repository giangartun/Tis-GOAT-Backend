<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReactivacionCuenta extends Mailable
{
    use Queueable, SerializesModels;

    public string $nombre;

    public function __construct(string $nombre)
    {
        $this->nombre = $nombre;
    }

    public function build(): self
    {
        return $this->subject('Tu cuenta ha sido reactivada')
                    ->view('emails.reactivacion');
    }
}