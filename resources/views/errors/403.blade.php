<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>403 Unauthorized</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f9;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .error-box {
            text-align: center;
        }
        .error-code {
            font-size: 8rem;
            font-weight: bold;
            color: #f30820;
        }
        .error-message {
            font-size: 1.5rem;
            color: #6c757d;
        }
        .home-btn {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="error-box">
        <div class="error-code">403</div>
        <div class="error-message">You are not authorized to access this page.</div>
        <a href="{{ url()->previous() }}" class="btn btn-danger home-btn">🔙 Go Back</a>
    </div>
</body>
</html>
