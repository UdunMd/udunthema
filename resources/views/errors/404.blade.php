<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404 Not Found - {{ config('app.name', 'AlxZen Panel') }}</title>
    <style>
        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background-color: #08080e;
            color: #f0f0f8;
            height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
        }
        .container {
            max-width: 500px;
            padding: 2rem;
            background: #111118;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.4), 0 0 20px rgba(124,58,237,0.15);
            border: 1px solid rgba(255,255,255,0.06);
        }
        h1 {
            font-size: 48px;
            font-weight: 800;
            margin: 0 0 10px;
            color: #7c3aed;
        }
        h2 {
            font-size: 22px;
            font-weight: 600;
            margin: 0 0 15px;
        }
        p {
            color: #8b8ba0;
            margin-bottom: 25px;
            line-height: 1.5;
        }
        a {
            display: inline-block;
            background-color: #7c3aed;
            color: white;
            text-decoration: none;
            padding: 10px 24px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.2s;
        }
        a:hover {
            background-color: #6d28d9;
            transform: scale(1.02);
            box-shadow: 0 0 15px rgba(124,58,237,0.3);
        }
        .brand {
            margin-top: 30px;
            font-size: 12px;
            color: #4a4a5e;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>404</h1>
        <h2>Page Not Found</h2>
        <p>The page you are looking for doesn't exist or has been moved.</p>
        <a href="/">Return to Dashboard</a>
        <div class="brand">AlxZen Panel</div>
    </div>
</body>
</html>
