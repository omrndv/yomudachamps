<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Yomuda Championship')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="{{ asset('images/logo-yomuda.png') }}">

    <style>
        html,
        body {
            height: 100%;
            margin: 0;
            padding: 0;
            background-color: #121417;
        }

        body {
            font-family: 'Poppins', sans-serif;
            display: flex;
            flex-direction: column;
            background-image: url('/images/bg-yomuda.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            min-height: 158vh;
        }

        main {
            flex: 1 0 auto;
            display: flex;
            flex-direction: column;
            width: 100%;
            padding: 80px 20px 100px 20px;
        }

        .content-wrapper {
            margin: auto;
            width: 100%;
            display: flex;
            justify-content: center;
        }

        footer {
            flex-shrink: 0;
            width: 100%;
        }

        @media (max-width: 768px) {
            body {
                background-image: url('/images/bg-mobile.jpg');
                background-size: cover;
                background-position: center;
                background-repeat: no-repeat;
                background-attachment: fixed;
            }

            main {
                padding: 60px 10px 80px 10px;
            }
        }
    </style>
</head>

<body>
    <main>
        <div class="content-wrapper">
            @yield('content')
        </div>
    </main>

    @include('components.footer')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>