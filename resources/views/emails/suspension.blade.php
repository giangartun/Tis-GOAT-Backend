@extends('emails.layout')

@section('contenido')
    <h2 style="color: #c0392b;">Cuenta Suspendida</h2>

    <p>Hola, <strong>{{ $nombre }}</strong>.</p>
    <p>
        Tu cuenta en el <strong>Sistema de Portafolios Digitales</strong> ha sido
        <strong style="color: #c0392b;">suspendida</strong>. Mientras tanto, no podrás
        acceder a la plataforma ni a tu portafolio registrado.
    </p>
    <p>
        Si crees que esto es un error, comunícate con soporte en:
        <a href="mailto:goat.group.company@gmail.com" style="color: #2a9fd6;">
            goat.group.company@gmail.com
        </a>
    </p>
@endsection