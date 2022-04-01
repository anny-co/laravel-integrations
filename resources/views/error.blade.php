<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Success</title>
    <style>
        html, body {
            font-family: sans-serif;
        }
        .flex {
            display: flex;
        }
        .justify-center {
            justify-content: center;
        }
        .align-center {
            align-items: center;
        }
        .w-full {
            width: 100vw;
        }
        .h-full {
            height: 100vh;
        }
        .text-danger {
            color: #E64759;
        }
        .text-center {
            text-align: center;
        }
        .p-0 { padding: 0 }
        .m-0 { margin: 0 }
        .p-4 { padding: 1rem }
    </style>
</head>
<body class="w-full h-full flex justify-center align-center p-0 m-0">
<div class="text-center p-4">
    <div class="text-danger">
        <svg xmlns="http://www.w3.org/2000/svg" style="width: 8rem; height: 8rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
    </div>
    <h1>Ein Fehler ist aufgetreten</h1>
    <p>Leider konnte die Authorisierung nicht erfolgreich abgeschlossen werden.</p>

    @if(isset($description))
        <b>Weitere Informationen:</b>
        <p>{{ $description  }}</p>
    @endif
</div>

<script>
    window.setTimeout(function() {
        window.close();
    }, 10000)
</script>
</body>
</html>