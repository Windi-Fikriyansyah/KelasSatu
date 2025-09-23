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
            /* A4 portrait */
        }

        .certificate-background {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url("{{ $template }}");
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center;
        }

        .certificate-name {
            position: absolute;
            top: 45%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 36px;
            font-weight: bold;
            color: #000000;
            text-align: center;
            width: 80%;
        }

        .certificate-number {
            position: absolute;
            top: 28%;
            /* sedikit di bawah nama */
            left: 52%;
            transform: translateX(-50%);
            font-size: 20px;
            font-weight: bold;
            color: #000000;
            text-align: center;
        }

        .purchaseDate {
            position: absolute;
            top: 66.1%;
            /* sedikit di bawah nama */
            left: 61.3%;
            transform: translateX(-50%);
            font-size: 23px;
            font-weight: bold;
            color: #000000;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="certificate-container">
        <div class="certificate-background"></div>
        <div class="certificate-name">{{ $user->name }}</div>
        <div class="certificate-number">{{ $certificateNumber }}</div>
        <div class="purchaseDate">{{ $purchaseDate }}</div>
    </div>
</body>

</html>
