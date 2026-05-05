<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
use Illuminate\Support\Facades\Log;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class FotoPerfilController extends Controller
{
    public function subir(Request $request)
    {
        $request->validate([
            'foto' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $usuario = $request->user();

        try {
            $file = $request->file('foto');

            if (!$file->isValid()) {
                return response()->json(['message' => 'Archivo inválido'], 400);
            }

            // Eliminar foto anterior de Cloudinary si existe
            if ($usuario->foto) {
                $publicId = $this->extraerPublicId($usuario->foto);
                if ($publicId) {
                    Cloudinary::uploadApi()->destroy($publicId, [
                        'resource_type' => 'image'
                    ]);
                }
            }

            // Subir nueva foto
            $upload = Cloudinary::uploadApi()->upload(
                $file->getRealPath(),
                [
                    'folder'         => 'portafolios/fotos_perfil',
                    'public_id'      => 'usuario_' . $usuario->id_usuario,
                    'overwrite'      => true,
                    'resource_type'  => 'image',
                    'transformation' => [
                        'width'   => 400,
                        'height'  => 400,
                        'crop'    => 'fill',
                        'gravity' => 'face',
                    ]
                ]
            );

            $url = $upload['secure_url'];

            // Guardar URL en la base de datos
            $usuario->foto = $url;
            $usuario->save();

            return response()->json([
                'message'  => 'Foto actualizada correctamente',
                'foto_url' => $url,
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error subiendo foto: ' . $e->getMessage());
            return response()->json([
                'message' => 'Error al subir la foto',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function eliminar(Request $request)
    {
        $usuario = $request->user();

        if (!$usuario->foto) {
            return response()->json([
                'message' => 'No tienes foto de perfil'
            ], 404);
        }

        try {
            $publicId = $this->extraerPublicId($usuario->foto);
            if ($publicId) {
                Cloudinary::uploadApi()->destroy($publicId, [
                    'resource_type' => 'image'
                ]);
            }

            $usuario->foto = null;
            $usuario->save();

            return response()->json([
                'message' => 'Foto eliminada correctamente'
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error eliminando foto: ' . $e->getMessage());
            return response()->json([
                'message' => 'Error al eliminar la foto',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    private function extraerPublicId(string $url): ?string
    {
        $path = parse_url($url, PHP_URL_PATH);
        preg_match('/\/upload\/(?:v\d+\/)?(.+)\.[^.]+$/', $path, $matches);
        return $matches[1] ?? null;
    }
}