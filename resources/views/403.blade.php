<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 — Access Denied | VSULHS_SSLG</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f4f4f4;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .container {
            text-align: center;
            padding: 2rem;
        }

        .code {
            font-size: 6rem;
            font-weight: 800;
            color: #000;
            line-height: 1;
        }

        .title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #111;
            margin: 1rem 0 0.5rem;
        }

        .message {
            font-size: 0.95rem;
            color: #666;
            margin-bottom: 2rem;
            max-width: 400px;
        }

        .divider {
            width: 60px;
            height: 3px;
            background: #000;
            margin: 1.2rem auto;
            border-radius: 2px;
        }

        .btn-back {
            display: inline-block;
            padding: 0.7rem 1.8rem;
            background: #000;
            color: #fff;
            border-radius: 6px;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 600;
            transition: background 0.2s;
        }

        .btn-back:hover {
            background: #333;
        }

        .role-info {
            margin-top: 1.5rem;
            font-size: 0.8rem;
            color: #999;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="code">403</div>
    <div class="divider"></div>
    <h1 class="title">Access Denied</h1>
    <p class="message">
        {{ $exception->getMessage() ?: 'You do not have permission to access this page.' }}
    </p>
    <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('dashboard') }}" class="btn-back">
        &larr; Go Back
    </a>
    @auth
        <p class="role-info">
            Logged in as <strong>{{ Auth::user()->full_name }}</strong>
            &mdash; Role: <strong>{{ Auth::user()->role->name }}</strong>
        </p>
    @endauth
</div>

</body>
</html>