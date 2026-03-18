<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: admin.php');
    exit;
}

$id = $_POST['id'] ?? '';
$jsonPath = __DIR__ . '/../data/events.json';
$events = json_decode(file_get_contents($jsonPath), true);

$index = null;
foreach ($events as $i => $e) {
    if ($e['id'] === $id) {
        $index = $i;
        break;
    }
}
if ($index === null) {
    die('Event not found.');
}

$event = $events[$index];

// keep existing images unless new uploaded
$eventPhotoName = $_POST['existing_event_photo'] ?? '';
if (isset($_FILES['event_photo']) && $_FILES['event_photo']['error'] === 0) {
    $eventPhotoName = uniqid() . '_' . basename($_FILES['event_photo']['name']);
    move_uploaded_file($_FILES['event_photo']['tmp_name'], "../mediaFiles/" . $eventPhotoName);
}

$backgroundPhotoName = $_POST['existing_background_photo'] ?? '';
if (isset($_FILES['background_photo']) && $_FILES['background_photo']['error'] === 0) {
    $backgroundPhotoName = uniqid() . '_' . basename($_FILES['background_photo']['name']);
    move_uploaded_file($_FILES['background_photo']['tmp_name'], "../mediaFiles/" . $backgroundPhotoName);
}

// update only fields that were submitted
$fields = [
    'event_name','type_of_project','start_date','end_date','country','city',
    'city_description','short_description','about','accommodation','travel-reinbursement',
    'language','link','participation-cost'
];
foreach ($fields as $f) {
    if (isset($_POST[$f])) {
        $event[$f] = $_POST[$f];
    }
}

$event['event_photo'] = $eventPhotoName;
$event['background_photo'] = $backgroundPhotoName;

// put updated event back in array
$events[$index] = $event;

// Path to template
$templatePath = __DIR__ . '/../template-event-page/event-template.html';
$template = file_get_contents($templatePath);

// Format dates
$startDateObj = date_create($event['start_date']);
$endDateObj = date_create($event['end_date']);
$startDay = date_format($startDateObj, 'd');
$startMonth = strtoupper(date_format($startDateObj, 'M'));
$endDay = date_format($endDateObj, 'd');
$endMonth = strtoupper(date_format($endDateObj, 'M'));
$formattedStartDate = date('d.m.Y', strtotime($event['start_date']));
$formattedEndDate = date('d.m.Y', strtotime($event['end_date']));

// Prepare replacements (same as in submit-event.php)
$replacements = [
    '{{title}}' => htmlspecialchars($event['event_name']),
    '{{event_name}}' => htmlspecialchars($event['event_name']),
    '{{type_of_project}}' => htmlspecialchars($event['type_of_project']),
    '{{start_date}}' => $formattedStartDate,
    '{{end_date}}' => $formattedEndDate,
    '{{start_day}}' => $startDay,
    '{{end_day}}' => $endDay,
    '{{start_month}}' => $startMonth,
    '{{end_month}}' => $endMonth,
    '{{city}}' => htmlspecialchars($event['city']),
    '{{country}}' => htmlspecialchars($event['country']),
    '{{city_description}}' => nl2br(htmlspecialchars($event['city_description'])),
    '{{about}}' => nl2br(htmlspecialchars($event['about'])),
    '{{accommodation}}' => nl2br(htmlspecialchars($event['accommodation'])),
    '{{travel-reinbursement}}' => nl2br(htmlspecialchars($event['travel-reinbursement'])),
    '{{language}}' => htmlspecialchars($event['language']),
    '{{participation-cost}}' => nl2br(htmlspecialchars($event['participation-cost'])),
    '{{link}}' => htmlspecialchars($event['link']),
    '{{background_photo}}' => htmlspecialchars($event['background_photo']),
    '{{event_photo}}' => htmlspecialchars($event['event_photo'])
];

$pageContent = str_replace(array_keys($replacements), array_values($replacements), $template);

// Get the existing filename from JSON
$pageLink = $event['page_link']; // e.g. "/Go-alive-website-for-events/EventsPages/12345-visit-paris-now.php"
$eventPagePath = __DIR__ . '/..' . $pageLink;


// save json
file_put_contents($jsonPath, json_encode($events, JSON_PRETTY_PRINT));

// regenerate the event page here (you can reuse your existing template logic)

header("Location: event.php?id=" . urlencode($id));
exit;
