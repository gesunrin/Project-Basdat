<?php
session_start();
include 'config/koneksi.php';

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = "SELECT * FROM user WHERE username='$username' AND password='$password'";
    $result = mysqli_query($koneksi, $query);

    if (mysqli_num_rows($result) == 1) {
        $_SESSION['user'] = mysqli_fetch_assoc($result);
        header("Location: dashboard(1).php");
    } else {
        $error = "Username atau Password salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Login</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body {
    background: #fff;
    font-family: 'Poppins', sans-serif;
}
.card {
    border: 1px solid #dc3545;
    box-shadow: 0 4px 10px rgba(220,53,69,0.3);
}
.btn-primary {
    background: #dc3545;
    border: none;
}
.btn-primary:hover {
    background: #b02a37;
}
</style>
</head>
<body>
<div class="container d-flex justify-content-center align-items-center" style="height:100vh;">
    <div class="card p-4" style="width:400px;">
        <h4 class="text-center mb-3 text-danger">Login</h4>
        <?php if(isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
        <form method="POST">
            <div class="mb-3">
                <label>Username</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" name="login" class="btn btn-primary w-100">Login</button>
        </form>
    </div>
</div>
</body>
</html>
