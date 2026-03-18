<?php

header("Cache-Control: no-cache, no-store, must-revalidate"); 
header("Pragma: no-cache"); 
header("Expires: 0");

$eventsFile = __DIR__ . "/data/events.json";

$events = [];

if (file_exists($eventsFile)) {
    $json = file_get_contents($eventsFile);
    $events = json_decode($json, true);
}

$latestEvent = null;
$daysLeft = null;

if (file_exists($eventsFile)) {
    $jsonContent = file_get_contents($eventsFile);
    $eventsData = json_decode($jsonContent, true);

    if (is_array($eventsData) && !empty($eventsData)) {
        $today = new DateTime();

        // Filter to only events that haven't started yet or start today
        $upcomingEvents = array_filter($eventsData, function ($event) use ($today) {
            $startDate = new DateTime($event['start_date']);
            return $startDate >= $today;
        });

        if (!empty($upcomingEvents)) {
            // Sort by soonest start_date
            usort($upcomingEvents, function ($a, $b) {
                return strtotime($a['start_date']) - strtotime($b['start_date']);
            });

            // Pick the soonest event
            $latestEvent = $upcomingEvents[0];

            // Calculate days left
            $startDate = new DateTime($latestEvent['start_date']);
            $interval = $today->diff($startDate);
            $daysLeft = (int)$interval->format('%r%a');
        } else {
            // No upcoming events
            $daysLeft = 0;
        }
    }
} else {
    $latestEvent = null;
    $daysLeft = 0;
}

