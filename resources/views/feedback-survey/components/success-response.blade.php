<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Survey Submitted</title>
    <script>
        window.onload = function () {
            // Push the initial state into the browser's history
            history.pushState(null, null, location.href);
            
            // Prevent navigation on back button press
            window.addEventListener('popstate', function (event) {
                // Re-push the current state to keep the user on this page
                history.pushState(null, null, location.href);
                alert("Back navigation is disabled on this page."); // Optional: Add an alert for user feedback
            });
        };
    </script>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            color: #212529;
            font-family: 'Inter', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            max-width: 500px;
            background-color: #ffffff;
            border: 1px solid #e9ecef;
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .icon-container {
            display: inline-flex;
            justify-content: center;
            align-items: center;
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background-color: #e7f9ed;
            margin-bottom: 20px;
        }
        .icon-container svg {
            width: 50px;
            height: 50px;
            color: #28a745;
        }
        h1 {
            font-size: 2rem;
            font-weight: 600;
            margin: 20px 0 10px;
        }
        p {
            font-size: 1rem;
            color: #6c757d;
        }
        .social-icons {
            margin-top: 20px;
        }
        .social-icons a {
            margin: 0 10px;
            text-decoration: none;
            color: #495057;
            font-size: 2rem;
        }
        .social-icons a:hover {
            color: #007bff;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon-container">
            <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-check-circle" viewBox="0 0 16 16">
                <path d="M15.854 7.646a.5.5 0 0 1 0 .708l-8 8a.5.5 0 0 1-.708 0l-4-4a.5.5 0 1 1 .708-.708L7.5 15.793l7.646-7.647a.5.5 0 0 1 .708 0z"/>
                <path d="M7.5 1.5A6.5 6.5 0 1 0 7.5 14.5 6.5 6.5 0 0 0 7.5 1.5zM1 7.5a6.5 6.5 0 1 1 13 0 6.5 6.5 0 0 1-13 0z"/>
            </svg>
        </div>

        <h1>Thank You!</h1>
        <p>Thanks for your feedback, if you don't mind can you leave us a review on one of the platforms below?</p>

        <div class="social-icons">
            <a href="https://www.tripadvisor.com/Hotel_Review-g47734-d7132250-Reviews-Kayuta_Lake_Campground-Forestport_New_York.html" target="_blank" aria-label="Google">
                <i class="fab fa-google"></i>
            </a>
            <a href="https://www.facebook.com/KayutaLake" target="_blank" aria-label="Facebook">
                <i class="fab fa-facebook"></i>
            </a>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
