<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Pet Matchmaker - FurFaimily</title>
    <link rel="stylesheet" href="../CSS/sign.css">
    <style>
        .quiz-container { max-width: 600px; margin: 50px auto; padding: 20px; text-align: center; background: white; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .question { margin-bottom: 20px; text-align: left; }
        .question label { font-weight: bold; display: block; margin-bottom: 10px; }
        .options label { font-weight: normal; margin-right: 15px; }
    </style>
</head>
<body>
    <div class="quiz-container">
        <h2>Find Your Perfect Pet</h2>
        <p>Answer 3 quick questions to meet your match!</p>
        <form action="quiz-result.php" method="POST">
            <div class="question">
                <label>1. What size is your home?</label>
                <div class="options">
                    <label><input type="radio" name="home" value="small" required> Apartment</label>
                    <label><input type="radio" name="home" value="large"> House with yard</label>
                </div>
            </div>
            <div class="question">
                <label>2. What is your activity level?</label>
                <div class="options">
                    <label><input type="radio" name="activity" value="calm" required> Chill & Relaxed</label>
                    <label><input type="radio" name="activity" value="active"> Active & Outdoors</label>
                </div>
            </div>
            <div class="question">
                <label>3. Do you have small children?</label>
                <div class="options">
                    <label><input type="radio" name="kids" value="yes" required> Yes</label>
                    <label><input type="radio" name="kids" value="no"> No</label>
                </div>
            </div>
            <button type="submit" class="btn" style="width: 100%; padding: 12px; min-height: 44px;">Find My Match</button>
            <br><br>
            <a href="index.php">Back to Home</a>
        </form>
    </div>
</body>
</html>