?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Go Alive Opportunities</title>
    <link rel="stylesheet" href="index-style.css?v=4">
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
                        <a href="index.php">
                            <img src="MediaFiles/logo slogan.png" alt="">
                        </a>
                    </div>

                    <div class="social-media-part">

                        <a href="loginPage/login-page.php" class="login-button"><p>log in</p></a>

                        <a href="https://www.facebook.com/GOAliveNGO" target="_blank"><i class="fab fa-facebook"></i></a>
                        <a href="https://www.instagram.com/go.alive/" target="_blank"><i class="fab fa-instagram"></i></a>
                        <a href="https://www.linkedin.com/company/go-alive/posts/?feedView=all" target="_blank"><i class="fab fa-linkedin"></i></a>
                        <a href="https://www.tiktok.com/@go.alive" target="_blank"><i class="fab fa-tiktok"></i></a>
                    </div>


                </div>

                <!-- <div class="main-heading-top">   
                    <h1>DON’T MISS THE UPCOMING EVENT!</h1>
                    
                    <div class="days-left-top-section">
                        <p class="number-days">17</p>
                        <p class="days-left">days left!</p>
                    </div>


                </div> -->

                <div class="main-heading-top">   
                    <h1>
                        <?= $latestEvent ? "DON’T MISS THE UPCOMING EVENT!" : "No upcoming events found" ?>
                    </h1>

                    <?php if ($latestEvent): ?>

                        <div class="days-left-top-section">
                            <p class="number-days"><?= $daysLeft ?> </p>
                            <p class="days-left"> days left!</p>
                         </div>
                    <?php else: ?>
                        <div class="days-left-top-section">
                            <p class="number-days-1">STAY TUNED FOR FUTURE EVENTS!</p>
                        </div>
                    <?php endif; ?>
                </div>

            </div>

        </div>

        <!-- <div class="hot-event-card">

            <div class="image-hot-card">
                <img src="MediaFiles/hungary pic.png" alt="">
            </div>

            <div class="event-info">

                <div class="date-part">
                    <span class="date-part-date-number">28</span>
                    <span class="date-part-month">JUN</span>
                </div>

                <div class="separator-hot-offer-card">

                </div>

                <div class="info-part-right">

                    <h1>Disconnect to Connect!</h1>

                    <div class="location-div-hot-offer">
                        <i class="fas fa-map-marker-alt"></i>
                        <p>Hollókő, Hungary</p>
                    </div>

                    <p class="description-hot-offer-card">A training course for youth workers focused on offline tools!</p>
                    
                </div>

                <div class="button-more-info">


                    <a href="EventsPages/example.php">

                        <a href="EventsPages/example.php">
                            <button>
                                MORE INFO
                            </button>
                        </a>
                        
                    </a>
                    
                </div>

            </div>

        </div> -->

        <?php if ($latestEvent): ?>

            <div class="hot-event-card">

                <div class="image-hot-card">
                    <img src="MediaFiles/<?= htmlspecialchars($latestEvent['event_photo']) ?>" alt="<?= htmlspecialchars($latestEvent['event_name']) ?>">
                </div>

            <div class="event-info">

                <div class="date-part">
                    <?php
                        $startDateObj = date_create($latestEvent['start_date']);
                        $endDateObj = date_create($latestEvent['end_date']);

                        $startDay = date_format($startDateObj, 'd');
                        $endDay = date_format($endDateObj, 'd');

                        $startMonth = strtoupper(date_format($startDateObj, 'M'));
                        $endMonth = strtoupper(date_format($endDateObj, 'M'));
                    ?>

                    <span class="date-part-date-number"><?= $startDay ?> - <?= $endDay ?></span>
                    <span class="date-part-month"><?= $startMonth ?> - <?= $endMonth ?></span>

                </div>

                <div class="separator-hot-offer-card"></div>

                <div class="info-part-right">

                    <h1><?= htmlspecialchars($latestEvent['event_name']) ?></h1>

                    <div class="location-div-hot-offer">
                        <i class="fas fa-map-marker-alt"></i>
                        <p><?= htmlspecialchars($latestEvent['city']) ?>, <?= htmlspecialchars($latestEvent['country']) ?></p>
                    </div>

                    <p class="description-hot-offer-card"><?= htmlspecialchars($latestEvent['short_description']) ?></p>
                </div>

                <div class="button-more-info">
                    <a href="<?= htmlspecialchars($latestEvent['page_link']) ?>">
                        <button>
                            APPLY NOW!
                        </button>
                    </a>
                </div>

            </div>

    </div>

    <?php else: ?>
        
    <div class="hot-event-card">
        
        <div class="hot-event-card-no-event">
            <div class="no-event-left-part">
                <p class="no-event-p1">
                    NO UPCOMING 
                    <br>EVENTS SO FAR!
                </p>
                <p class="no-event-p2">STAY TUNED FOR MORE!</p>
            </div> 

            <div class="no-event-right-part">
                <img src="MediaFiles/goalive logo black.png" alt="">
            </div> 
        </div> 

    </div>

    <?php endif; ?>

    </div>

        <div class="opportunities-heading-button">
            
            <h1>
                MORE AMAZING OPPORTUNITIES!
            </h1>

            <div class="explore-now-button">
                <button>
                    EXPLORE NOW!
                </button>
            </div>

        </div>

    <div class="more-opportunities-section">
    
        

        <!-- <div class="opportunities-cards-section">
            
            <div class="Opportunities-row">
                <div class="single-card">

                <div class="image">
                    <img src="/MediaFiles/hungary 2.png" alt="">
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
                        <button>
                            REGISTER NOW!
                        </button>
                </div>

            </div>

            <div class="single-card">

                <div class="image">
                    <img src="/MediaFiles/hungary 2.png" alt="">
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
                        <button>
                            REGISTER NOW!
                        </button>
                </div>

            </div>

            <div class="single-card">

                <div class="image">
                    <img src="/MediaFiles/hungary 2.png" alt="">
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
                        <button>
                            REGISTER NOW!
                        </button>
                </div>

            </div>
            </div>
            
            <div class="Opportunities-row">
                <div class="single-card">

                <div class="image">
                    <img src="/MediaFiles/hungary 2.png" alt="">
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
                        <button>
                            REGISTER NOW!
                        </button>
                </div>

            </div>

            <div class="single-card">

                <div class="image">
                    <img src="/MediaFiles/hungary 2.png" alt="">
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
                        <button>
                            REGISTER NOW!
                        </button>
                </div>

            </div>

            <div class="single-card">

                <div class="image">
                    <img src="/MediaFiles/hungary 2.png" alt="">
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
                        <button>
                            REGISTER NOW!
                        </button>
                </div>

            </div>
            </div>


        </div> -->

        <div class="opportunities-cards-section">

        <?php if (empty($events)): ?>

            <h1 class="no-events-heading">Unfortunately, no events are available so far!</h1>

        <?php else: ?>

        <?php foreach ($events as $index => $event): ?>

            <?php
           
                if ($index % 1 === 0) {
                    echo '<div class="Opportunities-row">';
                }

                $startDate = date_create($event['start_date']);
                $endDate = date_create($event['end_date']);

            ?>

            <div class="single-card">
                
                <div class="image">
                    <img src="MediaFiles/<?= htmlspecialchars($event['event_photo']) ?>" alt="">
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
                    <a href="<?= htmlspecialchars($event['page_link']) ?>">
                    <button>REGISTER NOW!</button>
                    </a>
                </div>
                
            </div>

            <?php
            
            if (($index + 1) % 1 === 0 || $index + 1 === count($events)) {
                echo '</div>'; 
            }
            ?>

        <?php endforeach; ?>

        <?php endif; ?>

        
        </div>

    </div>
    

    <div class="more-about-us-section">

        <div class="find-out-more-container">

            <h1>
                FIND OUT MORE ABOUT US!
            </h1>

            <div class="button-our-website">
                <a href="https://goalive.eu/" target="_blank">
                    <button>
                        OUR WEBSITE!
                    </button>
                </a>
            </div>

        </div>

    </div>

    <div class="follow-on-social-media-section">

        <h1>
            FOLLOW US ON OUR SOCIAL MEDIA!
        </h1>

        <div class="social-media-section-container">

            <div class="social-media-box">
                <a href="https://www.facebook.com/GOAliveNGO" target="_blank">
                    <i class="fab fa-facebook"></i>
                </a>
                <p>FACEBOOK</p>
            </div>
            <div class="social-media-box">
                <a href="https://www.instagram.com/go.alive/" target="_blank">
                    <i class="fab fa-instagram"></i>
                </a>
                <p>INSTAGRAM</p>
            </div>
            <div class="social-media-box">
                <a href="https://www.linkedin.com/company/go-alive/posts/?feedView=all" target="_blank">
                    <i class="fab fa-linkedin"></i>
                </a>
                <p>LINKEDIN</p>
            </div>
            <div class="social-media-box">
                <a href="https://www.tiktok.com/@go.alive" target="_blank" class="links-to-social-section">
                    <i class="fab fa-tiktok"></i>
                </a>
                <p>TIKTOK</p>
            </div>

        </div>

    </div>

    <div class="footer-section">

        <div class="footer-section-inside">

            <div class="left-part">

                <div class="logo-part-footer">
                    <img src="MediaFiles/logo slogan.png" alt="">
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