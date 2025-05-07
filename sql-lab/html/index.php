<?php
// broken-login-cdn/html/index.php
session_start();

// --- Flag location: Outside webroot for better security ---
$flagFilePath = '/app_secrets/flag.txt'; // Path outside /var/www/html
// --- End Flag location ---

$dbFile = __DIR__ . '/db/database.sqlite';
$message = '';
$messageType = ''; // 'success' or 'error'
$isLoggedInAsAdmin = false;

if (!file_exists($dbFile)) {
    include 'init_db.php';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (!empty($username) && !empty($password)) {
        try {
            $pdo = new PDO('sqlite:' . $dbFile);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $sql = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
            $stmt = $pdo->query($sql);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                $_SESSION['user'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                
                if ($user['role'] === 'admin') {
                    $isLoggedInAsAdmin = true;
                    $message = "Login successful! Welcome, " . htmlspecialchars($user['username']) . "!";
                    $messageType = 'success';
                    
                    if (file_exists($flagFilePath)) {
                        $flagContent = file_get_contents($flagFilePath);
                        $message .= "<br>Here's your reward: <strong>" . htmlspecialchars(trim($flagContent)) . "</strong>";
                    } else {
                        $message .= "<br>Error: Flag file not found!";
                    }
                } else {
                    $message = "Login successful! Welcome, " . htmlspecialchars($user['username']) . ". But you're not admin...";
                    $messageType = 'success';
                }
            } else {
                $message = "Invalid username or password.";
                $messageType = 'error';
            }
        } catch (PDOException $e) {
            $message = "Database error: " . $e->getMessage();
            $messageType = 'error';
        }
    } else {
        $message = "Please enter both username and password.";
        $messageType = 'error';
    }
}

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Brittle Biscuit</title>
    <!-- Include Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Optional: Configure Tailwind JIT if needed (usually not for simple cases) -->
    <!--
    <script>
      tailwind.config = {
        theme: {
          extend: {
            colors: {
              clifford: '#da373d',
            }
          }
        }
      }
    </script>
    -->
    <style type="text/tailwindcss">
        /* You can add custom base styles or components here if needed */
        /* For example:
        @layer utilities {
          .content-auto {
            content-visibility: auto;
          }
        }
        */
    </style>
</head>
<body class="bg-gray-900 text-gray-100 flex flex-col items-center justify-center min-h-screen p-4 font-sans">

    <div class="bg-gray-800 p-8 rounded-lg shadow-xl w-full max-w-md">
        <h1 class="text-3xl font-bold text-center text-blue-400 mb-6">Brittle Biscuit Login</h1>
        
        <?php if (isset($_SESSION['user'])): ?>
            <div class="text-center">
                <p class="text-green-400 text-lg mb-4">Logged in as: <?php echo htmlspecialchars($_SESSION['user']); ?> (<?php echo htmlspecialchars($_SESSION['role']); ?>)</p>
                <?php if ($isLoggedInAsAdmin && $messageType === 'success'): ?>
                     <div class="mt-4 p-4 bg-green-700 border border-green-500 rounded text-white break-all">
                        <?php echo $message; // Already contains flag if admin ?>
                    </div>
                <?php elseif ($message && $messageType === 'success'): ?>
                    <div class="mt-4 p-4 bg-blue-700 border border-blue-500 rounded text-white">
                        <?php echo $message; ?>
                    </div>
                <?php endif; ?>
                <a href="index.php?logout=true" class="mt-6 inline-block bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded transition duration-300">
                    Logout
                </a>
            </div>
        <?php else: ?>
            <?php if ($message): ?>
                <div class="mb-4 p-3 rounded <?php echo $messageType === 'success' ? 'bg-green-700 border-green-500' : 'bg-red-700 border-red-500'; ?> text-white break-all">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <form action="index.php" method="POST">
                <div class="mb-4">
                    <label for="username" class="block text-sm font-medium text-gray-300 mb-1">Username</label>
                    <input type="text" name="username" id="username" required
                           class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="e.g., admin">
                </div>
                <div class="mb-6">
                    <label for="password" class="block text-sm font-medium text-gray-300 mb-1">Password</label>
                    <input type="password" name="password" id="password" required
                           class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="************">
                </div>
                <button type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition duration-300">
                    Login
                </button>
            </form>
            <p class="text-xs text-gray-500 mt-4 text-center">Hint: Try to login as 'admin'. Maybe the SQL is a bit... broken?</p>
        <?php endif; ?>
    </div>

    <footer class="text-center text-gray-500 mt-8 text-sm">
        Challenge: Broken Login
    </footer>

</body>
</html>
