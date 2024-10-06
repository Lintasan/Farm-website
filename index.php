<?php
session_start(); // Start the session to access the logged-in user data

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "Farm"; // Make sure your database name is correct

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'submit_query') {
    $user_id = $_SESSION['user_id']; // Assuming user ID is stored in session variable after logging in
    $subject = $conn->real_escape_string($_POST['subject']); // Get subject from the form, include this field in the form
    $message = $conn->real_escape_string($_POST['message']);

    // Prepare SQL statement
    $sql = "INSERT INTO queries (user_id,subject, message, status) VALUES ('$user_id', '$subject', '$message', 'pending')";

    // Execute query
    if ($conn->query($sql) === TRUE) {
        echo json_encode(["status" => "success", "message" => "Your query has been submitted successfully."]);
    } else {
        echo json_encode(["status" => "error", "message" => "There was an error submitting your query: " . $conn->error]);
    }

    $conn->close();
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="fflogo.jpeg" type="image/icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <title>Fresh Farms - Your Source for Healthy Living</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #c8e6c9; /* Change this to your desired shade of green */
            color: #333;
            overflow-x: hidden;
        }

       
        /* Navbar styles */
        .navbar {
            background-color: rgba(255, 255, 255, 0.9);
            box-shadow: 0 2px 20px rgba(0, 0, 0, .1);
            transition: background-color 0.3s ease;
        }

        .navbar-brand {
            font-weight: 700;
            transition: transform 0.2s ease;
        }

        .navbar-brand:hover {
            transform: scale(1.05);
        }

        .nav-link {
            color: #333;
            font-weight: 500;
            transition: color 0.3s ease, font-weight 0.3s ease;
        }

        .nav-link:hover {
            color: #ffc107;
            font-weight: 600;
        }

        /* Hero section styles */
        .hero-section {
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url('img/farm.jpg') no-repeat center center;
            background-size: cover;
            height: 100vh;
            display: flex;
            align-items: center;
            text-align: center;
            color: white;
            animation: fadeIn 1s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        .hero-content {
            max-width: 800px;
            margin: 0 auto;
        }

        /* Button styles */
        .btn-primary {
            background-color: #ffc107;
            border-color: #ffc107;
            color: #333;
            font-weight: 600;
            padding: 10px 25px;
            transition: all 0.3s ease;
            animation: bounce 1s infinite alternate;
        }

        @keyframes bounce {
            from {
                transform: translateY(0);
            }
            to {
                transform: translateY(-10px);
            }
        }

        .btn-primary:hover {
            background-color: #e0a800;
            border-color: #e0a800;
            transform: translateY(-2px);
        }

        /* Section title styles */
        .section-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: #333;
            animation: slideIn 0.5s ease forwards;
            opacity: 0;
            transform: translateY(20px);
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Service card styles */
        .service-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            height: 100%;
        }

        .service-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .service-icon {
            font-size: 4rem;
            color: #ffc107;
            margin-bottom: 1rem;
        }
/* About section styles */
.about-section {
    background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url('img/farm1.jpg') no-repeat center center;
    ackground-size: cover;
    background-color: #f9f9f9;
    position: relative;
    overflow: hidden;
    padding: 5rem 0;
}

.about-image-container {
    position: relative;
    overflow: hidden;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    max-width: 400px; /* Limit the maximum width of the image container */
    margin: 0 auto; /* Center the image if it's smaller than the column width */
}

.about-image {
    width: 100%;
    height: auto;
    transition: transform 0.3s ease;
}

.about-image-container:hover .about-image {
    transform: scale(1.05);
}

.about-image-overlay {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background-color: rgba(255, 193, 7, 0.8);
    overflow: hidden;
    width: 100%;
    height: 0;
    transition: .5s ease;
    display: flex;
    align-items: center;
    justify-content: center;
}

.about-image-container:hover .about-image-overlay {
    height: 25%;
}

.overlay-text {
    color: white;
    font-size: 24px;
    font-weight: bold;
    text-align: center;
}

.about-content {
    padding: 20px;
    background-color: white;
    border-radius: 15px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
}

.about-features li {
    font-size: 16px;
    transition: transform 0.3s ease;
}

.about-features li:hover {
    transform: translateX(5px);
}

