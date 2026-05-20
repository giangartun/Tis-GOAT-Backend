<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SuspensionCuenta extends Mailable
{
    use Queueable, SerializesModels;

    public string $nombre;
    public ?string $motivo;

    public function __construct(string $nombre, ?string $motivo = null)
    {
        $this->nombre = $nombre;
        $this->motivo = $motivo;
    }

    public function build(): self
    {
        return $this->subject('Tu cuenta ha sido suspendida')
                    ->view('emails.suspension');
    }
}