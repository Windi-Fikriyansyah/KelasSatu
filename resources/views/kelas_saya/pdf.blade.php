<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Sertifikat - {{ $user->name }}</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #FFFFFF;
            font-family: Arial, sans-serif;
        }

        .certificate-container {
            position: relative;
            width: 100%;
            height: 842px;
            /* A4 portrait height in px at 72dpi */
            /* Jika landscape, gunakan 595px height */
        }

        .certificate-background {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url("{{ $template }}");
            background-size: contain;
            /* cover agar sesuai halaman */
            background-repeat: no-repeat;
            background-position: center;
        }

        .certificate-name {
            position: absolute;
            top: 45%;
            /* vertical center */
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 36px;
            font-weight: bold;
            color: #000000;
            text-align: center;
            width: 80%;
        }

        .certificate-date {
            position: absolute;
            bottom: 10%;
            left: 50%;
            transform: translateX(-50%);
            font-size: 16px;
            color: #000000;
            text-align: center;
        }

        .certificate-id {
            position: absolute;
            bottom: 5%;
            left: 50%;
            transform: translateX(-50%);
            font-size: 12px;
            color: #666666;
            text-align: center;
        }
    </style>

</head>

<body>
    <div class="certificate-container">
        <div class="certificate-background"></div>
        <div class="certificate-name">{{ $user->name }}</div>
    </div>
</body>

</html>
