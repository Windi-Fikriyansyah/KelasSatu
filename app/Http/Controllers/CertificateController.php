<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
// Atau jika menggunakan Imagick:
// use Intervention\Image\Drivers\Imagick\Driver;
use Barryvdh\DomPDF\Facade\Pdf;

class CertificateController extends Controller
{
    public function preview()
    {
        $user = Auth::user();
        $templatePath = public_path('image/template.JPG');

        // Untuk Intervention Image v3 (Laravel 10+)
        $manager = new ImageManager(new Driver());
        $image = $manager->read($templatePath);

        // Tambahkan nama user ke gambar
        $image->text($user->name, 600, 400, function ($font) {
            $font->file(public_path('fonts/arial.ttf'));
            $font->size(48);
            $font->color('#000000');
            $font->align('center');
            $font->valign('middle');
        });

        return response($image->toJpeg(), 200)
            ->header('Content-Type', 'image/jpeg');
    }

    public function download(Request $request)
    {
        $user = Auth::user();
        $format = $request->get('format', 'pdf');

        if ($format === 'image') {
            return $this->downloadImage($user);
        }

        return $this->downloadPDF($user);
    }

    private function downloadImage($user)
    {
        $templatePath = public_path('image/template.JPG');

        if (!file_exists($templatePath)) {
            return response()->json(['error' => 'Template not found at: ' . $templatePath], 404);
        }

        try {
            // Inisialisasi ImageManager langsung di method
            $imageManager = new ImageManager(new Driver());
            $image = $imageManager->read($templatePath);

            // Tambahkan nama user ke gambar
            $image->text($user->name, 800, 500, function ($font) {
                $fontPath = public_path('fonts/arialbd.ttf');
                if (file_exists($fontPath)) {
                    $font->filename($fontPath);
                }
                $font->size(50);
                $font->color('#000000');
                $font->align('center');
                $font->valign('middle');
            });

            $filename = 'sertifikat_' . $user->id . '.jpg';

            return response($image->toJpeg(), 200)
                ->header('Content-Type', 'image/jpeg')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    private function downloadPDF($user)
    {
        $templatePath = public_path('image/template.JPG');

        list($width, $height) = getimagesize($templatePath);
        $orientation = $width > $height ? 'landscape' : 'portrait';

        $data = [
            'user' => $user,
            'template' => $templatePath,
            'certificateId' => 'CERT-' . $user->id . '-' . time(),
        ];

        $pdf = PDF::loadView('kelas_saya.pdf', $data);
        $pdf->setPaper('a4', $orientation);
        $pdf->setOption('dpi', 150);
        $pdf->setOption('defaultFont', 'Arial');

        // Pastikan tidak ada margin yang menyebabkan konten terpotong
        $pdf->setOption('margin-top', 0);
        $pdf->setOption('margin-right', 0);
        $pdf->setOption('margin-bottom', 0);
        $pdf->setOption('margin-left', 0);

        $filename = 'sertifikat_' . $user->id . '.pdf';

        return $pdf->download($filename);
    }
}
