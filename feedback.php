<?php
session_start();
include 'db.php'; // Ensure your db connection is correct

// Redirect to login page if the user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$error = '';
$feedback_content = '';
$message = ''; // Variable for feedback messages

// Handle the form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['feedback'])) {
    $feedback_content = trim($_POST['feedback']); // Get the feedback

    // Check if feedback is empty
    if (!empty($feedback_content)) {
        try {
            // Prepare the SQL query to insert feedback into the database
            $stmt = $conn->prepare("INSERT INTO feedback (user_id, feedback, created_at) VALUES (?, ?, NOW())");
            $stmt->bind_param("is", $_SESSION['user_id'], $feedback_content); // Bind parameters

            // Execute the query
            if ($stmt->execute()) {
                $message = 'Thank you for sharing your valuable feedback!';

                // Log the user out by destroying the session
                session_unset(); // Clear all session variables
                session_destroy(); // Destroy the session
            } else {
                $message = 'Error: Could not submit your feedback. Please try again later.';
            }
        } catch (Exception $e) {
            $message = 'Error: ' . $e->getMessage();
        }
    } else {
        $message = 'Feedback cannot be empty!';
    }
}

// Retrieve all feedback from the database to display below the form
$sql = "SELECT f.feedback, f.created_at, u.username FROM feedback f 
        JOIN users u ON f.user_id = u.id 
        ORDER BY f.created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback</title>
    <link rel="stylesheet" href="Feedback.css">
    <script>
        // Function to display a styled notification
        function showNotification(message, isError = false) {
            const notification = document.createElement('div');
            notification.className = 'notification';
            if (isError) {
                notification.classList.add('error');
            }
            notification.textContent = message;
            document.body.appendChild(notification);
            notification.style.display = 'block';
            setTimeout(() => {
                notification.style.opacity = 0;
                setTimeout(() => notification.remove(), 500);
            }, 3000);
        }

        // Function to sort the comments
        function sortComments(criteria) {
            const feedbackList = document.querySelector('.feedback-list');
            const feedbackItems = Array.from(feedbackList.children);

            feedbackItems.sort((a, b) => {
                const aDate = new Date(a.dataset.createdAt);
                const bDate = new Date(b.dataset.createdAt);
                const aUsername = a.dataset.username.toLowerCase();
                const bUsername = b.dataset.username.toLowerCase();

                if (criteria === 'newest') {
                    return bDate - aDate; // Sort by newest first
                } else if (criteria === 'oldest') {
                    return aDate - bDate; // Sort by oldest first
                } else if (criteria === 'username') {
                    return aUsername.localeCompare(bUsername); // Sort alphabetically by username
                }
                return 0;
            });

            // Reorder the items in the list based on the sorted order
            feedbackList.innerHTML = '';
            feedbackItems.forEach(item => feedbackList.appendChild(item));
        }
    </script>
</head>
<body>
    <nav>
        <a href="Gui.html">Home</a>
        <a href="About.html">About</a>
        <img src="Elb.png" alt="Icon" class="nav-icon">
        <a href="Menu.html">Menu</a>
        <a href="feedback.php">Feedback</a>
    </nav>

    <div class="line"></div>

    <div class="feedback-container">
        <div class="feedback-textbox-wrapper">
            <!-- Feedback Form -->
            <form action="feedback.php" method="POST">
                <textarea id="feedback" name="feedback" class="feedback-textbox" placeholder="Write your feedback here..." maxlength="160"></textarea>
                <button type="submit" class="feedback-button">Write Review</button>
            </form>
            <!-- Trigger the notification if there's a message -->
            <?php if ($message != ''): ?>
                <script>
                    showNotification('<?php echo $message; ?>', <?php echo $message !== 'We Appreciate Your Thoughtfulness!!!' ? 'true' : 'false'; ?>);
                </script>
            <?php endif; ?>
        </div>

        <!-- Sort Options -->
        <div class="sort-container">
            <label for="sort-comments">Sort by:</label>
            <select id="sort-comments" onchange="sortComments(this.value)">
                <option value="newest">Newest</option>
                <option value="oldest">Oldest</option>
                <option value="username">Username</option>
            </select>
        </div>

        <!-- Feedback List -->
        <div class="feedback-list">
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="feedback-item" 
                     data-created-at="<?php echo htmlspecialchars($row['created_at']); ?>" 
                     data-username="<?php echo htmlspecialchars($row['username']); ?>">
                    <p><strong><?php echo htmlspecialchars($row['username']); ?>:</strong></p>
                    <p><?php echo htmlspecialchars($row['feedback']); ?></p>
                    <p><small><?php echo htmlspecialchars($row['created_at']); ?></small></p>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</body>
</html>
