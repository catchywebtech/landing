<?php
// Set headers
header('Content-Type: application/json');

// --- CONFIGURATION ---
// IMPORTANT: Set your business WhatsApp number here (including country code, without + or spaces)
$business_whatsapp_number = '911234567890'; // Example for India: +91 12345 67890

// --- DATABASE CONNECTION ---
// IMPORTANT: Replace with your actual database credentials.
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'cw_landing_page';

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
    exit();
}

// --- FORM PROCESSING ---
$response = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Sanitize and validate inputs
    $name = trim(filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING));
    $service = trim(filter_input(INPUT_POST, 'service', FILTER_SANITIZE_STRING));
    $contact_number = trim(filter_input(INPUT_POST, 'contact_number', FILTER_SANITIZE_STRING));
    $whatsapp_number = trim(filter_input(INPUT_POST, 'whatsapp_number', FILTER_SANITIZE_STRING));
    $location = trim(filter_input(INPUT_POST, 'location', FILTER_SANITIZE_STRING)); // Optional

    // Basic Validation
    if (empty($name) || empty($service) || empty($contact_number) || empty($whatsapp_number)) {
        $response = ['success' => false, 'message' => 'Please fill in all required fields.'];
        echo json_encode($response);
        exit();
    }
    
    if (!preg_match('/^[0-9\s\-\+\(\)]{7,20}$/', $contact_number) || !preg_match('/^[0-9\s\-\+\(\)]{7,20}$/', $whatsapp_number)) {
        $response = ['success' => false, 'message' => 'Please enter a valid contact/WhatsApp number.'];
        echo json_encode($response);
        exit();
    }

    // --- DATABASE INSERTION ---
    try {
        $sql = "INSERT INTO contact_submissions (name, service, contact_number, whatsapp_number, location) VALUES (:name, :service, :contact_number, :whatsapp_number, :location)";
        $stmt = $pdo->prepare($sql);
        
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':service', $service);
        $stmt->bindParam(':contact_number', $contact_number);
        $stmt->bindParam(':whatsapp_number', $whatsapp_number);
        $stmt->bindParam(':location', $location);
        
        $stmt->execute();
        
        $whatsapp_link = "https://wa.me/" . $business_whatsapp_number;

        $response = [
            'success' => true, 
            'message' => 'Thank You!',
            'whatsapp_link' => $whatsapp_link
        ];

    } catch (PDOException $e) {
        $response = ['success' => false, 'message' => 'Error: Could not save your inquiry. Please try again.'];
    }

} else {
    $response = ['success' => false, 'message' => 'Invalid request method.'];
}

echo json_encode($response);
?>
