<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Evidencia;
use Illuminate\Support\Facades\Validator;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class EvidenciaController extends Controller
{
    private function detectarTipo(string $mime): string
    {
        if (str_starts_with($mime, 'image/'))       return 'imagen';
        if (str_starts_with($mime, 'video/'))       return 'video';
        if (str_starts_with($mime, 'audio/'))       return 'audio';
        if ($mime === 'application/pdf')            return 'pdf';
        if (str_contains($mime, 'word'))            return 'documento';
        if (str_contains($mime, 'excel') || str_contains($mime, 'spreadsheet')) return 'hoja_calculo';
        if (str_contains($mime, 'powerpoint') || str_contains($mime, 'presentation')) return 'presentacion';
        return 'otro';
    }

    private function generarVistaPrevia(string $url, string $tipo): ?string
    {
        return match($tipo) {
            'imagen', 'video', 'audio', 'pdf' => $url,
            'documento', 'hoja_calculo', 'presentacion' => "https://docs.google.com/viewer?url=" . urlencode($url),
            default => null
        };
    }

    // Extrae el public_id de la URL de Cloudinary
    private function extraerPublicId(string $url): string
    {
        // URL ejemplo: https://res.cloudinary.com/cloud/image/upload/v123456/evidencias/id/archivo.pdf
        $path = parse_url($url, PHP_URL_PATH);
        // Elimina /cloud_name/resource_type/upload/vXXXXX/ y la extensión
        preg_match('/\/upload\/(?:v\d+\/)?(.+)\.[^.]+$/', $path, $matches);
        return $matches[1] ?? '';
    }

    public function subir(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'archivo'     => 'required|file|max:20480|mimes:jpg,jpeg,png,gif,webp,pdf,doc,docx,xls,xlsx,ppt,pptx,mp4,mp3,wav',
            'id_proyecto' => 'nullable|exists:proyecto,id_proyecto',
            'id_academica' => 'nullable|exists:experiencia_academica,id_experiencia_academica',
            'id_laboral'   => 'nullable|exists:experiencia_laboral,id_experiencia',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'errors'  => $validator->errors()
            ], 400);
        }

        try {
            $file   = $request->file('archivo');

            if (!$file->isValid()) {
                return response()->json(['message' => 'Archivo inválido'], 400);
            }

            $mime   = $file->getMimeType();
            $nombre = $file->getClientOriginalName();
            $size   = $file->getSize();
            $tipo   = $this->detectarTipo($mime);

            $upload = Cloudinary::uploadApi()->upload($file->getRealPath(), [
                'folder'        => 'evidencias/' . $request->id_proyecto,
                'resource_type' => 'auto',
            ]);

            $url     = $upload['secure_url'];
            $preview = $this->generarVistaPrevia($url, $tipo);

            $evidencia = Evidencia::create([
                'tipo'           => $tipo,
                'url_evidencia'  => $url,
                'nombre_archivo' => $nombre,
                'foto_url'       => $tipo === 'imagen',
                'tamano_bytes'   => $size,
                'fecha_subida'   => now(),
                'id_proyecto'    => $request->id_proyecto,
                'id_experiencia_academica' => $request->id_academica,
                'id_experiencia_laboral'   => $request->id_laboral,
            ]);

            return response()->json([
                'message'   => 'Archivo subido correctamente',
                'evidencia' => [
                    'id'          => $evidencia->id_evidencia,
                    'nombre'      => $nombre,
                    'tipo'        => $tipo,
                    'mime'        => $mime,
                    'tamano_kb'   => round($size / 1024, 2),
                    'url'         => $url,
                    'preview_url' => $preview,
                    'fecha'       => $evidencia->fecha_subida,
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al subir archivo',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function mostrar($id)
    {
        $evidencia = Evidencia::find($id);

        if (!$evidencia) {
            return response()->json(['message' => 'Archivo no encontrado'], 404);
        }

        $preview = $this->generarVistaPrevia($evidencia->url_evidencia, $evidencia->tipo);

        return response()->json([
            'evidencia' => [
                'id'          => $evidencia->id_evidencia,
                'nombre'      => $evidencia->nombre_archivo,
                'tipo'        => $evidencia->tipo,
                'tamano_kb'   => round($evidencia->tamano_bytes / 1024, 2),
                'url'         => $evidencia->url_evidencia,
                'preview_url' => $preview,
                'fecha'       => $evidencia->fecha_subida,
            ]
        ]);
    }

    private function detectarResourceType(string $tipo): string
    {
        return match($tipo) {
            'video', 'audio' => 'video',
            'documento', 'hoja_calculo', 'presentacion' => 'raw',
            default => 'image', // imagen, pdf
        };
    }

    public function eliminar($id)
    {
        try {
            $evidencia = Evidencia::find($id);

            if (!$evidencia) {
                return response()->json(['message' => 'Evidencia no encontrada'], 404);
            }

            $publicId     = $this->extraerPublicId($evidencia->url_evidencia);
            $resourceType = $this->detectarResourceType($evidencia->tipo);

            Cloudinary::uploadApi()->destroy($publicId, [
                'resource_type' => $resourceType
            ]);

            $evidencia->delete();

            return response()->json(['message' => 'Archivo eliminado correctamente']);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al eliminar archivo',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}