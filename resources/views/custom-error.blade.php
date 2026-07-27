<!-- <!DOCTYPE html>
<html>
<head>
    <title>Something went wrong</title>
    <style>
        /* Make sure html and body take full height */
        html, body {
            height: 100%;
            margin: 0;
        }
        /* Set background image and make it cover full screen */
        body {
            background-image: url('{{ asset('images/error-image.jpg') }}');
            background-size: cover;        /* cover entire viewport */
            background-position: center;   /* center the image */
            background-repeat: no-repeat;  /* don't repeat */
            display: flex;
            justify-content: center;       /* center horizontally */
            align-items: center;           /* center vertically */
            color: white;                  /* text color */
            font-family: Arial, sans-serif;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.7); /* make text readable */
            height: 100vh;                 /* full viewport height */
            flex-direction: column;
            text-align: center;
            /* padding: 20px; */
        }
        h1 {
            font-size: 3em;
            margin-bottom: 0.2em;
        }
        p {
            font-size: 1.2em;
        }
    </style>
</head>
<body>
    <h1>Oops! Something went wrong.</h1>
    <p>We are working to fix the issue. Please try again later.</p>
</body>
</html> -->



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Oops! Something Went Wrong</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap');

        body, html {
            height: 100%;
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
        }

        .container {
            text-align: center;
            max-width: 450px;
            background: rgba(0,0,0,0.5);
            padding: 40px 30px;
            border-radius: 15px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.3);
            position: relative;
            z-index: 10;
        }

        .container h1 {
            font-size: 6rem;
            margin: 0 0 20px 0;
            font-weight: 700;
            animation: fadeInDown 1s ease forwards;
        }

        .container p {
            font-size: 1.25rem;
            margin: 0 0 30px 0;
            animation: fadeInUp 1s ease forwards;
            animation-delay: 0.3s;
        }

        .btn-home {
            text-decoration: none;
            color: #764ba2;
            background: #fff;
            padding: 12px 28px;
            font-weight: 700;
            border-radius: 30px;
            transition: all 0.3s ease;
            display: inline-block;
            animation: fadeInUp 1s ease forwards;
            animation-delay: 0.5s;
        }

        .btn-home:hover {
            background: #6b3e94;
            color: #fff;
            box-shadow: 0 4px 15px rgba(107, 62, 148, 0.6);
        }

        /* Background floating circles */
        .background-circles {
            position: absolute;
            top: 50%;
            left: 50%;
            width: 120vw;
            height: 120vh;
            transform: translate(-50%, -50%);
            overflow: hidden;
            z-index: 1;
        }

        .circle {
            position: absolute;
            border-radius: 50%;
            opacity: 0.15;
            animation: float 10s linear infinite;
        }

        .circle:nth-child(1) {
            width: 220px;
            height: 220px;
            background: #ff7e5f;
            top: 20%;
            left: 10%;
            animation-duration: 12s;
        }
        .circle:nth-child(2) {
            width: 300px;
            height: 300px;
            background: #feb47b;
            top: 70%;
            left: 20%;
            animation-duration: 15s;
            animation-delay: 2s;
        }
        .circle:nth-child(3) {
            width: 150px;
            height: 150px;
            background: #86a8e7;
            top: 40%;
            left: 75%;
            animation-duration: 13s;
            animation-delay: 4s;
        }
        .circle:nth-child(4) {
            width: 350px;
            height: 350px;
            background: #91eae4;
            top: 80%;
            left: 80%;
            animation-duration: 18s;
            animation-delay: 1s;
        }

        /* Animations */
        @keyframes fadeInDown {
            0% {
                opacity: 0;
                transform: translateY(-40px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInUp {
            0% {
                opacity: 0;
                transform: translateY(40px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes float {
            0% {
                transform: translateY(0) translateX(0);
            }
            50% {
                transform: translateY(-20px) translateX(15px);
            }
            100% {
                transform: translateY(0) translateX(0);
            }
        }
    </style>
</head>
<body>
    <div class="background-circles">
        <div class="circle"></div>
        <div class="circle"></div>
        <div class="circle"></div>
        <div class="circle"></div>
    </div>

    <div class="container" role="main" aria-labelledby="error-title" aria-describedby="error-message">
        <h1 id="error-title">500</h1>
        <p id="error-message">Oops! Something went wrong on our end.<br>We are working to fix it.</p>
        <a href="{{ url('/dashboard') }}" class="btn-home" aria-label="Go back to homepage">Go Home</a>
    </div>
</body>
</html>
