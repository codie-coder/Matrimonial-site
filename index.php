<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Matrimonial Home</title>

    <!-- Google Fonts + Custom CSS -->
    <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Poppins:wght@300;500&display=swap" rel="stylesheet" />

    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
            background-image: url('https://images.unsplash.com/photo-1523906834658-6e24ef2386f9?auto=format&fit=crop&w=1350&q=80');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #333;
        }

        .overlay {
            background-color: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(4px);
            padding: 3rem 4rem;
            border-radius: 20px;
            box-shadow: 0 12px 35px rgba(214, 51, 132, 0.3);
            text-align: center;
            max-width: 420px;
            width: 90%;
        }

        h1 {
            font-family: 'Great Vibes', cursive;
            font-size: 3.6rem;
            color: #d63384;
            margin-bottom: 2rem;
            letter-spacing: 1px;
        }

        .login-btn {
            display: inline-block;
            background: linear-gradient(45deg, #ff6f91, #d63384);
            color: white;
            font-weight: 600;
            font-size: 1.4rem;
            padding: 0.75rem 3rem;
            border-radius: 50px;
            text-decoration: none;
            box-shadow: 0 6px 15px rgba(214, 51, 132, 0.4);
            transition: background 0.3s ease, transform 0.2s ease;
            user-select: none;
        }

        .login-btn:hover,
        .login-btn:focus {
            background: linear-gradient(45deg, #d63384, #ff6f91);
            transform: scale(1.05);
            box-shadow: 0 8px 20px rgba(214, 51, 132, 0.6);
        }

        /* Responsive */
        @media (max-width: 480px) {
            h1 {
                font-size: 2.8rem;
            }
            .overlay {
                padding: 2rem 2.5rem;
            }
            .login-btn {
                padding: 0.6rem 2rem;
                font-size: 1.1rem;
            }
        }
    </style>
</head>
<body>
    <div class="overlay" role="main">
        <h1>Welcome to Our Matrimonial Site</h1>
        <a href="auth/login.php" class="login-btn" aria-label="Login to connect">🔐 Connect</a>
    </div>
</body>
</html>
