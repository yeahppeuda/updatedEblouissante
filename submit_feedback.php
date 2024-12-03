<?php
session_start();

// Establish PDO connection to the database
$connect = new PDO('mysql:host=localhost;dbname=users_db', 'root', '');

// Initialize variables
$error = '';
$feedback_content = '';
$message = '';

// Check if the feedback content is empty
if(empty($_POST["feedback"]))
{
    $error .= '<p class="text-danger">Feedback is required</p>';
}
else
{
    // Sanitize and assign the feedback content
    $feedback_content = trim($_POST["feedback"]);
}

// If no errors, proceed with inserting the feedback into the database
if($error == '')
{
    try {
        // Prepare the SQL query for inserting feedback
        $query = "
            INSERT INTO feedback (user_id, feedback, created_at) 
            VALUES (:user_id, :feedback, NOW())
        ";

        // Prepare the statement
        $statement = $connect->prepare($query);

        // Execute the query with bound parameters
        $statement->execute(
            array(
                ':user_id' => $_SESSION['user_id'],  // Assuming user_id is stored in the session
                ':feedback' => $feedback_content
            )
        );

        // Success message
        $message = '<label class="text-success">Feedback Submitted Successfully</label>';
    } catch (PDOException $e) {
        // Error handling
        $error = '<label class="text-danger">Error: ' . $e->getMessage() . '</label>';
    }
}

// Prepare response data
$data = array(
    'error' => $error,
    'message' => $message
);

// Return JSON response
echo json_encode($data);
?>
