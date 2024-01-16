<?php
session_start();

// Подключаемся к базе данных
$servername = "localhost";
$username = "a0908526_lua_database";
$password = "19219219m";
$dbname = "a0908526_lua_database";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Проверяем, вошел ли пользователь
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    // Пользователь уже вошел, перенаправляем на главную страницу
    header("location: main.php");
    exit;
}

// Обработка отправленной формы для входа
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["login"])) {
    $username = $_POST["username"];
    $password = $_POST["password"];

    $sql = "SELECT id, username, password FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $param_username);
    $param_username = $username;
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 1) {
        $stmt->bind_result($id, $username, $hashed_password);
        if ($stmt->fetch()) {
            if (password_verify($password, $hashed_password)) {
                // Пароль верен, создаем сессию
                session_start();
                $_SESSION["loggedin"] = true;
                $_SESSION["username"] = $username;
                $_SESSION["id"] = $id;

                // Перенаправляем на главную страницу
                header("location: main.php");
            } else {
                // Пароль неверен
                $login_err = "Invalid username or password.";
            }
        }
    } else {
        // Пользователь не найден
        $login_err = "Invalid username or password.";
    }

    $stmt->close();
}

// Обработка отправленной формы для регистрации
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["register"])) {
    $username = $_POST["username"];
    $password = $_POST["password"];

    $sql = "INSERT INTO users (username, password) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $param_username, $param_password);
    $param_username = $username;
    $param_password = password_hash($password, PASSWORD_DEFAULT);

    if ($stmt->execute()) {
        // Регистрация успешна, создаем сессию
        session_start();
        $_SESSION["loggedin"] = true;
        $_SESSION["username"] = $username;
        $_SESSION["id"] = $stmt->insert_id;

        // Перенаправляем на главную страницу
        header("location: main.php");
    } else {
        $register_err = "Registration failed. Please try again later.";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Login</title>
</head>

<body>
    <h2>Login</h2>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <div>
            <label>Username</label>
            <input type="text" name="username" required>
        </div>
        <div>
            <label>Password</label>
            <input type="password" name="password" required>
        </div>
        <div>
            <input type="submit" name="login" value="Login">
        </div>
    </form>
    <p>Don't have an account? <a href="register.php">Register here</a>.</p>
</body>

</html>
