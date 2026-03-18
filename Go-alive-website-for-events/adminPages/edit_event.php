<?php
session_start();

if (!isset($_GET['id'])) {
    die('No event ID provided.');
}
$id = $_GET['id'];

$jsonPath = __DIR__ . '/../data/events.json';
$events = file_exists($jsonPath) ? json_decode(file_get_contents($jsonPath), true) : [];
if (!is_array($events)) {
    die('Events file not found or invalid.');
}

$event = null;
foreach ($events as $e) {
    if ($e['id'] === $id) {
        $event = $e;
        break;
    }
}
if (!$event) {
    die('Event not found.');
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adding Event Step 1 - Go Alive Opportunities</title>

    <link rel="stylesheet" href="adding-event-step2.css">
    <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
    integrity="sha512-..."
    crossorigin="anonymous"
    referrerpolicy="no-referrer"
  />
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;700;800&display=swap" rel="stylesheet">

</head>
<body>
    
    <div class="top-part-of-the-page">

        <div class="section-with-bg">

            <div class="background-transparent">
                
                <div class="top-bar">

                    <div class="logo-part">
                        <a href="../index.php">
                            <img src="../MediaFiles/logo slogan.png" alt="" width="343px" height="79px">
                        </a>
                    </div>

                    <div class="social-media-part">
                        <p>log in</p>
                        <i class="fab fa-facebook" style="font-size: 30px; color: #FFFFFF;"></i>
                        <i class="fab fa-instagram" style="font-size: 30px; color: #FFFFFF;"></i>
                        <i class="fab fa-linkedin" style="font-size: 30px; color: #FFFFFF;"></i>
                        <i class="fab fa-tiktok" style="font-size: 30px; color: #FFFFFF;"></i>
                    </div>


                </div>

                <div class="main-heading-top">   
                    <h1>WELCOME TO</h1>
                    
                    <div class="days-left-top-section">
                        <p class="number-days">ADMIN PAGE</p>
                    </div>

                    <div class="colorful-heading-top-part">
                
                            <h1>
                                ADDING EVENT
                            </h1>

                    </div>


                </div>

            </div>

        </div>

    </div>

    <div class="form-fillin-heading">
        <h1>FILL IN THE FORM FOR THE WEB PAGE</h1>
        <p>Don’t write a lot of text, look at the example</p>
    </div>

    <form method="post" action="update_event.php" enctype="multipart/form-data" class="form-structure">

    <input type="hidden" name="id" value="<?= htmlspecialchars($event['id']) ?>">
    <input type="hidden" name="existing_event_photo" value="<?= htmlspecialchars($event['event_photo'] ?? '') ?>">
    <input type="hidden" name="existing_background_photo" value="<?= htmlspecialchars($event['background_photo'] ?? '') ?>">
    <input type="hidden" name="existing_page_link" value="<?= htmlspecialchars($event['page_link'] ?? '') ?>">

                <div class="form-part-input">
                    <p>Project Name</p>
                    <input type="text" name="event_name" class="gradient-input-form-part" value="<?= htmlspecialchars($event['event_name']) ?>">
                </div>

                <div class="form-part-input">
                    <p>Type of project</p>
                    <input type="text" name="type_of_project" class="gradient-input-form-part" value="<?= htmlspecialchars($event['type_of_project'] ?? '') ?>">
                </div>

                <div class="form-part-input">
                    <p>Project Start Date</p>
                    <input type="date" name="start_date" class="gradient-input-form-part" value="<?= htmlspecialchars($event['start_date'] ?? '') ?>">
                </div>

                <div class="form-part-input">
                    <p>Project End Date</p>
                    <input type="date" name="end_date" class="gradient-input-form-part" value="<?= htmlspecialchars($event['end_date'] ?? '') ?>">
                </div>

                <div class="form-part-input">
                    <p>Project country</p>
                    <input type="text" name="country" class="gradient-input-form-part" value="<?= htmlspecialchars($event['country'] ?? '') ?>">
                </div>

                <div class="form-part-input">
                    <p>Project city</p>
                    <input type="text" name="city" class="gradient-input-form-part" value="<?= htmlspecialchars($event['city'] ?? '') ?>">
                </div>

                <div class="form-part-input">
                    <p>Project SHORT description</p>
                    <input type="text" name="short_description" class="gradient-input-form-part" value="<?= htmlspecialchars($event['short_description'] ?? '') ?>">
                </div>

                <div class="form-part-input">
                    <p>Project objectives</p>
                    <input type="text" name="about" class="gradient-input-form-part" value="<?= htmlspecialchars($event['about'] ?? '') ?>">
                </div>

                <div class="form-part-input">
                    <p>Accommodation</p>
                    <input type="text" name="accommodation" class="gradient-input-form-part" value="<?= htmlspecialchars($event['accommodation'] ?? '') ?>">
                </div>

                <div class="form-part-input">
                    <p>Travel reimbursement</p>
                    <input type="text" name="travel-reinbursement" class="gradient-input-form-part" value="<?= htmlspecialchars($event['travel-reinbursement'] ?? '') ?>">
                </div>

                <div class="form-part-input">
                    <p>Working language</p>
                    <input type="text" name="language" class="gradient-input-form-part" value="<?= htmlspecialchars($event['language'] ?? '') ?>">
                </div>

                <div class="form-part-input">
                    <p>Participation cost</p>
                    <input type="text" name="participation-cost" class="gradient-input-form-part" value="<?= htmlspecialchars($event['participation-cost'] ?? '') ?>">
                </div>

                <div class="form-part-input">
                    <p>Link to Google Form </p>
                    <input type="text" name="link" class="gradient-input-form-part" value="<?= htmlspecialchars($event['link'] ?? '') ?>">
                </div>

                <div class="form-part-input">
                    <p>Project photo</p>

                    <?php if (!empty($event['event_photo'])): ?>
                        <img src="../mediaFiles/<?= htmlspecialchars($event['event_photo']) ?>" alt="Event Photo" style="max-width:150px;">
                    <?php endif; ?>

                    <input type="file" name="event_photo" class="gradient-input-form-part" accept="image/*" placeholder="Project photo">
                </div>

                <button type="submit">
                    SUBMIT
                </button>

    </form>

    <div class="footer-section">

        <div class="footer-section-inside">

            <div class="left-part">

                <div class="logo-part-footer">
                    <img src="../MediaFiles/logo slogan.png" alt="" width="343px" height="79px">
                </div>

                <div class="contact-info-footer-section">

                    <div class="phone-number-footer">
                        <h2>
                            Call us: 
                        </h2>
                        <p>
                            +30 246 130 83 80
                        </p>
                    </div>

                    <div class="address-footer">
                        <h2>
                            Visit us:  
                        </h2>
                        <p>
                            S. Mplioura 2, Kozani, Greece
                        </p>
                    </div>

                    <div class="email-footer">
                        <h2>
                            Email us: 
                        </h2>
                        <p>
                            info@goalive.eu
                        </p>
                    </div>

                </div>

            </div>

        </div>

    </div>

</body>
</html>