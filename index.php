<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Perpustakaan Modern</title>
    <style>
        /* General Style */
        body, html {
            margin: 0;
            padding: 0;
            font-family: 'Arial', sans-serif;
            box-sizing: border-box;
            background-color: #f1f1f1;
        }

        /* Hero Section */
        .hero {
            background: linear-gradient(to right,rgb(68, 92, 173),rgb(89, 153, 182));
            color: white;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            text-align: center;
            padding: 0 20px;
            position: relative;
        }

        .hero h1 {
            font-size: 4em;
            font-weight: bold;
            margin-bottom: 20px;
            animation: fadeIn 1.5s ease-in-out;
        }

        .hero p {
            font-size: 1.3em;
            margin-bottom: 30px;
            opacity: 0.8;
        }

        .cta-button {
            background-color: #FF7F50;
            color: white;
            padding: 20px 40px;
            font-size: 1.2em;
            border-radius: 50px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: background-color 0.3s ease, transform 0.3s ease;
            text-decoration: none;
        }

        .cta-button:hover {
            background-color: #ff5722;
            transform: translateY(-5px);
        }

        /* Animation */
        @keyframes fadeIn {
            0% {
                opacity: 0;
                transform: translateY(30px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .hero h1 {
                font-size: 3em;
            }

            .hero p {
                font-size: 1.1em;
            }

            .cta-button {
                font-size: 1em;
                padding: 15px 30px;
            }
        }

    </style>
</head>
<body>

    <!-- Hero Section -->
    <section class="hero">
        <h1>Selamat Datang di Perpustakaan Digital</h1>
        <p>Temukan pengetahuan tak terbatas, akses buku dan sumber daya kapan saja, di mana saja.</p>
        <a href="pages\auth\login.php" class="cta-button">Masuk ke Akun</a>
    </section>

</body>
</html>
