<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            font-family: Arial, sans-serif;
        }
        .wrapper {
            max-width: 600px;
            margin: 40px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .header {
            background-color: #1a3a5c;
            padding: 24px 32px;
            display: flex;
            align-items: center;
        }
        .header h1 {
            color: #ffffff;
            font-size: 20px;
            margin: 0;
        }
        .content {
            padding: 32px;
            color: #333333;
        }
        .content h2 {
            color: #1a3a5c;
            margin-top: 0;
        }
        .btn {
            display: inline-block;
            margin-top: 24px;
            padding: 12px 28px;
            background-color: #2a9fd6;
            color: #ffffff;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
        }
        .footer {
            background-color: #f0f0f0;
            padding: 16px 32px;
            text-align: center;
            font-size: 12px;
            color: #888888;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <h1>Sistema de Portafolios Digitales</h1>
        </div>
        <div class="content">
            @yield('contenido')
        </div>
        <div class="footer">
            © {{ date('Y') }} Portafolio de Profesionales GOAT. Todos los derechos reservados.
        </div>
    </div>
</body>
</html>