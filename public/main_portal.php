<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Patient Portal | New Santor Clinic</title>

    <style>
        body {
    margin: 0;
    font-family: Arial, sans-serif;
    background-image: 
        linear-gradient(rgba(255,255,255,0.85), rgba(255,255,255,0.85)),
        url('logo.png');
    background-repeat: repeat;
}

        .topbar {
            background: #1f4e46;
            color: white;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .topbar small {
            font-size: 13px;
            font-weight: normal;
            display: block;
        }

        .admin-btn {
            background: white;
            color: #1f4e46;
            padding: 8px 16px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            border-radius: 6px;
        }

        .center-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 60px;
        }

        .booking-card {
            background: white;
            width: 500px;
            padding: 40px 30px;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            text-align: center;
        }

        .section-box {
            display: inline-block;
            background: #f1f1f1;
            padding: 8px 14px;
            border-radius: 6px;
            margin: 5px;
            font-size: 14px;
        }

        .action-btn {
            display: inline-block;
            background: #1f4e46;
            color: white;
            padding: 12px 25px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
        }

        .action-btn:hover {
            opacity: 0.9;
        }
    </style>
</head>
<body>

<!-- TOPBAR -->
<div class="topbar">
    <div>
        The New Santor Clinic and Diagnostic Center Web-Based Management System
        <small>
            Welcome, <?= htmlspecialchars($_SESSION['user']['name'] ?? 'Guest') ?>
        </small>
    </div>

        <a href="login.php" class="admin-btn">
    Admin Login
</a>
</div>

<!-- MAIN CONTENT -->
<div class="center-wrapper">
    <div class="booking-card">

        <div style="margin-bottom:20px;">
            <img src="logo.png" alt="Clinic Logo" style="width:90px; border-radius:15px;">
        </div>

        <h1 style="font-size:34px; margin-bottom:10px; color:#1f4e46;">
            Patient Portal
        </h1>

        <p style="font-size:15px; margin-bottom:20px; color:#555;">
            Your trusted partner in quality healthcare services
        </p>

        <div style="margin-bottom:25px;">
            <span class="section-box">📍 Santor, Tanauan, Philippines, 4232</span>
            <span class="section-box">🕗 8:00 AM – 12:00 PM</span>
        </div>

        <div class="section-box" style="text-align:left; margin-bottom:25px; display:block;">
            <b>HOW TO USE THIS PORTAL (GUIDE)</b>
            <ol style="margin-top:10px; padding-left:18px; font-size:14px;">
                <li>Book Appointment – Fill up the form and submit.</li>
                <li>Wait for Admin Approval – You will be scheduled.</li>
            </ol>
        </div>

        <a href="booking.php" class="action-btn">
            📅 Book Appointment
        </a>

    </div>
</div>

</body>
</html>