<?php
include('../conn/conn.php');

// Dynamic base URL for portability - always points to project root
$script_path = $_SERVER['SCRIPT_NAME']; // e.g., /book-catalog-app/educational.php or /book-catalog-app/endpoint/add_book.php
$base_path = '/book-catalog-app/'; // Always use the project root

// Handle subdirectories by ensuring we always point to the project root
if (strpos($script_path, '/book-catalog-app/') !== false) {
    // Extract the base path up to /book-catalog-app/
    $path_parts = explode('/book-catalog-app/', $script_path);
    $base_path = '/book-catalog-app/';
}

$base_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . $base_path;

if (isset($_GET['delete'])) {
    $bookID = $_GET['delete'];

    // Retrieve the book image filename
    $stmt = $conn->prepare("SELECT `book_image` FROM `tbl_book` WHERE `tbl_book_id` = ?");
    $stmt->execute([$bookID]);
    $row = $stmt->fetch();

    $bookImage = $row['book_image'];

    // Delete the book from the database
    $stmt = $conn->prepare("DELETE FROM `tbl_book` WHERE `tbl_book_id` = ?");
    $stmt->execute([$bookID]);

    // Delete the associated image file
    if (!empty($bookImage)) {
        $imagePath = "../image/" . $bookImage;
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
    }

    // Redirect to the page where you want to display the updated book list
    header("Location: " . $base_url . "index.php");
    exit();
}
?>
