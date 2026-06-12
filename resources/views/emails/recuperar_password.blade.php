@extends('emails.layout')

@section('contenido')
    <h2>Hola 👋</h2>
    <p>Has solicitado restablecer tu contraseña. No te preocupes, a todos nos pasa. Haz clic en el siguiente botón para continuar:</p>
    
    <a href="{{ $url }}" class="btn">
        Restablecer Contraseña
    </a>

    <p style="margin-top: 24px; font-size: 13px; color: #888;">
        Este enlace expira en <strong>60 minutos</strong>. Si no solicitaste este cambio, puedes ignorar este correo de forma segura; tu contraseña seguirá siendo la misma.
    </p>
@endsection