<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require 'assets/php/PHPMailer/src/Exception.php';
require 'assets/php/PHPMailer/src/PHPMailer.php';
require 'assets/php/PHPMailer/src/SMTP.php';

$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // ================= SANITIZE INPUTS =================
    $name    = trim(strip_tags($_POST["name"] ?? ''));
    $email   = filter_var($_POST["email"] ?? '', FILTER_SANITIZE_EMAIL);
    $phone   = trim(strip_tags($_POST["phone"] ?? ''));
    $subject = trim(strip_tags($_POST["subject"] ?? ''));
    $message = trim(strip_tags($_POST["message"] ?? ''));
    $recaptcha_token = $_POST['recaptcha_token'] ?? '';

    // ================= reCAPTCHA v3 =================
    if (empty($recaptcha_token)) {
        $errors[] = "reCAPTCHA verification failed. Please try again.";
    } else {

        $secretKey = '6LfNETosAAAAADFDLcQSCV8l9WYCtdDWROEYgTfu';

        $verifyResponse = file_get_contents(
            "https://www.google.com/recaptcha/api/siteverify?secret={$secretKey}&response={$recaptcha_token}"
        );

$responseData = json_decode($verifyResponse, true);


        if (
            empty($responseData['success']) ||
            $responseData['score'] < 0.5 ||
            $responseData['action'] !== 'contact_form'
        ) {
            $errors[] = "reCAPTCHA verification failed. Please try again.";
        }
    }

    // ================= VALIDATIONS =================
    if (strlen($name) < 2) {
        $errors[] = "Please provide a valid name.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please provide a valid email address.";
    }

    if (!empty($phone)) {
        $digits = preg_replace('/\D/', '', $phone);
        if (strlen($digits) < 10 || strlen($digits) > 12) {
            $errors[] = "Phone number must be 10–12 digits.";
        }
    }

    if (strlen($message) < 10) {
        $errors[] = "Message must be at least 10 characters.";
    }

    // ================= SEND EMAIL =================
    if (empty($errors)) {

        $mail = new PHPMailer(true);

        try {
            $mail->SMTPDebug = SMTP::DEBUG_OFF; // 🔴 turn ON only for testing
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'linipremaraj2000@gmail.com';
            $mail->Password   = 'jcdgslctkbwiwgtn'; // Gmail App Password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            $mail->setFrom('linipremaraj2000@gmail.com', 'Master Duct Website');
            $mail->addAddress('lini@gysztechnologies.com');
            $mail->addAddress('vishnu@gysztechnologies.com');

            $mail->addReplyTo($email, $name);

            $mail->isHTML(true);
            $mail->Subject = "NEW LEAD: {$name}";

            // ================= EMAIL BODY =================
            $mail->Body = "
            <html>
            <head>
            <style>
                body { font-family: Arial, sans-serif; background:#f5f5f5; padding:20px; }
                .container { max-width:650px; margin:auto; background:#fff; border-radius:8px; overflow:hidden; }
                .header { background:#FDB913; color:#001224; padding:25px; text-align:center; }
                .content { padding:25px; }
                table { width:100%; border-collapse:collapse; font-size:14px; }
                th { background:#001224; color:#fff; padding:12px; text-align:left; }
                td { padding:12px; border-bottom:1px solid #eaeaea; }
                .label { font-weight:600; width:30%; }
                .footer { background:#f5f5f5; padding:15px; text-align:center; font-size:12px; color:#777; }
            </style>
            </head>

            <body>
            <div class='container'>
                <div class='header'>
                    <h2>New Contact Form Lead</h2>
                    <p>Master Duct Website</p>
                </div>

                <div class='content'>
                    <table>
                        <tr><th colspan='2'>Lead Details</th></tr>
                        <tr><td class='label'>Name</td><td>{$name}</td></tr>
                        <tr><td class='label'>Email</td><td>{$email}</td></tr>
                        <tr><td class='label'>Phone</td><td>{$phone}</td></tr>
                        <tr><td class='label'>Subject</td><td>{$subject}</td></tr>
                        <tr><td class='label'>Message</td><td>".nl2br($message)."</td></tr>
                    </table>
                </div>

                <div class='footer'>
                    Submitted via Master Duct website
                </div>
            </div>
            </body>
            </html>";

            $mail->send();

        } catch (Exception $e) {
            $errors[] = "Mailer Error: " . $mail->ErrorInfo;
        }
    }
}

$showErrors = !empty($errors);
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Thank You - MasterDuct </title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800;900&family=Open+Sans:wght@400;600&display=swap"
        rel="stylesheet">
    <!-- Ionicons -->
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <!-- Styles -->
    <link rel="stylesheet" href="assets/css/masterduct.css">
    <link href="assets/css/style.css" rel="stylesheet">
</head>

<body>

    <!-- Top Bar -->
    <div class="top-bar">
        <div class="container top-bar-content">
            <div class="contact-info">
                <span><ion-icon name="call-outline"></ion-icon> <a href="tel:+971 (0) 65 79 9768">+971 (0) 65 79 9768</a></span>
                <span><ion-icon name="mail-outline"></ion-icon> <a href="mailto:info@masterduct.ae">info@masterduct.ae</a></span>
                <span><ion-icon name="time-outline"></ion-icon> Mon - Sat 8:00 AM - 06:00 PM</span>
            </div>
            <div class="social-links">
                <a href="https://www.facebook.com/masterduct.ae" target="_blank"><ion-icon name="logo-facebook"></ion-icon></a>
                <a href="https://www.instagram.com/master_duct_/?hl=en" target="_blank"><ion-icon name="logo-instagram"></ion-icon></a>
                <a href="https://www.linkedin.com/company/mdacae" target="_blank"><ion-icon name="logo-linkedin"></ion-icon></a>
                <a href="https://www.youtube.com/@MasterDuct" target="_blank"><ion-icon name="logo-youtube"></ion-icon></a>
            </div>
        </div>
    </div>

    <!-- Navbar -->
    <nav class="navbar">
        <div class="container navbar-content">
            <div class="logo">
                <a class="navbar-brand" href="index.html"><img src="assets/logo.svg" alt="Master Duct"></a>
            </div>
            <div class="nav-right-combo">
                <ul class="nav-menu">
                    <li><a href="index.html">HOME</a></li>
                    <li><a href="about.html">ABOUT US</a></li>
                    <li class="dropdown">
                        <a href="#">PRODUCTS <ion-icon name="chevron-down-outline"></ion-icon></a>
                        <ul class="dropdown-menu">
                            <li><a href="girectangular.html">GI Rectangular</a></li>
                            <li><a href="giround.html">GI Round</a></li>
                            <li><a href="preinsulated.html">Pre-Insulated</a></li>
                            <li><a href="aluminium.html">Aluminium</a></li>
                            <li><a href="mildsteel.html">Mild Steel</a></li>
                            <li><a href="volumecontroldampers.html">Volume Control Dampers</a></li>
                            <li><a href="accessdoors.html">Access Doors</a></li>
                            <li><a href="nonreturndampers.html">Non Return Dampers</a></li>
                        </ul>
                    </li>
                    <li><a href="contact.html">CONTACT</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Thank You / Error Section -->
    <section class="section-padding" style="text-align: center; padding: 120px 0; background: #fafafa;">
        <div class="container">
            <?php if ($showErrors): ?>
                <!-- Error Display -->
                <div style="background: #fff; padding: 60px; border-radius: 20px; box-shadow: 0 20px 50px rgba(0,0,0,0.05); max-width: 600px; margin: 0 auto;">
                    <div style="width: 100px; height: 100px; background: rgba(255, 0, 0, 0.1); border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 30px;">
                        <ion-icon name="alert-circle" style="font-size: 60px; color: #dc3545;"></ion-icon>
                    </div>
                    <h1 style="color: var(--dark-blue); font-weight: 800; font-family: var(--font-heading); margin-bottom: 20px; font-size: 2.5rem;">Validation Error</h1>
                    <div style="text-align: left; background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 10px; padding: 20px; margin-bottom: 30px;">
                        <ul style="margin: 0; padding-left: 20px; color: #721c24;">
                            <?php foreach ($errors as $error): ?>
                                <li style="margin-bottom: 8px;"><?php echo htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <a href="contact.html" class="btn-quote" style="display: inline-block; padding: 15px 40px; font-size: 14px;">Go Back to Contact Form</a>
                </div>
            <?php else: ?>
                <!-- Success Display -->
                <div style="background: #fff; padding: 60px; border-radius: 20px; box-shadow: 0 20px 50px rgba(0,0,0,0.05); max-width: 600px; margin: 0 auto;">
                    <div style="width: 100px; height: 100px; background: rgba(253, 185, 19, 0.1); border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 30px;">
                        <ion-icon name="checkmark-circle" style="font-size: 60px; color: var(--primary);"></ion-icon>
                    </div>
                    <h1 style="color: var(--dark-blue); font-weight: 800; font-family: var(--font-heading); margin-bottom: 20px; font-size: 2.5rem;">Thank You!</h1>
                    <p style="font-size: 1.1rem; color: var(--text-gray); margin-bottom: 35px; line-height: 1.8;">Your message has been successfully sent. We will get back to you shortly.</p>
                    <a href="index.html" class="btn-quote" style="display: inline-block; padding: 15px 40px; font-size: 14px;">Return to Home</a>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container footer-content">
            <div class="footer-col brand-col">
                <div class="footer-logo">
                    <a class="navbar-brand" href="index.html"><img src="assets/logo.svg" alt="Master Duct"></a>
                </div>
                <p class="footer-desc">Leading the way in duct manufacturing and accessories across the Middle East. Excellence in every installation.</p>
            </div>
            <div class="footer-col">
                <h4>Quick Links</h4>
                <ul class="footer-links">
                    <li><a href="index.html">Home</a></li>
                    <li><a href="about.html">About Us</a></li>
                    <li><a href="contact.html">Contact</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>Contact Info</h4>
                <ul class="footer-contact">
                    <li><ion-icon name="location"></ion-icon> Industrial Area 15, Sharjah, UAE</li>
                    <li><ion-icon name="call"></ion-icon> +971 (0) 65 79 9768</li>
                    <li><ion-icon name="mail"></ion-icon> info@masterduct.ae</li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>© 2025 Master Duct. All Rights Reserved. Designed for Excellence.</p>
        </div>
    </footer>

</body>

</html>
