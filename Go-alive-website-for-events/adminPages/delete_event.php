<?php

$basePath = $_SERVER['DOCUMENT_ROOT'] . "/go-alive-opportunities/";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $eventId = $_POST['id'] ?? '';

    if (!$eventId) {
        http_response_code(400);
        echo "Missing event ID";
        exit;
    }

    // $jsonPath = '../data/events.json';

    $jsonPath = $basePath . "data/events.json";

    if (!file_exists($jsonPath)) {
        http_response_code(404);
        echo "Events file not found";
        exit;
    }

    $events = json_decode(file_get_contents($jsonPath), true);
    $eventIndex = array_search($eventId, array_column($events, 'id'));

    if ($eventIndex === false) {
        http_response_code(404);
        echo "Event not found";
        exit;
    }

    $event = $events[$eventIndex];

    // 1. Delete event page

    // if (!empty($event['page_link'])) {
    //     $pageFile = $_SERVER['DOCUMENT_ROOT'] . $event['page_link'];
    //     if (file_exists($pageFile)) {
    //         unlink($pageFile);
    //     }
    // }

    if (!empty($event['page_link'])) {

        $pageFile = $_SERVER['DOCUMENT_ROOT'] . $event['page_link'];

            if (file_exists($pageFile)) {

                    unlink($pageFile);

            }
    }

    // 2. Delete event photo
    if (!empty($event['event_photo'])) {
        // $eventPhotoPath = "../mediaFiles/" . $event['event_photo'];

        $eventPhotoPath = $basePath . "MediaFiles/" . $event['event_photo'];

        if (file_exists($eventPhotoPath)) {
            unlink($eventPhotoPath);
        }
    }

    // 3. Delete background photo
    if (!empty($event['background_photo'])) {

        // $backgroundPhotoPath = "../mediaFiles/" . $event['background_photo'];

        $backgroundPhotoPath = $basePath . "MediaFiles/" . $event['background_photo'];
        
        if (file_exists($backgroundPhotoPath)) {
            unlink($backgroundPhotoPath);
        }
    }

    // 4. Remove from JSON
    array_splice($events, $eventIndex, 1);
    file_put_contents($jsonPath, json_encode($events, JSON_PRETTY_PRINT));

    echo "success";
}