.about-features i {
    font-size: 20px;
    width: 25px;
    text-align: center;
}

        .overlay-text {
            color: white;
            font-size: 24px;
            font-weight: bold;
            text-align: center;
        }

        
        /* Contact form styles */
        .contact-form {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            transition: transform 0.3s ease;
        }

        .contact-form:hover {
            transform: scale(1.02);
        }

        /* Footer styles */
        .footer {
            background-color: #333;
            color: white;
            padding: 2rem 0;
            position: relative;
        }

        .footer p {
            margin: 0;
            animation: fadeIn 1s ease forwards;
            opacity: 0;
            transform: translateY(20px);
        }

        .social-icons a {
            color: #ffc107;
            font-size: 2rem;
            margin: 0 10px;
            transition: color 0.3s ease, transform 0.3s ease;
        }

        .social-icons a:hover {
            color: #e0a800;
            transform: scale(1.1);
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#"><span class="text-danger">FRESH</span> <span class="text-warning">FARMS</span> 🍃</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav"
                aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#Home">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#services">Services</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#about">About Us</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contact">Contact</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="Home" class="hero-section">
        <div class="container hero-content">
            <h1 class="display-4 fw-bold mb-4">Discover Healthy Fresh <span class="text-warning">Fruits!</span></h1>
            <p class="lead mb-4">Revitalize Your Mornings with Fresh, Nutritious Breakfasts! Elevate Your Health and Mind.</p>
            <a href="dashboard.php" class="btn btn-primary btn-lg">Click to Start <i class="fas fa-arrow-right ms-2"></i></a>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="py-5">
        <div class="container">
            <h2 class="section-title text-center">Our Services</h2>
            <p class="section-description text-center">We offer a variety of services to meet your needs. From farm tours to personalized fruit baskets, we provide high-quality services tailored to your preferences.</p>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="service-card p-4 text-center">
                        <i class="fas fa-truck service-icon"></i>
                        <h3 class="h4 mb-3">Delivery Services</h3>
                        <p>Experience farm-fresh goodness delivered right to your doorstep, supporting local agriculture with every order.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="service-card p-4 text-center">
                        <i class="fas fa-apple-alt service-icon"></i>
                        <h3 class="h4 mb-3">Fresh Produce</h3>
                        <p>Indulge in the vibrant flavors of our farm-fresh fruits and vegetables, carefully grown for your health and enjoyment.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="service-card p-4 text-center">
                        <i class="fas fa-handshake service-icon"></i>
                        <h3 class="h4 mb-3">Consultation</h3>
                        <p>Unlock personalized farm consultation services, guiding you towards sustainable practices for your farming endeavors.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

   <!-- About Us Section -->
<section id="about" class="about-section py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-5 mb-4 mb-lg-0">
                <div class="about-image-container">
                    <img src="img/About.jpg" alt="Fresh Farms" class="img-fluid rounded-lg shadow-lg about-image">
                    <div class="about-image-overlay">
                        <span class="overlay-text">Fresh Farms</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="about-content">
                    <h2 class="section-title mb-4">About Us</h2>
                    <p class="lead mb-4">Fresh Farms is your go-to source for healthy, organic produce. We are committed to bringing you the best quality fruits and vegetables, grown sustainably with care for the environment.</p>
                    <p class="mb-4">Our mission is to promote healthy living through fresh, natural foods that nourish your body and soul.</p>
                    <div class="row">
                        <div class="col-md-6">
                            <ul class="list-unstyled about-features">
                                <li class="mb-3"><i class="fas fa-leaf text-success me-3"></i>Fresh and organic produce</li>
                                <li class="mb-3"><i class="fas fa-map-marker-alt text-danger me-3"></i>Locally sourced ingredients</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul class="list-unstyled about-features">
                                <li class="mb-3"><i class="fas fa-seedling text-primary me-3"></i>Sustainable farming practices</li>
                                <li class="mb-3"><i class="fas fa-users text-warning me-3"></i>Community focused</li>
                            </ul>
                        </div>
                    </div>
                    <a href="#contact" class="btn btn-primary btn-lg mt-3">Get in Touch</a>
                </div>
            </div>
        </div>
    </div>
</section>

  <!-- Contact Us Section -->
<section id="contact" class="py-5">
    <div class="container">
        <h2 class="section-title text-center">Contact Us</h2>
        <p class="section-description text-center">Have any questions? We'd love to hear from you. Reach out to us, and we'll get back to you as soon as possible.</p>
        <div class="row justify-content-center">
            <div class="col-md-8">
                <form id="contactForm" class="contact-form" style="background-color: #fdfdfd; padding: 20px; border-radius: 10px; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);">
                    <div class="mb-3">
                        <label for="name" class="form-label" style="font-weight: bold; color: #333;">Name</label>
                        <input type="text" class="form-control" id="name" name="name" required style="border-radius: 8px; border: 1px solid #ced4da; padding: 10px;">
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label" style="font-weight: bold; color: #333;">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required style="border-radius: 8px; border: 1px solid #ced4da; padding: 10px;">
                    </div>
                    <div class="mb-3">
                        <label for="message" class="form-label" style="font-weight: bold; color: #333;">Message</label>
                        <textarea class="form-control" id="message" name="message" rows="5" required style="border-radius: 8px; border: 1px solid #ced4da; padding: 10px;"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary" style="background-color: #ffc107; border: none; color: #333; padding: 10px 20px; border-radius: 5px; transition: background-color 0.3s;">
                        Submit
                    </button>
                </form>
                <div id="responseMessage" class="mt-3"></div>
            </div>
        </div>
    </div>
</section>


    <!-- Footer Section -->
    <footer class="footer text-center">
        <div class="container">
            <p>&copy; 2023 Fresh Farms. All rights reserved.</p>
            <div class="social-icons">
                <a href="#"><i class="fab fa-facebook"></i></a>
                <a href="#"><i class="fab fa-twitter"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
                <a href="#"><i class="fab fa-linkedin"></i></a>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#contactForm').on('submit', function(e) {
                e.preventDefault();

                $.ajax({
                    type: 'POST',
                    url: 'index.php',
                    data: $(this).serialize() + '&action=submit_query',
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            $('#responseMessage').html('<div class="alert alert-success">' + response.message + '</div>');
                            $('#contactForm')[0].reset();
                        } else {
                            $('#responseMessage').html('<div class="alert alert-danger">' + response.message + '</div>');
                        }
                    },
                    error: function() {
                        $('#responseMessage').html('<div class="alert alert-danger">An error occurred while submitting your query.</div>');
                    }
                });
            });
        });
    </script>
</body>

</html>
