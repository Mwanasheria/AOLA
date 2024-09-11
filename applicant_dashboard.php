<?php
session_start();
include 'db_connect.php';

if ($_SESSION['role'] !== 'applicant') {
    header('Location: login.php');
    exit();
}
$username = $_SESSION['username'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['apply'])) {
    $loan_type = $_POST['loan_type'];
    $loan_code = $_POST['loan_code'];  
    $employment_status = $_POST['employment_status'];
    $additional_details = $_POST['additional_details'];
    $employer_name = isset($_POST['employer_name']) ? $_POST['employer_name'] : null;
    $monthly_income = isset($_POST['monthly_income']) ? $_POST['monthly_income'] : null;

    $stmt = $conn->prepare("INSERT INTO loan_applications (username, loan_type, loan_code, employment_status, additional_details, employer_name, monthly_income, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'Pending')");
    $stmt->bind_param('sssssss', $username, $loan_type, $loan_code, $employment_status, $additional_details, $employer_name, $monthly_income);
    $stmt->execute();

    $success_message = "Your application has been submitted!";
}


$result = $conn->query("SELECT * FROM loan_packages");

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loan Applicant Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="style1.css">
    <style>
        .form-container {
            display: none;
            max-width: 400px;
            margin: 50px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .form-container h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .form-container label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-container input[type="text"],
        .form-container select,
        .form-container textarea {
            width: calc(100% - 30px);
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .form-container textarea {
            resize: none;
        }
        .form-container button {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="top-container">
        <div class="username-display">Welcome, <?php echo $username; ?>!</div>
        <div class="settings-button">
            <a href="settings.php">&#9881;</a> 
        </div>
    </div>
    <div class="banner">
<h3>Loan Application Status</h3>

 <?php if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$loan_status_query = $conn->prepare("SELECT loan_type, status  FROM loan_applications WHERE username = ?");
if (!$loan_status_query) {
    die("Prepare failed: " . $conn->error);
}

$loan_status_query->bind_param('s', $username);
if (!$loan_status_query->execute()) {
    die("Execute failed: " . $loan_status_query->error);
}

$loan_status_result = $loan_status_query->get_result();
if (!$loan_status_result) {
    die("Get result failed: " . $conn->error);
}


if ($loan_status_result->num_rows > 0) {
    while ($application = $loan_status_result->fetch_assoc()) {
        if ($application['status'] == 'Approved') {
            echo "<p>Your loan application for <strong>{$application['loan_type']}</strong> has been approved. Please visit the loan officer to receive your loan.</p>";
        } elseif ($application['status'] == 'Rejected') {
            echo "<p>Your loan application for <strong>{$application['loan_type']}</strong> has been rejected.</p>";
        } else {
            echo "<p>Your loan application for <strong>{$application['loan_type']}</strong> is still pending.</p>";
        }
    }
} else {
    echo "<p>No active loan applications.</p>";
}?>
    </div>
    <div class="buttons-container">
        <div class="button">
            <a href="applicant_dashboard.php">
              <img src="home.png" alt="Home">
              <p>Home</p>
             </a>
        </div>
        <div class="button">
            <a href="calculator.php">
               <img src="calculator.webp" alt="Calculator">
               <p>Calculator</p>
            </a>
        </div>
        <div class="button">
            <img src="support.png" alt="24/7 Support">
            <p>24/7 Support</p>
        </div>
        <div class="button">
            <img src="logout.png" alt="Logout">
            <p> <a href="aola.php">Logout</a></p>
        </div>
    </div>

    <h3>Available Loan Packages</h3>
    <div class="loan-packages" id="loan-packages">
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo '<div class="loan-package">';
                echo '<h4>' . $row['loan_type'] . '</h4>';
                echo '<p>Qualification: ' . $row['qualification'] . '</p>';
                echo '<p>Interest Rate: ' . $row['interest_rate'] . '%</p>';
                echo '<p>Duration: ' . $row['duration'] . ' months</p>';
                echo '<button onclick="showApplyForm(' . $row['id'] . ', \'' . $row['loan_type'] . '\', \'' . $row['loan_code'] . '\')">Apply Now</button>';
                echo '</div>';
            }
        } else {
            echo '<p>No loan packages available at this time.</p>';
        }
        ?>
    </div>

   
    <div class="form-container" id="apply-form">
        <h2>Apply for Loan</h2>
        <form method="POST" action="">
            <input type="hidden" id="loan_type" name="loan_type">
            <input type="hidden" id="loan_code" name="loan_code">
            <label for="employment_status">Employment Status:</label>
            <select id="employment_status" name="employment_status" required>
                <option value="Unemployed">Unemployed</option>
                <option value="Employed">Employed</option>
            </select>

            <label for="additional_details">Additional Details:</label>
            <textarea id="additional_details" name="additional_details" rows="4"></textarea>

            <div id="employed_details" style="display: none;">
                <label for="employer_name">Employer Name:</label>
                <input type="text" id="employer_name" name="employer_name">

                <label for="monthly_income">Monthly Income:</label>
                <input type="text" id="monthly_income" name="monthly_income">
            </div>

            <button type="submit" name="apply">Submit Application</button>
        </form>
    </div>
</div>

<script>
    function showApplyForm(loanTypeId, loanTypeName, loanCode) {
        document.getElementById('loan_type').value = loanTypeName;
        document.getElementById('loan_code').value = loanCode;  
        document.querySelector('.form-container h2').textContent = 'Apply for ' + loanTypeName;
        document.getElementById('apply-form').style.display = 'block';
        window.scrollTo(0, document.getElementById('apply-form').offsetTop);
    }

    document.getElementById('employment_status').addEventListener('change', function () {
        var employedDetails = document.getElementById('employed_details');
        if (this.value === 'Employed') {
            employedDetails.style.display = 'block';
        } else {
            employedDetails.style.display = 'none';
        }
    });
</script>
</body>
</html>
