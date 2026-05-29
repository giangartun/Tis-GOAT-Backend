<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RecuperarPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    // Definimos estas variables como públicas para que la vista (.blade.php) pueda usarlas
    public $token;
    public $url;

    /**
     * Create a new message instance.
     */
    public function __construct($token)
    {
        $this->token = $token;
        $this->url = rtrim(env('FRONTEND_URL'), '/') . '/reset-password?token=' . $token;
    }

    /**
     * Get the message envelope (El "sobre" del correo: asunto)
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Recuperación de Contraseña - Portafolio Profesional',
        );
    }

    /**
     * Get the message content definition (El "cuerpo" del correo)
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.recuperar_password', // Aquí indicamos la vista que creamos antes
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}