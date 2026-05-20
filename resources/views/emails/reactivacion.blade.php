@extends('emails.layout')

@section('contenido')
    <h2 style="color: #27ae60;">Cuenta Reactivada</h2>

    <p>Hola, <strong>{{ $nombre }}</strong>.</p>
    <p>
        Tu cuenta en el <strong>Sistema de Portafolios Digitales</strong> ha sido
        <strong style="color: #27ae60;">reactivada</strong>. Ya puedes acceder
        a la plataforma y a tu portafolio con normalidad; 
        <a href="{{ env('FRONTEND_URL') }}" style="color: #2a9fd6;">ir a la plataforma</a>.
    </p>

    <p>
        Si tienes alguna consulta, escríbenos a:
        <a href="mailto:goat.group.company@gmail.com" style="color: #2a9fd6;">
            goat.group.company@gmail.com
        </a>
    </p>
@endsection