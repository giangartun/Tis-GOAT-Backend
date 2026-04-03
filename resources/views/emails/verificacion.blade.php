@extends('emails.layout')

@section('contenido')
    <h2>Hola, {{ $nombre }} 😁</h2>
    <p>Gracias por registrarte. Para completar tu registro verifica tu correo haciendo clic en el botón:</p>
    <a href="{{ config('app.url') }}/api/usuario/verificar-email/{{ $token }}" class="btn">
        Verificar mi correo
    </a>
    <p style="margin-top: 24px; font-size: 13px; color: #888;">
        Este enlace expira en <strong>5 minutos</strong>. Si no solicitaste este registro, ignora este correo.
    </p>
@endsection