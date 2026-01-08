<?php
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
include('conn/conn.php');
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) { echo "Invalid book id"; exit; }
$stmt = $conn->prepare("SELECT book_title, book_text, read_status, last_read_position, rating, book_image, book_category, book_author FROM tbl_book WHERE tbl_book_id = ?");
$stmt->execute([$id]);
$book = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$book || empty($book['book_text'])) { echo "Book text not found."; exit; }
$position = $book['last_read_position'] ?? 0;
$status = $book['read_status'] ?? 'not_started';
$rating = $book['rating'] ?? 0;
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reader - <?php echo htmlspecialchars($book['book_title']); ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style> 
        body {
            margin: 0;
            padding: 0;
        }
        .book-text { 
            white-space: pre-wrap; 
            font-family: serif; 
            font-size: 20px; 
            line-height: 1.6; 
            padding: 20px; 
            margin: 0 auto;
            max-width: 900px;
            max-height: 85vh; 
            overflow-y: auto; 
            text-align: justify;
        } 
    </style>
</head>
<body>
    <div class="d-flex align-items-center mb-2" style="padding: 10px 20px;">
        <a href="index.php" class="btn btn-sm btn-outline-dark mr-2" title="Back to Library"><i class="fas fa-arrow-left"></i></a>
        <h4><?php echo htmlspecialchars($book['book_title']); ?></h4>
        <div class="ml-auto">
            <button id="markReading" class="btn btn-sm btn-outline-secondary">Mark as Reading</button>
            <button id="markFinished" class="btn btn-sm btn-outline-secondary">Mark as Finished</button>
        </div>
    </div>

    <div class="book-text" id="bookText">
        <?php echo htmlspecialchars($book['book_text']); ?>
    </div>

    <!-- Rating Modal -->
    <div class="modal fade" id="ratingModal" tabindex="-1" aria-labelledby="ratingModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ratingModalLabel">Rate "<?php echo htmlspecialchars($book['book_title']); ?>"</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <div class="mb-3">
                        <img src="<?php echo $base_url; ?>image/<?php echo $book['book_image']; ?>" alt="Book Cover" style="width: 100px; height: auto; border-radius: 5px;">
                    </div>
                    <p><strong>Category:</strong> <?php echo $book['book_category']; ?></p>
                    <p><strong>Author:</strong> <?php echo htmlspecialchars($book['book_author']); ?></p>
                    <p><strong>Status:</strong> <?php echo ucfirst(str_replace('_', ' ', $book['read_status'])); ?></p>
                    <hr>
                    <p>How would you rate this book?</p>
                    <div id="ratingStars">
                        <?php for($i=1; $i<=5; $i++): ?>
                            <i class="fas fa-star" data-rating="<?php echo $i; ?>" style="color: <?php echo $i <= $rating ? 'gold' : 'gray'; ?>; cursor: pointer; font-size: 30px;"></i>
                        <?php endfor; ?>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Skip</button>
                    <button type="button" class="btn btn-primary" id="saveRating">Save Rating</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js" integrity="sha384-+sLIOodYLS7CIrQpBjl+C7nPvqq+FbNUBDunl/OZv93DB7Ln/533i8e/mZXLi/P+" crossorigin="anonymous"></script>

    <script>
        const bookId = <?php echo $id; ?>;
        const initialPosition = <?php echo $position; ?>;
        let saveTimer = null;

        // Set initial scroll position
        document.addEventListener('DOMContentLoaded', function() {
            const textDiv = document.getElementById('bookText');
            textDiv.scrollTop = initialPosition * textDiv.scrollHeight;
        });

        // Save progress on scroll
        document.getElementById('bookText').addEventListener('scroll', function() {
            const textDiv = this;
            const position = textDiv.scrollTop / textDiv.scrollHeight;
            if (saveTimer) clearTimeout(saveTimer);
            saveTimer = setTimeout(() => {
                fetch('endpoint/update_progress.php', {
                    method: 'POST',
                    headers: {'Content-Type':'application/x-www-form-urlencoded'},
                    body: 'id=' + encodeURIComponent(bookId) + '&position=' + encodeURIComponent(position)
                }).then(r => r.json()).then(data => {
                    console.log('progress saved', data);
                }).catch(err => console.error('save failed', err));
            }, 1000);
        });

        // Mark as reading
        document.getElementById('markReading').addEventListener('click', function() {
            fetch('endpoint/update_progress.php', {
                method: 'POST',
                headers: {'Content-Type':'application/x-www-form-urlencoded'},
                body: 'id=' + encodeURIComponent(bookId) + '&status=reading'
            }).then(r => r.json()).then(data => {
                alert('Marked as reading');
            });
        });

        // Mark as finished
        document.getElementById('markFinished').addEventListener('click', function() {
            fetch('endpoint/update_progress.php', {
                method: 'POST',
                headers: {'Content-Type':'application/x-www-form-urlencoded'},
                body: 'id=' + encodeURIComponent(bookId) + '&status=finished'
            }).then(r => r.json()).then(data => {
                alert('Marked as finished');
                $('#ratingModal').modal('show');
            });
        });

        // Rating in modal
        let currentRating = <?php echo $rating; ?>;
        document.querySelectorAll('#ratingStars i[data-rating]').forEach(star => {
            star.addEventListener('click', function() {
                currentRating = this.dataset.rating;
                document.querySelectorAll('#ratingStars i[data-rating]').forEach(s => {
                    s.style.color = s.dataset.rating <= currentRating ? 'gold' : 'gray';
                });
            });
        });

        // Save rating
        document.getElementById('saveRating').addEventListener('click', function() {
            fetch('endpoint/update_progress.php', {
                method: 'POST',
                headers: {'Content-Type':'application/x-www-form-urlencoded'},
                body: 'id=' + encodeURIComponent(bookId) + '&rating=' + encodeURIComponent(currentRating)
            }).then(r => r.json()).then(data => {
                $('#ratingModal').modal('hide');
                alert('Rating saved!');
            });
        });
    </script>
</body>
</html>
