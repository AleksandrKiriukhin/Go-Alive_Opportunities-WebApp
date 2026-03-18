<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Upload event photo
    $eventPhotoName = '';
    if (isset($_FILES['event_photo']) && $_FILES['event_photo']['error'] === 0) {
        $eventPhotoName = uniqid() . '_' . basename($_FILES['event_photo']['name']);
        // move_uploaded_file($_FILES['event_photo']['tmp_name'], "../MediaFiles/" . $eventPhotoName);
        $mediaFolder = __DIR__ . '/../MediaFiles/';
        move_uploaded_file($_FILES['event_photo']['tmp_name'], $mediaFolder . $eventPhotoName);

    }

    // Upload background photo (resized)
    $backgroundPhotoName = '';
    if (isset($_FILES['background_photo']) && $_FILES['background_photo']['error'] === 0) {
        $backgroundPhotoName = uniqid() . '_' . basename($_FILES['background_photo']['name']);
        $targetPath = "../MediaFiles/" . $backgroundPhotoName;
        move_uploaded_file($_FILES['background_photo']['tmp_name'], $targetPath);
        resizeImage($targetPath, $targetPath, 1440, 522); // Resize
    }

    // Collect form data
    $eventData = [
        'event_name' => $_POST['event_name'],
        'type_of_project' => $_POST['type_of_project'],
        'start_date' => $_POST['start_date'],
        'end_date' => $_POST['end_date'],
        'country' => $_POST['country'],
        'city' => $_POST['city'],
        'city_description' => $_POST['city_description'] ?? '',
        'short_description' => $_POST['short_description'],
        'about' => $_POST['about'],
        'accommodation' => $_POST['accommodation'],
        'travel-reinbursement'=> $_POST['travel-reinbursement'],
        'language' => $_POST['language'],
        'link' => $_POST['link'],
        'participation-cost' => $_POST['participation-cost'],
        'event_photo' => $eventPhotoName,
        'background_photo' => $backgroundPhotoName
    ];

    // Date formatting
    $startDateObj = date_create($eventData['start_date']);
    $endDateObj = date_create($eventData['end_date']);
    $startDay = date_format($startDateObj, 'd');
    $startMonth = strtoupper(date_format($startDateObj, 'M'));
    $endDay = date_format($endDateObj, 'd');
    $endMonth = strtoupper(date_format($endDateObj, 'M'));
    $formattedStartDate = date('d.m.Y', strtotime($eventData['start_date']));
    $formattedEndDate = date('d.m.Y', strtotime($eventData['end_date']));

    // Generate slug & paths
    $randomDigits = rand(10000, 99999);
    $cleanName = preg_replace('/[^a-z0-9]+/i', '-', strtolower($eventData['event_name']));
    $slug = $randomDigits . '-' . $cleanName;
    $filename = "{$slug}.php";
    // $eventPagePath = "../EventsPages/{$filename}";

    // $eventPagePath = __DIR__ . "/../EventsPages/{$filename}";

    // $pageLink = "/Go-alive-website-for-events/EventsPages/{$filename}";
    // $pageLink = "/EventsPages/{$filename}";

    // $pageLink = "/go-alive-opportunities/EventsPages/{$filename}";

    $eventPagePath = __DIR__ . "/../EventsPages/{$filename}";
    $pageLink = "/go-alive-opportunities/EventsPages/{$filename}";

    $eventData['page_link'] = $pageLink;

    // Load template
    // $template = file_get_contents('/template-event-page/event-template.html');

    $template = file_get_contents(__DIR__ . '/../template-event-page/event-template.html');

    // Replacements
    $replacements = [
        '{{title}}' => htmlspecialchars($eventData['event_name']),
        '{{event_name}}' => htmlspecialchars($eventData['event_name']),
        '{{type_of_project}}' => htmlspecialchars($eventData['type_of_project']),
        '{{start_date}}' => $formattedStartDate,
        '{{end_date}}' => $formattedEndDate,
        '{{start_day}}' => $startDay,
        '{{end_day}}' => $endDay,
        '{{start_month}}' => $startMonth,
        '{{end_month}}' => $endMonth,
        '{{city}}' => htmlspecialchars($eventData['city']),
        '{{country}}' => htmlspecialchars($eventData['country']),
        '{{city_description}}' => nl2br(htmlspecialchars($eventData['city_description'])),
        '{{about}}' => nl2br(htmlspecialchars($eventData['about'])),
        '{{accommodation}}' => nl2br(htmlspecialchars($eventData['accommodation'])),
        '{{travel-reinbursement}}' => nl2br(htmlspecialchars($eventData['travel-reinbursement'])),
        '{{language}}' => htmlspecialchars($eventData['language']),
        '{{participation-cost}}' => nl2br(htmlspecialchars($eventData['participation-cost'])),
        '{{link}}' => htmlspecialchars($eventData['link']),
        '{{background_photo}}' => htmlspecialchars($eventData['background_photo']),
        '{{event_photo}}' => htmlspecialchars($eventData['event_photo'])
    ];

    $pageContent = str_replace(array_keys($replacements), array_values($replacements), $template);

    // Save event page
    file_put_contents($eventPagePath, $pageContent);

    // Save to JSON

    $eventData['id'] = uniqid();
    $jsonPath = __DIR__ . '/../data/events.json';

    $allEvents = file_exists($jsonPath) ? json_decode(file_get_contents($jsonPath), true) : [];

    // Prevent duplicate insert
    $exists = false;

    foreach ($allEvents as $event) {
        if ($event['page_link'] === $eventData['page_link']) {
            $exists = true;
            break;
        }
    }

    if (!$exists) {
        $allEvents[] = $eventData;
        file_put_contents($jsonPath, json_encode($allEvents, JSON_PRETTY_PRINT));
    }

    // $eventData['id'] = uniqid();

    // $jsonPath = '/data/events.json';

    // $jsonPath = __DIR__ . '/../data/events.json';

    // $allEvents = file_exists($jsonPath) ? json_decode(file_get_contents($jsonPath), true) : [];
    // $allEvents[] = $eventData;

    // file_put_contents($jsonPath, json_encode($allEvents, JSON_PRETTY_PRINT));

    // Redirect to new event page
    // var_dump($eventPagePath, $pageLink);

    error_log("Event saved: " . $eventData['event_name']);

    header("Location: $pageLink");

    exit;
}

// Resize helper
function resizeImage($sourcePath, $destinationPath, $width, $height) {
    $imageInfo = getimagesize($sourcePath);
    if (!$imageInfo) return false;

    $mime = $imageInfo['mime'];
    switch ($mime) {
        case 'image/jpeg':
            $srcImage = imagecreatefromjpeg($sourcePath); break;
        case 'image/png':
            $srcImage = imagecreatefrompng($sourcePath); break;
        case 'image/gif':
            $srcImage = imagecreatefromgif($sourcePath); break;
        default: return false;
    }

    $resizedImage = imagecreatetruecolor($width, $height);
    $white = imagecolorallocate($resizedImage, 255, 255, 255);
    imagefill($resizedImage, 0, 0, $white);

    imagecopyresampled($resizedImage, $srcImage, 0, 0, 0, 0,
        $width, $height, imagesx($srcImage), imagesy($srcImage));

    imagejpeg($resizedImage, $destinationPath, 90);

    imagedestroy($srcImage);
    imagedestroy($resizedImage);
    return true;
}
?>