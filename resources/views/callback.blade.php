<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Success</title>
    <style>
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
        .text-primary  {
            color: #2b6af8;
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
    <div class="text-primary">
        <svg xmlns="http://www.w3.org/2000/svg" style="width: 8rem; height: 8rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
        </svg>
    </div>
    <h1>Authentifizierung erfolgreich</h1>
    <p>Dieses Fenster schließt automatisch in 3 Sekunden. Sollte dies nicht der Fall sein, kannst du das Fenster einfach schließen.</p>
</div>

<script>
    window.opener.postMessage()
    window.setTimeout(function() {
        window.close();
    }, 3000)
</script>
</body>
</html>