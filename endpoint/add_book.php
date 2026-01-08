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

function uploadFile($fileInputName, $destDir, $allowedExt = []) {
    if (empty($_FILES[$fileInputName]['name'])) return null;
    $fileName = basename($_FILES[$fileInputName]['name']);
    $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    if (!empty($allowedExt) && !in_array($ext, $allowedExt)) {
        throw new Exception('Invalid file type for ' . $fileInputName);
    }
    if (!is_dir($destDir)) mkdir($destDir, 0755, true);
    $safeName = time() . '_' . preg_replace('/[^A-Za-z0-9_\-\.]/','_', $fileName);
    $target = $destDir . DIRECTORY_SEPARATOR . $safeName;
    if (!move_uploaded_file($_FILES[$fileInputName]['tmp_name'], $target)) {
        throw new Exception('Upload failed for ' . $fileInputName);
    }
    return $safeName;
}

try {
    $bookTitle = $_POST['book_title'] ?? '';
    $bookCategory = $_POST['book_category'] ?? '';
    $bookAuthor = $_POST['book_author'] ?? '';
    $bookAbstract = $_POST['book_abstract'] ?? '';
    $bookText = $_POST['book_text'] ?? '';

    // handle image upload (optional)
    $bookImage = null;
    if (!empty($_FILES['book_image']['name'])) {
        $imageDir = __DIR__ . '/../image';
        $bookImage = uploadFile('book_image', $imageDir, ['jpg','jpeg','png','gif']);
    }

    $stmt = $conn->prepare("INSERT INTO `tbl_book` (`tbl_book_id`,`book_image`, `book_title`, `book_category`, `book_author`, `book_abstract`, `book_text`, `time_added`) VALUES (NULL, :bookImage, :bookTitle, :bookCategory, :bookAuthor, :bookAbstract, :bookText, NOW())");
    $stmt->bindParam(':bookImage', $bookImage);
    $stmt->bindParam(':bookTitle', $bookTitle);
    $stmt->bindParam(':bookCategory', $bookCategory);
    $stmt->bindParam(':bookAuthor', $bookAuthor);
    $stmt->bindParam(':bookAbstract', $bookAbstract);
    $stmt->bindParam(':bookText', $bookText);
    $stmt->execute();

    echo "<script>
        alert('Upload Success!'); 
        window.location.href = '" . $base_url . "index.php';
        </script>";

} catch (Exception $e) {
    echo "<script>
        alert('Error: " . addslashes($e->getMessage()) . "'); 
        window.location.href = '" . $base_url . "index.php';
        </script>";
}

?>
