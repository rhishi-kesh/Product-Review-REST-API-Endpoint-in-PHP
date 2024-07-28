<?php

    header('Content-Type: application/json');
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: POST');
    header('Access-Control-Allow-Headers: Content-Type');

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['error' => 'Invalid request method. Only POST is allowed.']);
        exit;
    }

    $input = json_decode(file_get_contents('php://input'), true);

    $product_id = isset($input['product_id']) ? (int)$input['product_id'] : null;
    $user_id = isset($input['user_id']) ? (int)$input['user_id'] : null;
    $review = isset($input['review']) ? trim($input['review']) : null;

    if (is_null($product_id) || $product_id <= 0) {
        echo json_encode(['error' => 'Invalid product ID.']);
        exit;
    }

    if (is_null($user_id) || $user_id <= 0) {
        echo json_encode(['error' => 'Invalid user ID.']);
        exit;
    }

    if (empty($review)) {
        echo json_encode(['error' => 'Review text cannot be empty.']);
        exit;
    }

    $response = [];

    // Replace with your actual database credentials
    $host = 'localhost';
    $db = 'product_review';
    $user = 'root';
    $pass = '';

    // Create connection
    $mysqli = new mysqli($host, $user, $pass, $db);

    // Check connection
    if ($mysqli->connect_error) {
        echo json_encode(['error' => 'Database connection failed: ' . $mysqli->connect_error]);
        exit;
    }

    
    // Prepare and bind
    $date = date('Y-m-d H:i:s');
    $stmt = $mysqli->prepare('INSERT INTO reviews (product_id, user_id, review, created_at) VALUES (?, ?, ?, ?)');
    if ($stmt) {
        $stmt->bind_param('iiss', $product_id, $user_id, $review, $date);
        if ($stmt->execute()) {
            $response['message'] = 'Review submitted successfully.';
        } else {
            $response['error'] = 'Execute error: ' . $stmt->error;
        }
        $stmt->close();
    } else {
        $response['error'] = 'Prepare error: ' . $mysqli->error;
    }

    // Close connection
    $mysqli->close();

    echo json_encode($response);
?>
