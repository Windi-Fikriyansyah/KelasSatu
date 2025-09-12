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
    private function monthToRoman($month)
    {
        $romans = [
            1 => 'I',
            2 => 'II',
            3 => 'III',
            4 => 'IV',
            5 => 'V',
            6 => 'VI',
            7 => 'VII',
            8 => 'VIII',
            9 => 'IX',
            10 => 'X',
            11 => 'XI',
            12 => 'XII'
        ];
        return $romans[$month] ?? '';
    }

    private function generateCertificateNumber($userId)
    {
        $nomorUrut = str_pad($userId, 3, '0', STR_PAD_LEFT);
        $kode = 'TKA'; // kode tetap
        $bulanRomawi = $this->monthToRoman(date('n'));
        $tahun = date('Y');

        return "{$nomorUrut}/{$kode}/{$bulanRomawi}/{$tahun}";
    }

    public function preview()
    {
        $user = Auth::user();
        $templatePath = public_path('image/template.jpg');

        $manager = new ImageManager(new Driver());
        $image = $manager->read($templatePath);

        // Tambahkan nama user
        $image->text($user->name, 600, 400, function ($font) {
            $font->file(public_path('fonts/arial.ttf'));
            $font->size(48);
            $font->color('#000000');
            $font->align('center');
            $font->valign('middle');
        });

        // Tambahkan nomor sertifikat
        $certificateNumber = $this->generateCertificateNumber($user->id);
        $image->text($certificateNumber, 600, 470, function ($font) {
            $font->file(public_path('fonts/arial.ttf'));
            $font->size(32);
            $font->color('#000000');
            $font->align('center');
            $font->valign('middle');
        });

        return response($image->toJpeg(), 200)
            ->header('Content-Type', 'image/jpeg');
    }

    private function downloadImage($user)
    {
        $templatePath = public_path('image/template.jpg');

        if (!file_exists($templatePath)) {
            return response()->json(['error' => 'Template not found at: ' . $templatePath], 404);
        }

        try {
            $imageManager = new ImageManager(new Driver());
            $image = $imageManager->read($templatePath);

            // Tambahkan nama user
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

            // Tambahkan nomor sertifikat
            $certificateNumber = $this->generateCertificateNumber($user->id);
            $image->text($certificateNumber, 850, 335, function ($font) {
                $fontPath = public_path('fonts/arialbd.ttf');
                if (file_exists($fontPath)) {
                    $font->filename($fontPath);
                }
                $font->size(30);
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

    public function download(Request $request)
    {
        $user = Auth::user();
        $format = $request->get('format', 'pdf');

        if ($format === 'image') {
            return $this->downloadImage($user);
        }

        return $this->downloadPDF($user);
    }



    private function downloadPDF($user)
    {
        $templatePath = public_path('image/template.jpg');

        list($width, $height) = getimagesize($templatePath);
        $orientation = $width > $height ? 'landscape' : 'portrait';

        // Generate nomor sertifikat
        $certificateNumber = $this->generateCertificateNumber($user->id);

        $data = [
            'user' => $user,
            'template' => $templatePath,
            'certificateNumber' => $certificateNumber,
        ];

        $pdf = PDF::loadView('kelas_saya.pdf', $data);
        $pdf->setPaper('a4', $orientation);
        $pdf->setOption('dpi', 150);
        $pdf->setOption('defaultFont', 'Arial');

        // Hilangkan margin biar tidak terpotong
        $pdf->setOption('margin-top', 0);
        $pdf->setOption('margin-right', 0);
        $pdf->setOption('margin-bottom', 0);
        $pdf->setOption('margin-left', 0);

        $filename = 'sertifikat_' . $user->id . '.pdf';

        return $pdf->download($filename);
    }
}
