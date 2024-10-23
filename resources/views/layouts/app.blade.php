<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Traduttore Italiano - CAA per Bambini</title>
        <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
        <style>
            body {
                font-family: 'Nunito', sans-serif;
                background-color: #f0f8ff;
                color: #333;
            }
            .container {
                max-width: 800px;
                margin: 0 auto;
                padding: 20px;
            }
            h1 {
                color: #4a86e8;
                text-align: center;
                font-size: 2.5rem;
                margin-bottom: 30px;
            }
            .form-group {
                margin-bottom: 20px;
            }
            label {
                font-size: 1.2rem;
                color: #4a86e8;
                display: block;
                margin-bottom: 5px;
            }
            textarea {
                width: 100%;
                padding: 10px;
                border: 2px solid #4a86e8;
                border-radius: 10px;
                font-size: 1rem;
            }
            button {
                background-color: #4a86e8;
                color: white;
                border: none;
                padding: 10px 20px;
                font-size: 1rem;
                border-radius: 20px;
                cursor: pointer;
                transition: background-color 0.3s;
            }
            button:hover {
                background-color: #3a76d8;
            }
            #risultatoTraduzione {
                margin-top: 30px;
                display: flex;
                flex-wrap: wrap;
                justify-content: center;
            }
            #risultatoTraduzione img {
                margin: 10px;
                border: 3px solid #4a86e8;
                border-radius: 10px;
                transition: transform 0.3s;
            }
            #risultatoTraduzione img:hover {
                transform: scale(1.1);
            }
        </style>
    </head>
    <body>
        <div class="container">
            @yield('content')
        </div>
        
        @stack('scripts')
    </body>
</html>
