<?php
include 'db_connect.php';
session_start();

if ($_SESSION['role'] !== 'provider') {
    header('Location: login.php');
    exit();
}

$username = $_SESSION['username'];

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
        $loan_type = $_POST['loan_type'];
        $loan_code = $_POST['loan_code'];
        $qualification = $_POST['qualification'];
        $interest_rate = $_POST['interest_rate'];
        $duration = $_POST['duration'];

        $stmt = $conn->prepare("INSERT INTO loan_packages (username, loan_type, loan_code, qualification, interest_rate, duration) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('ssssss', $username, $loan_type, $loan_code, $qualification, $interest_rate, $duration);
        $stmt->execute();
        $message = "Loan package registered!";
    }
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['approve'])) {
        $applicant_username = $_POST['applicant_username'];
        $loan_type = $_POST['loan_type'];

       
        $update_status_query = $conn->prepare("UPDATE loan_applications SET status = 'Approved' WHERE username = ? AND loan_type = ?");
        $update_status_query->bind_param('ss', $applicant_username, $loan_type);
        $update_status_query->execute();
        $approval_message = "Loan application approved!";
    }

   
    $query = "
    SELECT la.username AS applicant_username, lp.loan_type, la.status, la.loan_code
    FROM loan_applications la
    JOIN loan_packages lp ON la.loan_type = lp.loan_type
    WHERE lp.username = ? AND la.status = 'Pending'
";
$stmt = $conn->prepare($query);
$stmt->bind_param('s', $username);
$stmt->execute();
$result = $stmt->get_result();
} catch (Exception $e) {
echo "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Financial Loan Institution</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="style1.css">
    <style>
        .register-container, .applications-container {    
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 40px 30px;
            text-align: center;
            width: 300px;
            margin: 20px auto;
            display: none; 
        }
        h2, p {
            text-align: center;
        }
        .applicant-table {
            margin: 20px auto;
            max-width: 800px;
        }
        .applicant-table table {
            width: 100%;
            border-collapse: collapse;
        }
        .applicant-table th, .applicant-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .applicant-table th {
            background-color: #f2f2f2;
        }
        .nav-container {
            text-align: center;
            margin: 20px;
        }
        .nav-container a {
            margin: 0 10px;
            text-decoration: none;
            color: #007bff;
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

    <div class="buttons-container">
        <div class="button">
            <a href="provider_dashboard.php">
                <img src="home.png" alt="Home">
                <p>Home</p>
            </a>
        </div>
        <div class="button">
            <a id="toggle-form-btn">
                <img src="register.png" alt="Register Loan Packages">
                <span>Register Loan Packages</span>
            </a>
        </div>
        <div class="button">
            <a id="toggle-applications-btn">
                <img src="applications.png" alt="View Loan Applications">
                <span>View Loan Applications</span>
            </a>
        </div>
        <div class="button">
            <img src="support.png" alt="Support">
            <p>24/7 Support</p>
        </div>
        <div class="button">
            <img src="logout.png" alt="Logout">
            <p><a href="aola.php">Logout</a></p>
        </div>
    </div>

    <div class="register-container">
        <h3>Register Loan Package</h3>
        <form method="POST" action="">
            <div class="form-group">
                <label for="loan_type">Loan Type:</label>
                <input type="text" id="loan_type" name="loan_type" placeholder="Loan Type" required>
            </div>
            <div class="form-group">
                <label for="loan_code">Loan Code:</label>
                <input type="text" id="loan_code" name="loan_code" placeholder="Loan Code" required>
            </div>
            <div class="form-group">
                <label for="qualification">Qualification:</label>
                <textarea id="qualification" name="qualification" required></textarea>
            </div>
            <div class="form-group">
                <label for="interest_rate">Interest Rate (%):</label>
                <input type="number" id="interest_rate" name="interest_rate" step="0.01" placeholder="Interest Rate (%)" required>
            </div>
            <div class="form-group">
                <label for="duration">Duration:</label>
                <input type="text" id="duration" name="duration" placeholder="Duration" required>
            </div>
            <button type="submit" name="register">Register Loan Package</button>
        </form>
    </div>

    <div class="applications-container">
        <h3>Loan Applications </h3>

        <?php if (isset($approval_message)) { echo "<p style='color: green;'>$approval_message</p>"; } ?>

        <table class="applicant-table">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Loan Type</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo '<tr>';
                        echo '<td>' . $row['applicant_username'] . '</td>';
                        echo '<td>' . $row['loan_type'] . '</td>';
                        echo '<td>' . $row['status'] . '</td>';
                        echo '<td>';
                        echo '<form method="POST" action="">';
                        echo '<input type="hidden" name="applicant_username" value="' . $row['applicant_username'] . '">';
                        echo '<input type="hidden" name="loan_type" value="' . $row['loan_type'] . '">';
                        echo '<button type="submit" name="approve">Approve</button>';
                        echo '</form>';
                        echo '</td>';
                        echo '</tr>';
                    }
                } else {
                    echo '<tr><td colspan="4">No pending loan applications.</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<script>
   
    document.getElementById('toggle-form-btn').addEventListener('click', function () {
        var formContainer = document.querySelector('.register-container');
        if (formContainer.style.display === 'none' || formContainer.style.display === '') {
            formContainer.style.display = 'block';
        } else {
            formContainer.style.display = 'none';
        }
    });

   
    document.getElementById('toggle-applications-btn').addEventListener('click', function () {
        var applicationsContainer = document.querySelector('.applications-container');
        if (applicationsContainer.style.display === 'none' || applicationsContainer.style.display === '') {
            applicationsContainer.style.display = 'block';
        } else {
            applicationsContainer.style.display = 'none';
        }
    });
</script>
</body>
</html>
