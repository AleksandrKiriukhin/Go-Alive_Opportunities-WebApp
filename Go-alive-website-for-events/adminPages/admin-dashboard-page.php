<?php

session_set_cookie_params([
    'lifetime' => 0, // expire on browser close
    'path' => '/',
    'secure' => false, // set true if HTTPS
    'httponly' => true,
    'samesite' => 'Lax'
]);

session_start();

// Set timeout duration (in seconds), 30 sec for testing
$timeout_duration = 420;

// Check if "last_activity" timestamp exists and if session timed out
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout_duration) {
    session_unset();
    session_destroy();
    header('Location: ../loginPage/login-page.php');
    exit;
}

// Update last activity time stamp
$_SESSION['last_activity'] = time();

if (empty($_SESSION['user']) || $_SESSION['user']['email'] !== 'volunteers@goalive.eu') {
    header('Location: ../loginPage/login-page.php');
    exit;
}

$eventsFile = __DIR__ . "/../data/events.json"; // Adjust this path if needed
$dashboardEvents = [];

if (file_exists($eventsFile)) {
    $json = file_get_contents($eventsFile);
    $dashboardEvents = json_decode($json, true);
} else {
    echo '<p style="color:red;">Could not find the events JSON file.</p>';
}

$latestEvent = null;
$daysLeft = null;

if (file_exists($eventsFile)) {
    
    $jsonContent = file_get_contents($eventsFile);
    $eventsData = json_decode($jsonContent, true);

    if (is_array($eventsData) && !empty($eventsData)) {
        $latestEvent = end($eventsData);
        
        $startDate = date_create($latestEvent['start_date']);
        $today = new DateTime();
        $interval = $today->diff($startDate);
        $daysLeft = (int)$interval->format('%r%a');
    }
    
} else {

        $latestEvent = null;
        $daysLeft = 0;
    }
?>

<script>
function deleteEvent(eventId) {
    if (!confirm("Are you sure you want to delete this event?")) return;

    fetch('delete_event.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: 'id=' + encodeURIComponent(eventId)
    })
    .then(response => response.text())
    .then(data => {
        if (data.trim() === "success") {
            alert("Event deleted successfully");
            location.reload();
        } else {
            alert("Error deleting event: " + data);
        }
    })
    .catch(error => {
        alert("Request failed: " + error);
    });
}

