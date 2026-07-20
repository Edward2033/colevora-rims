<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>403 – Forbidden</title>
    <style>
        body { font-family: ui-sans-serif, system-ui, sans-serif; background: #f9fafb; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; }
        .container { text-align: center; padding: 2rem; }
        h1 { font-size: 6rem; font-weight: 700; color: #ea580c; margin: 0; }
        h2 { font-size: 1.5rem; color: #374151; margin: 0.5rem 0; }
        p { color: #6b7280; margin: 1rem 0 2rem; }
        a { background: #ea580c; color: white; padding: 0.75rem 1.5rem; border-radius: 0.5rem; text-decoration: none; font-weight: 600; }
        a:hover { background: #c2410c; }
    </style>
</head>
<body>
    <div class="container">
        <h1>403</h1>
        <h2>Access Forbidden</h2>
        <p>{{ $exception->getMessage() ?: 'You do not have permission to access this page.' }}</p>
        <a href="/">Go Home</a>
    </div>
</body>
</html>
