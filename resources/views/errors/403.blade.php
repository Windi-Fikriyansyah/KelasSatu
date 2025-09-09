<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Akses Ditolak</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-50 flex items-center justify-center h-screen">
    <div class="text-center max-w-md p-6 bg-white rounded-xl shadow-lg">
        <h1 class="text-8xl font-extrabold text-red-500 mb-4">403</h1>
        <h2 class="text-2xl font-semibold mb-2">Akses Ditolak</h2>
        <p class="text-gray-600 mb-6">Maaf, Anda tidak memiliki izin untuk mengakses halaman ini.</p>
        <a href="{{ url('/') }}"
            class="inline-block bg-blue-600 text-white px-6 py-2 rounded-lg shadow hover:bg-blue-700 transition">
            Kembali ke Home
        </a>
    </div>
</body>

</html>