window.addEventListener('beforeunload', function () {
    navigator.sendBeacon('../logout.php');  // send a request to logout.php
});
</script>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Dashboard - Go Alive Opportunities</title>

    <link rel="stylesheet" href="admin-dashboard-page.css?v=4">
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

                        <a href="../loginPage/login-page.php" class="login-button"><p>log in</p></a>

                        <a href="https://www.facebook.com/GOAliveNGO" target="_blank"><i class="fab fa-facebook"></i></a>
                        <a href="https://www.instagram.com/go.alive/" target="_blank"><i class="fab fa-instagram"></i></a>
                        <a href="https://www.linkedin.com/company/go-alive/posts/?feedView=all" target="_blank"><i class="fab fa-linkedin"></i></a>
                        <a href="https://www.tiktok.com/@go.alive" target="_blank"><i class="fab fa-tiktok"></i></a>
                    </div>


                </div>

                <div class="main-heading-top">   
                    <h1>WELCOME TO</h1>
                    
                    <div class="days-left-top-section">
                        <p class="number-days">ADMIN PAGE</p>
                    </div>

                    <div class="colorful-heading-top-part">
                
                            <h1>
                                DASHBOARD
                            </h1>

                    </div>


                </div>

            </div>

        </div>

    </div>

    <div class="add-new-event-button-section">

        <h1>ADDING NEW EVENT</h1>
        
        <a href="../adminPages/adding-event-step1.php">
            <button>NEW EVENT</button>
        </a>
        

    </div>

    <div class="active-events-heading">
        <h1>THE ACTIVE EVENTS</h1>
    </div>

    <div class="active-events-section">

    <div class="opportunities-cards-section">

    <?php if (empty($dashboardEvents)): ?>

        <h1 class="no-events-heading">No events available in the dashboard!</h1>

    <?php else: ?>

        <?php foreach ($dashboardEvents as $index => $event): ?>

            <?php
                // Group rows every 3 cards
                if ($index % 1 === 0) {
                    echo '<div class="Opportunities-row">';
                }

                $startDate = date_create($event['start_date']);
            ?>

            <div class="single-card">

                <div class="image">
                    <img src="../MediaFiles/<?= htmlspecialchars($event['event_photo']) ?>" alt="">
                </div>

                <div class="bottom-part">
                    
                    <!-- <div class="date-part-left">

                        <?php
                            $startDate = date_create($event['start_date']);
                            $endDate = date_create($event['end_date']);
                        ?>

                        <div class="date-part-start">
                            <span class="date-part-left-date-number"><?= date_format($startDate, "d") ?> - <?= date_format($endDate, "d") ?></span>
                            <span class="date-part-left-month"><?= strtoupper(date_format($startDate, "M")) ?> - <?= strtoupper(date_format($endDate, "M")) ?></span>
                        </div>

                        <div class="date-part-separator">
                            <span>-</span>
                        </div>

                        <div class="date-part-end">
                            <span class="date-part-left-date-number"><?= date_format($endDate, "d") ?></span>
                            <span class="date-part-left-month"><?= strtoupper(date_format($endDate, "M")) ?></span>
                        </div>
                        
                        
                    </div> -->

                    <!-- <div class="separator-opportunity-card"></div> -->

                    <div class="opportunity-info-right-part">

                        <div class="top-row-date">

                            <div class="full-date-container">
                                <span class="date-part-left-date-number"><?= date_format($startDate, "d") ?></span>
                                <span class="date-part-left-month"><?= strtoupper(date_format($startDate, "M")) ?> </span>
                            </div>

                            <div class="top-row-date-separator">
                                <span> - </span>
                            </div>

                            <div class="full-date-container">
                                <span class="date-part-left-date-number"><?= date_format($endDate, "d") ?></span>
                                <span class="date-part-left-month"><?= strtoupper(date_format($endDate, "M")) ?> </span>
                            </div>
                            
                        </div>

                        <div class="separator-opportunity-card"></div>

                        <h1><?= htmlspecialchars($event['event_name']) ?></h1>

                        <div class="location-div-opportunity-card">
                            <i class="fas fa-map-marker-alt"></i>
                            <p><?= htmlspecialchars($event['city']) ?>,<?= htmlspecialchars($event['country']) ?></p>
                        </div>

                        <p class="description-opportunity-card">
                            <?= htmlspecialchars($event['short_description'] ?? '') ?>
                        </p>
                    </div>
                </div>

                <div class="register-now-button">
                    <!-- <div class="but1-edit">
                        <a href="edit_event.php?id=<?= urlencode($event['id']) ?>"><button>EDIT</button></a>
                    </div> -->

                    <div class="but2-delete">
                        <button onclick="deleteEvent('<?= $event['id'] ?>')">DELETE</button>
                    </div>
                </div>

            </div>

            <?php
                if (($index + 1) % 1 === 0 || $index + 1 === count($dashboardEvents)) {
                    echo '</div>'; // close .Opportunities-row
                }
            ?>

        <?php endforeach; ?>

    <?php endif; ?>

