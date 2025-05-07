<?php
// html/login.php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();

// If already logged in, redirect to index.php
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

// --- User database (in a real app, this would be a proper database) ---
$users = [
    'pip_assistant' => [ // The user the player logs in as
        'password' => 'rockhopper123', // Password given in the hint
        'id' => 2,                     // Internal user ID
        'album_id' => 2,               // Their own album ID
        'full_name' => 'Pip Assistant'
    ],
    'archie_p' => [    // Professor Pengwin (the target)
        'password' => 'GoldenPenguinsRule!', // A strong password, player doesn't need this
        'id' => 1,
        'album_id' => 1, // The album ID the player needs to access via IDOR
        'full_name' => 'Prof. Archibald Pengwin'
    ]
];
// --- End User Database ---

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error_message = "Please enter both username and password.";
    } elseif (isset($users[$username]) && $users[$username]['password'] === $password) {
        // Login successful
        $_SESSION['user_id'] = $users[$username]['id'];
        $_SESSION['username'] = $username;
        $_SESSION['full_name'] = $users[$username]['full_name'];
        $_SESSION['album_id'] = $users[$username]['album_id'];
        header('Location: index.php'); // Redirect to the main dashboard
        exit;
    } else {
        $error_message = "Invalid username or password. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - PenguinstaGram</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="assets/style.css"> <!-- For any custom styles -->
</head>
<body class="bg-gray-900 text-gray-200 min-h-screen flex flex-col items-center justify-center p-4 sm:p-6 font-sans">
    <div class="bg-gray-800 p-6 sm:p-8 rounded-xl shadow-2xl w-full max-w-md">
        <header class="text-center mb-6 sm:mb-8">
            <img src="assets/penguinstagram_logo.png" alt="PenguinstaGram Logo" class="mx-auto h-20 sm:h-24 w-auto mb-3 sm:mb-4">
            <h1 class="text-2xl sm:text-3xl font-bold text-blue-400">Welcome to PenguinstaGram!</h1>
            <p class="text-gray-400 mt-1 text-sm sm:text-base">Log in to share your penguin snaps.</p>
        </header>

        <?php if (!empty($error_message)): ?>
            <div class="mb-4 p-3 rounded-md bg-red-800 bg-opacity-70 text-red-300 border border-red-700 text-center text-sm">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <div class="mb-5">
                <label for="username" class="block text-sm font-semibold text-gray-300 mb-1.5">Username</label>
                <input type="text" name="username" id="username" required
                       class="w-full px-4 py-2.5 bg-gray-700 border border-gray-600 rounded-lg text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent placeholder-gray-500"
                       placeholder="e.g., pip_assistant">
            </div>
            <div class="mb-7">
                <label for="password" class="block text-sm font-semibold text-gray-300 mb-1.5">Password</label>
                <input type="password" name="password" id="password" required
                       class="w-full px-4 py-2.5 bg-gray-700 border border-gray-600 rounded-lg text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent placeholder-gray-500"
                       placeholder="************">
            </div>
            <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg transition duration-300 ease-in-out transform hover:scale-105 text-lg shadow-md">
                Waddle In!
            </button>
        </form>
        <p class="text-xs text-gray-500 mt-6 text-center">
            (Hint: The CTF challenge description has login details for `pip_assistant`!)
        </p>
    </div>
     <footer class="text-center text-gray-600 mt-8 sm:mt-10 text-xs">
        © <?php echo date("Y"); ?> PenguinstaGram Login Portal
    </footer>
</body>
</html>
