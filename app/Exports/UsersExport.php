<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UsersExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        // Hanya menampilkan pengguna dengan role user
        return User::where('role', 'user')
            ->select('name', 'no_hp', 'email', 'alamat', 'kabupaten', 'provinsi', 'instansi', 'profesi')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Nama',
            'No HP',
            'Email',
            'Alamat',
            'Kabupaten',
            'Provinsi',
            'Instansi',
            'Profesi'
        ];
    }
}
