<?php
include 'db_connect.php';
session_start();
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register'])) {
    $username = $_POST['username'];
    $Region = $_POST['Region'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    $password = $_POST['password'];
    $retype_password = $_POST['retype_password'];

    if ($password !== $retype_password) {
        echo "Passwords do not match!";
        exit();
    }

    $hashed_password = hash('SHA256', $password);

    $sql = "INSERT INTO users (username, Region, phone, email, role, password) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $username, $Region, $phone, $email, $role, $hashed_password);

    if ($stmt->execute()) {
        echo "Registration successful!";
    } else {
        echo "Error: " . $stmt->error;
    }
}


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = $conn->prepare("SELECT id, password, role FROM users WHERE username = ?");
    $sql->bind_param("s", $username);
    $sql->execute();
    $result = $sql->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (hash("sha256", $password) == $row["password"]) {
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $row['role'];

            if ($row['role'] == 'provider') {
                header('Location: provider_dashboard.php');
            } else if ($row['role'] == 'applicant') {
                header('Location: applicant_dashboard.php');
            }
            exit();
        } else {
            $error = "Wrong password.";
        }
    } else {
        $error = "Invalid username or password.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AOLA</title>
    <style>
        body {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
}

.login-container, .register-container {
    background: #6763e1;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    padding: 40px 30px;
    text-align: center;
    width: 300px;
}

.login-container h2, .register-container h2 {
    margin-bottom: 20px;
}

.login-container input[type="text"],
.login-container input[type="password"],
.register-container input[type="text"],
.register-container input[type="password"],
.register-container input[type="number"],
.register-container input[type="email"]{
    width: 90%;
    padding: 10px;
    margin: 10px 0;
    border: 1px solid #ccc;
    border-radius: 5px;
}

.login-container button, .register-container button {
    width: 90%;
    padding: 10px;
    margin-top: 20px;
    background: #3f413f;
    border: none;
    border-radius: 5px;
    color: black;
    font-size: 16px;
    cursor: pointer;
}

.login-container button:hover, .register-container button:hover {
    background: #1af149;
}

.login-container .forgot-password,
.login-container .create-account {
    margin-top: 10px;
    display: block;
    color: black;
    text-decoration: none;
}

.login-container .forgot-password:hover,
.login-container .create-account:hover {
    text-decoration: underline;

}
.hidden {
    display: none;
}
h10{
    color: #6befd4;
   }



    </style>
    
</head>
<body>
    <div id="Login-form" class="login-container"> 
    <h10><b>ALL IN ONE LOAN APPLICATION SYSTEM</b>{AOLA} </h10> 
        <h2>Login</h2>
        <form method="post" action="">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="login">Login</button>
            <a href="ForgotPassword.php" class="forgot-password">Forgot Username Password?</a>
            <a href="#" class="create-account" onclick="toggleForm('Register')">Register</a>
        </form>
    </div>

    <div id="Register-form" class="register-container hidden">
          <h10><b>ALL IN ONE LOAN APPLICATION SYSTEM</b>{AOLA} </h10>
           <h3>Register</h3>
        <form method="post" action="">
            <div class="form-group">
                <input type="text" id="username" name="username" placeholder="Username" required>
            </div>
            <div class="form-group">
                <input type="text" id="Region" name="Region" placeholder="Region" required>
            </div>
            <div class="form-group">
                <input type="number" id="phone" name="phone" placeholder="Phone" required>
            </div>
            <div class="form-group">
                <input type="email" id="email" name="email" placeholder="E-mail" required>
            </div>
            <div class="form-group">
                <select id="role" name="role" required>
                    <option value="applicant">Loan Applicant</option>
                    <option value="provider">Financial Loan Institution</option>
                </select>
            </div>
            <div class="form-group">
                <input type="password" id="password" name="password" placeholder="Password" required>
            </div>
            <div class="form-group">
                <input type="password" id="retype_password" name="retype_password" placeholder="Re-Type Password" required>
            </div>
            <button type="submit" name="register">Register</button>
            <a href="#" onclick="toggleForm('Login')">Back to Login</a>
        </form>
    </div>

    <script>
        function toggleForm(formType) {
            document.getElementById('Login-form').classList.add('hidden');
            document.getElementById('Register-form').classList.add('hidden');

            if (formType === 'Register') {
                document.getElementById('Register-form').classList.remove('hidden');
            } else if (formType === 'Login') {
                document.getElementById('Login-form').classList.remove('hidden');
            }
        }
    </script>

</body>
</html>
