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

// if (isset($_SESSION['error'])) {
//     echo "<div style='background:#ffcccc; color:#990000; padding:10px; margin-bottom:10px; border:1px solid #990000; font-weight:bold; text-align:center;'>"
//         . $_SESSION['error'] . "</div>";
//     unset($_SESSION['error']);
// }

$eventData = [
  'event_name' => '',
  'type_of_project' => '',
  'start_date' => '',
  'end_date' => '',
  'country' => '',
  'city' => '',
  'city_description' => '',
  'short_description' => '',
  'about' => '',
  'accommodation' => '',
  'travel-reinbursement' => '',
  'language' => '',
  'participation-cost' => '',
  'link' => '',
  'event_photo' => '',
  'background_photo' => ''
];


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $_SESSION['event'] = [
        "event_name" => $_POST["event_name"],
        "start_date" => $_POST["start_date"],
        "event_location" => $_POST["event_location"],
        "short_description" => $_POST["short_description"],
        "event_photo" => $_FILES["event_photo"]["name"]
    ];

    move_uploaded_file($_FILES["event_photo"]["tmp_name"], "../mediaFiles/" . $_FILES["event_photo"]["name"]);

    header("Location: adding-event-step2.php");
    exit;
}
?>

<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form.form-structure');
    const errorDiv = document.getElementById('dateError');

    form.addEventListener('submit', function(event) {
        const startDate = new Date(form.querySelector('input[name="start_date"]').value);
        const endDate = new Date(form.querySelector('input[name="end_date"]').value);

        if (endDate < startDate) {
            event.preventDefault();
            errorDiv.textContent = '❌ End date cannot be earlier than start date.';
            errorDiv.style.display = 'block';
        } else {
            errorDiv.style.display = 'none';
        }
    });
});

window.addEventListener('beforeunload', function () {
    navigator.sendBeacon('../logout.php');  // send a request to logout.php
});

</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form'); // adjust selector if needed
    form.addEventListener('submit', function(event) {
        const startDate = new Date(document.querySelector('[name="start_date"]').value);
        const endDate = new Date(document.querySelector('[name="end_date"]').value);

        if (endDate < startDate) {
            event.preventDefault(); // stop form submission

            let alertDiv = document.querySelector('#date-alert');
            if (!alertDiv) {
                alertDiv = document.createElement('div');
                alertDiv.id = 'date-alert';
                alertDiv.style.background = '#ffcccc';
                alertDiv.style.color = '#990000';
                alertDiv.style.padding = '10px';
                alertDiv.style.marginBottom = '10px';
                alertDiv.style.border = '1px solid #990000';
                alertDiv.style.fontWeight = 'bold';
                alertDiv.style.textAlign = 'center';
                alertDiv.textContent = '❌ End date cannot be earlier than start date.';
                form.prepend(alertDiv);
            }
        }
    });
});
</script>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adding Event - Go Alive Opportunities</title>

    <link rel="stylesheet" href="adding-event-step1.css?v=4">
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

                        <a href="loginPage/login-page.php" class="login-button"><p>log in</p></a>

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
                                ADDING EVENT
                            </h1>

                    </div>


                </div>

            </div>

        </div>

    </div>

    <div class="card-example-heading">
        <h1>EVENT CREATION PROCESS</h1>
        <p>Provide all the necessary information about an event here!</p>
    </div>

    <!-- <div class="card-example-section">

        <div class="example-img-section-left">
            <img src="../MediaFiles/card-example-2.png" alt="">
        </div>

        <div class="right-section">
            <img src="../MediaFiles/arrow-red-example-section.png" alt="">
            <p>EXAMPLE</p>
        </div>

    </div> -->

    <div class="form-fillin-heading">
        <h1>FILL IN THE FORM FOR THE EVENT</h1>
    </div>

    <form method="post" action="submit-event.php" enctype="multipart/form-data" class="form-structure">

                <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">

                <div class="form-part-input">
                    <p>Project Name</p>
                    <input type="text" name="event_name" class="gradient-input-form-part" placeholder="Project Name" required>
                </div>

                <div class="form-part-input">
                    <p>Type of project</p>
                    <input list="project-types" name="type_of_project" class="gradient-input-form-part" placeholder="Type of project" required>
                        <datalist id="project-types">
                            <option value="Youth Exchange">
                            <option value="Training Course">
                            <option value="Project Meeting">
                            <option value="Job Shadowing">
                            <option value="Advance Planning Visit (APV)">
                        </datalist>
                </div>

                <div class="form-part-input">
                    <p>Project Start Date</p>
                    <input type="date" name="start_date" class="gradient-input-form-part" placeholder="Project Start Date" required>
                </div>

                <div class="form-part-input">
                    <p>Project End Date</p>
                    <input type="date" name="end_date" class="gradient-input-form-part" placeholder="Project End Date" required>
                </div>

                <div class="form-part-input">
                    <p>Project country</p>
                    <input type="text" name="country" class="gradient-input-form-part" placeholder="Project country" required>
                </div>

                <div class="form-part-input">
                    <p>Project city</p>
                    <input type="text" name="city" class="gradient-input-form-part" placeholder="Project city" required>
                </div>

                <div class="form-part-input">
                    <p>Project SHORT description</p>
                    <input type="text" name="short_description" class="gradient-input-form-part" placeholder="Project SHORT description" required>
                </div>

                <div class="form-part-input">
                    <p>Project objectives</p>
                    <textarea type="text" name="about" class="gradient-input-form-part" placeholder="Project objectives" required> </textarea>
                </div>

                <div class="form-part-input">
                    <p>Accommodation</p>
                    <textarea type="text" name="accommodation" class="gradient-input-form-part" placeholder="Accommodation " required> </textarea>
                </div>

                <div class="form-part-input">
                    <p>Travel reimbursement</p>
                    <textarea type="text" name="travel-reinbursement" class="gradient-input-form-part" placeholder="Travel reimbursemen" required> </textarea>
                </div>

                <div class="form-part-input">
                    <p>Working language</p>
                    <input type="text" name="language" class="gradient-input-form-part" placeholder="Working language" required>
                </div>

                <div class="form-part-input">
                    <p>Participation cost</p>
                    <textarea type="text" name="participation-cost" class="gradient-input-form-part" placeholder="Participation cost" required> </textarea>
                </div>

                <div class="form-part-input">
                    <p>Link to Google Form </p>
                    <input type="text" name="link" class="gradient-input-form-part" placeholder="Link to Google Forms" required>
                </div>

                <div class="form-part-input">
                    <p>Project photo</p>
                    <input type="file" name="event_photo" class="gradient-input-form-part" accept="image/*" placeholder="Project photo">
                </div>

                <div id="dateError" style="color: #990000; background: #ffcccc; padding: 10px; margin-bottom: 10px; border: 1px solid #990000; font-weight: bold; text-align: center; display: none;"></div>

                <button type="submit">SUBMIT</button>

    </form>

    

    <div class="footer-section">

        <div class="footer-section-inside">

            <div class="left-part">

                <div class="logo-part-footer">
                    <img src="../MediaFiles/logo slogan.png" alt="">
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