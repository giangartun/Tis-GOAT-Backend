<?php

namespace Tests\Feature;

use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Mockery;
use Tests\TestCase;

class UsuarioControllerVerificationTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_verificar_email_crea_usuario_y_portafolio_sin_error(): void
    {
        $token = 'token-test';
        $email = 'usuario@example.com';

        $usuarioMock = new \stdClass();
        $usuarioMock->id_usuario = 'U-1';
        $usuarioMock->nombre = 'Ana';
        $usuarioMock->apellido_paterno = 'Pérez';
        $usuarioMock->apellido_materno = 'Lopez';
        $usuarioMock->email = $email;
        $usuarioMock->tipo_usuario = 'usuario';
        $usuarioMock->estado_cuenta = 'activo';
        $usuarioMock->fecha = now();

        $portafolioMock = new \stdClass();
        $portafolioMock->id_portafolio = 'P-1';
        $portafolioMock->enlace_pagi_web = 'https://frontend.test/P-1/ana-perez';
        $portafolioMock->visible = true;

        Cache::shouldReceive('get')->with('token_' . $token)->andReturn($email);
        Cache::shouldReceive('get')->with('registro_' . $email)->andReturn([
            'token' => $token,
            'email' => $email,
            'contrasena' => 'hashed-password',
            'nombre' => 'Ana',
            'apellido_paterno' => 'Pérez',
            'apellido_materno' => 'Lopez',
            'tipo_usuario' => 'usuario',
            'estado_cuenta' => 'activo',
        ]);
        Cache::shouldReceive('forget')->with('registro_' . $email);
        Cache::shouldReceive('forget')->with('token_' . $token);

        $usuarioAlias = Mockery::mock('alias:App\\Models\\Usuario');
        $usuarioAlias->shouldReceive('create')->andReturn($usuarioMock);

        $portafolioAlias = Mockery::mock('alias:App\\Models\\Portafolio');
        $portafolioAlias->shouldReceive('create')->andReturn($portafolioMock);

        $registroHelperAlias = Mockery::mock('alias:App\\Helpers\\RegistroActividadHelper');
        $registroHelperAlias->shouldReceive('registrar')->once();

        DB::shouldReceive('beginTransaction')->once();
        DB::shouldReceive('commit')->once();
        DB::shouldReceive('rollBack')->never();

        $controller = new UsuarioController();

        $response = $controller->verificarEmail($token);

        $this->assertSame(201, $response->getStatusCode());
    }
}
