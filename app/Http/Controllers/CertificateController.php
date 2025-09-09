<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;
use Barryvdh\DomPDF\Facade\Pdf;

class CertificateController extends Controller
{
    public function preview()
    {
        $user = Auth::user();
        $templatePath = public_path('image/template.JPG');

        // Buat gambar dari template
        $image = Image::make($templatePath);

        // Tambahkan nama user ke gambar
        $image->text($user->name, 600, 400, function ($font) {
            $font->file(public_path('fonts/arial.ttf'));
            $font->size(48);
            $font->color('#000000');
            $font->align('center');
            $font->valign('middle');
        });

        return $image->response('jpg');
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
        $image = Image::make($templatePath);

        // Tambahkan nama user ke gambar
        $image->text($user->name, 600, 400, function ($font) {
            $font->file(public_path('fonts/arial.ttf'));
            $font->size(48);
            $font->color('#000000');
            $font->align('center');
            $font->valign('middle');
        });

        $filename = 'sertifikat_' . $user->id . '.jpg';

        return $image->download($filename);
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
