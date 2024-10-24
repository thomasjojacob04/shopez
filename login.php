<?php
session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: main.php");
    exit(); // Important to prevent further code execution
}

if (isset($_SESSION['loggedin'])) {
    header('Location: admin.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // For simplicity, using plain username and password. In production, use hashed passwords.
    if ($username == 'admin' && $password == 'password') {
        $_SESSION['loggedin'] = true;
        header('Location: admin.php');
    } else {
        $error = "Invalid credentials.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        function validateForm() {
            var username = document.forms["loginForm"]["username"].value;
            var password = document.forms["loginForm"]["password"].value;
            if (username == "" || password == "") {
                alert("Username and Password must be filled out");
                return false;
            }
            return true;
        }

        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('passwordEyeIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                `;
            } else {
                passwordInput.type = 'password';
                eyeIcon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                `;
            }
        }
    </script>

    <style>
        .button-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 10vh;
        }
    </style>
</head>

<body>
    <main class="bg-gray-900 flex items-center justify-center min-h-screen">
        <div class="bg-gray-950 p-8 mt-6 rounded-lg shadow-lg max-w-sm w-full">
            <h2 class="text-2xl font-bold text-white text-center mt-2 mb-6">Admin Login</h2>
            <form name="loginForm" class="max-w-sm mx-auto" method="POST" action="" onsubmit="return validateForm()">
                <div class="mt-2 mb-5">
                    <label for="username" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Username:</label>
                    <input type="text" id="username" name="username" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Username" required>
                </div>
                <div class="mb-5 relative">
                    <label for="password" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Password:</label>
                    <div class="relative">
                        <input type="password" id="password" name="password" 
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" 
                            placeholder="Password" required>
                        <button type="button" 
                            class="absolute right-2.5 bottom-2.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
                            onclick="togglePasswordVisibility()">
                            <svg id="passwordEyeIcon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                <div>
                    <?php if (isset($error)) : ?>
                        <p style="color: red;"><?php echo $error; ?></p>
                    <?php endif; ?>
                </div>
                <div class="button-container">
                    <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Login</button>
                </div>
            </form>
        </div>
    </main>
</body>

</html>