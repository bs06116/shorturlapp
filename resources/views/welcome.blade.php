<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Short URL</title>

      
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">

        <style>
            body {
                font-family: 'Nunito', sans-serif;
            }
        </style>

    </head>
    <body>
    <div id="app">
        <short-url-component></short-url-component>
    </div>
    <script src="{{ mix('/js/app.js') }}"></script> 
    </body>
</html>