</div>

        <!-- <div class="Opportunities-row">
                <div class="single-card">

                <div class="image">
                    <img src="../MediaFiles/hungary 2.png" alt="">
                </div>

                <div class="bottom-part">

                    <div class="date-part-left">
                        <span class="date-part-left-date-number">28</span>
                        <span class="date-part-left-month">JUN</span>
                    </div>

                    <div class="separator-opportunity-card">

                    </div>

                    <div class="opportunity-info-right-part">

                        <h1>Disconnect to Connect!</h1>

                        <div class="location-div-opportunity-card">
                            <i class="fas fa-map-marker-alt"></i>
                            <p>Hollókő, Hungary</p>
                        </div>

                        <p class="description-opportunity-card">A training course for youth workers focused on offline tools!</p>

                    </div>

                    

                </div>
                
                <div class="register-now-button">

                    <div class="but1-edit">
                         <button>
                            EDIT
                        </button>
                    </div>

                    <div class="but2-delete">
                         <button>
                            DELETE
                        </button>
                    </div>

                </div>

            </div>

            <div class="single-card">

                <div class="image">
                    <img src="../MediaFiles/hungary 2.png" alt="">
                </div>

                <div class="bottom-part">

                    <div class="date-part-left">
                        <span class="date-part-left-date-number">28</span>
                        <span class="date-part-left-month">JUN</span>
                    </div>

                    <div class="separator-opportunity-card">

                    </div>

                    <div class="opportunity-info-right-part">

                        <h1>Disconnect to Connect!</h1>

                        <div class="location-div-opportunity-card">
                            <i class="fas fa-map-marker-alt"></i>
                            <p>Hollókő, Hungary</p>
                        </div>

                        <p class="description-opportunity-card">A training course for youth workers focused on offline tools!</p>

                    </div>

                    

                </div>
                
                <div class="register-now-button">

                    <div class="but1-edit">
                         <button>
                            EDIT
                        </button>
                    </div>

                    <div class="but2-delete">
                         <button>
                            DELETE
                        </button>
                    </div>

                </div>

            </div>

            <div class="single-card">

                <div class="image">
                    <img src="../MediaFiles/hungary 2.png" alt="">
                </div>

                <div class="bottom-part">

                    <div class="date-part-left">
                        <span class="date-part-left-date-number">28</span>
                        <span class="date-part-left-month">JUN</span>
                    </div>

                    <div class="separator-opportunity-card">

                    </div>

                    <div class="opportunity-info-right-part">

                        <h1>Disconnect to Connect!</h1>

                        <div class="location-div-opportunity-card">
                            <i class="fas fa-map-marker-alt"></i>
                            <p>Hollókő, Hungary</p>
                        </div>

                        <p class="description-opportunity-card">A training course for youth workers focused on offline tools!</p>

                    </div>

                    

                </div>
                
                <div class="register-now-button">

                    <div class="but1-edit">
                         <button>
                            EDIT
                        </button>
                    </div>

                    <div class="but2-delete">
                         <button>
                            DELETE
                        </button>
                    </div>

                </div>

            </div>
            </div>
            
            <div class="Opportunities-row">
                <div class="single-card">

                <div class="image">
                    <img src="../MediaFiles/hungary 2.png" alt="">
                </div>

                <div class="bottom-part">

                    <div class="date-part-left">
                        <span class="date-part-left-date-number">28</span>
                        <span class="date-part-left-month">JUN</span>
                    </div>

                    <div class="separator-opportunity-card">

                    </div>

                    <div class="opportunity-info-right-part">

                        <h1>Disconnect to Connect!</h1>

                        <div class="location-div-opportunity-card">
                            <i class="fas fa-map-marker-alt"></i>
                            <p>Hollókő, Hungary</p>
                        </div>

                        <p class="description-opportunity-card">A training course for youth workers focused on offline tools!</p>

                    </div>

                    

                </div>
                
                <div class="register-now-button">

                    <div class="but1-edit">
                         <button>
                            EDIT
                        </button>
                    </div>

                    <div class="but2-delete">
                         <button>
                            DELETE
                        </button>
                    </div>

                </div>

            </div>

            <div class="single-card">

                <div class="image">
                    <img src="../MediaFiles/hungary 2.png" alt="">
                </div>

                <div class="bottom-part">

                    <div class="date-part-left">
                        <span class="date-part-left-date-number">28</span>
                        <span class="date-part-left-month">JUN</span>
                    </div>

                    <div class="separator-opportunity-card">

                    </div>

                    <div class="opportunity-info-right-part">

                        <h1>Disconnect to Connect!</h1>

                        <div class="location-div-opportunity-card">
                            <i class="fas fa-map-marker-alt"></i>
                            <p>Hollókő, Hungary</p>
                        </div>

                        <p class="description-opportunity-card">A training course for youth workers focused on offline tools!</p>

                    </div>

                    

                </div>
                
                <div class="register-now-button">

                    <div class="but1-edit">
                         <button>
                            EDIT
                        </button>
                    </div>

                    <div class="but2-delete">
                         <button>
                            DELETE
                        </button>
                    </div>

                </div>

            </div>

            <div class="single-card">

                <div class="image">
                    <img src="../MediaFiles/hungary 2.png" alt="">
                </div>

                <div class="bottom-part">

                    <div class="date-part-left">
                        <span class="date-part-left-date-number">28</span>
                        <span class="date-part-left-month">JUN</span>
                    </div>

                    <div class="separator-opportunity-card">

                    </div>

                    <div class="opportunity-info-right-part">

                        <h1>Disconnect to Connect!</h1>

                        <div class="location-div-opportunity-card">
                            <i class="fas fa-map-marker-alt"></i>
                            <p>Hollókő, Hungary</p>
                        </div>

                        <p class="description-opportunity-card">A training course for youth workers focused on offline tools!</p>

                    </div>

                    

                </div>
                
                <div class="register-now-button">

                    <div class="but1-edit">
                         <button>
                            EDIT
                        </button>
                    </div>

                    <div class="but2-delete">
                         <button>
                            DELETE
                        </button>
                    </div>

                </div>

            </div>
            </div> -->

    </div>

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