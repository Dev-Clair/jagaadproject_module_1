<?php
// Import file containing functions for validating userinput
require_once "validate_userinput.php";

$filePath = __DIR__ . DIRECTORY_SEPARATOR . 'database.json';

// Read the database file
$data = file_get_contents($filePath);
$members = json_decode($data, true);

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve the form data and clean the values using clean_name/class/grade functions
    $regNo = $_POST['regNo'];
    $name = clean_name($_POST['name']);
    $class = clean_class($_POST['class']);
    $grade = clean_grade($_POST['grade']);

    // Revalidate userinput using the validate_name/class/grade functions
    if (!validate_name($name) || !validate_class($class) || !validate_grade($grade)) {
        // Display error message and redirect to index.php after 5 seconds
        header('Refresh: 5; URL=update.php');
        echo "Oops! Something went wrong and we couldn't update the record.\nMake sure you follow the help-info and grade must be between 0-10 when you try again";
        exit;
    }

    // Search for the member with the matching registration number
    foreach ($members as $key => $member) {
        if ($member['regNo'] == $regNo) {
            // Update the member record
            $members[$key]['name'] = $name;
            $members[$key]['class'] = $class;
            $members[$key]['grade'] = $grade;
            break;
        }
    }

    // Save the updated records back to the file
    $updatedRecord = json_encode($members, JSON_PRETTY_PRINT);
    file_put_contents($filePath, $updatedRecord);

    // Redirect back to index.php
    $address = 'index.php?success=1';
    redirect_to($address);
    exit;
}

// Retrieve the registration number from the query string
if (isset($_GET['regNo'])) {
    $regNo = $_GET['regNo'];

    // Search for the member with the matching registration number
    foreach ($members as $member) {
        if ($member['regNo'] == $regNo) {
            $name = $member['name'];
            $class = $member['class'];
            $grade = $member['grade'];
            break;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <title>Update Student</title>
</head>

<body class="my-5">
    <?php
    if (isset($_GET['success'])) {
        if ($_GET['success'] == 1)
            // echo success message to user:
            echo "Success!";
    }
    ?>
    <div class="container">
        <h2><strong>Update Student Record</strong></h2>
        <form method="post" action="update.php">
            <!-- Hidden Field: contains student's registration number -->
            <input type="hidden" name="regNo" value="<?php echo $regNo; ?>">
            <!-- Name Field -->
            <div>
                <label for="name" class="form-label"><strong>Name:</strong></label>
                <input type="text" class="form-control" id="name" name="name" value="<?php echo $name; ?>" autocomplete="off">
            </div>
            <div class="mb-3">
                <p><em>First and Last Names Only</em> </p>
            </div>
            <!-- Class Field -->
            <div>
                <label for="class" class="form-label"><strong>Class:</strong></label>
                <input type="text" class="form-control" id="class" name="class" value="<?php echo $class; ?>" autocomplete="off">
            </div>
            <div class="mb-3">
                <p><em>Stem | Commerce | Art</em> </p>
            </div>
            <!-- Grade Field -->
            <div class="mb-3">
                <label for="grade" class="form-label">
                    <strong>Grade:</strong>
                </label>
                <input type="text" class="form-control" id="grade" name="grade" value="<?php echo $grade; ?>" autocomplete="off">
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>