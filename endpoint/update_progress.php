<?php
include('../conn/conn.php');
header('Content-Type: application/json');

$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$position = isset($_POST['position']) ? floatval($_POST['position']) : 0;
$status = isset($_POST['status']) ? $_POST['status'] : null;$rating = isset($_POST['rating']) ? intval($_POST['rating']) : null;
if ($id <= 0) {
    echo json_encode(['status'=>'error','message'=>'Invalid id']);
    exit;
}

try {
    $updates = [];
    $params = [];
    if ($position !== null) {
        $updates[] = "last_read_position = ?";
        $params[] = $position;
        // Auto set status
        if ($position > 0 && $position < 0.95) {
            $updates[] = "read_status = 'reading'";
        } elseif ($position >= 0.95) {
            $updates[] = "read_status = 'finished'";
        }
    }
    if ($status) {
        $updates[] = "read_status = ?";
        $params[] = $status;
    }
    if ($rating !== null) {
        $updates[] = "rating = ?";
        $params[] = $rating;
    }
    $params[] = $id;
    $stmt = $conn->prepare("UPDATE tbl_book SET " . implode(', ', $updates) . " WHERE tbl_book_id = ?");
    $stmt->execute($params);
    echo json_encode(['status'=>'ok']);
} catch (PDOException $e) {
    echo json_encode(['status'=>'error','message'=>$e->getMessage()]);
}

?>
