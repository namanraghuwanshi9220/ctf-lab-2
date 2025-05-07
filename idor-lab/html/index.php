<?php
// html/index.php
require_once 'auth_check.php'; // Route Protection!

ini_set('display_errors', 1); // For dev
error_reporting(E_ALL);    // For dev

// $loggedInUsername, $loggedInUserAlbumId, etc. are available from auth_check.php

$featured_albums = [/* ... same as before ... */]; // Keep your featured albums
$professor_note = "";
if ($loggedInUsername === 'pip_assistant') {
    $professor_note = "<p class='text-sm text-yellow-400 bg-yellow-800 bg-opacity-40 p-3 rounded-md my-4 border border-yellow-700'>\"Pip, my dear assistant! My groundbreaking research is in my private album (ID 1). I trust you won't peek before the official unveiling... unless you're testing the security, of course! Wink, wink.\" - Prof. A. Pengwin</p>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - PenguinstaGram</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body class="bg-gray-900 text-gray-200 min-h-screen flex flex-col items-center pt-6 sm:pt-10 font-sans">
    <div class="container mx-auto p-4 sm:p-6 md:p-8 bg-gray-800 rounded-xl shadow-2xl w-full max-w-5xl">
        <header class="text-center mb-6 sm:mb-8">
            <a href="index.php"><img src="assets/penguinstagram_logo.png" alt="PenguinstaGram Logo" class="mx-auto h-20 sm:h-24 w-auto mb-3 sm:mb-4 hover:opacity-80 transition-opacity"></a>
            <h1 class="text-3xl sm:text-4xl font-bold text-blue-400">Welcome, <?php echo htmlspecialchars($loggedInFullName); ?>!</h1>
            <p class="text-gray-400 text-sm sm:text-base">Your PenguinstaGram Dashboard</p>
        </header>

        <?php if (!empty($professor_note)) { echo $professor_note; } ?>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 my-6 sm:my-8">
            <div class="bg-gray-700 p-5 sm:p-6 rounded-lg shadow-lg hover:shadow-xl transition-shadow duration-300">
                <h2 class="text-xl sm:text-2xl font-semibold text-green-400 mb-3">My Album</h2>
                <p class="text-gray-300 text-sm mb-4">Access your personal collection of penguin photos.</p>
                <a href="view_album.php?album_id=<?php echo htmlspecialchars($loggedInUserAlbumId); ?>"
                   class="inline-block bg-green-600 hover:bg-green-700 text-white font-semibold py-2.5 px-5 rounded-lg shadow hover:shadow-md transition-all">
                    View My Album (ID: <?php echo htmlspecialchars($loggedInUserAlbumId); ?>)
                </a>
            </div>

            <div class="bg-gray-700 p-5 sm:p-6 rounded-lg shadow-lg hover:shadow-xl transition-shadow duration-300">
                <h2 class="text-xl sm:text-2xl font-semibold text-purple-400 mb-3">Penguin News</h2>
                <p class="text-gray-300 text-sm mb-1">"Exclusive: Professor Pengwin hints at a major breakthrough! His private digital album (ID 1) is rumored to contain the key. Security is tight, but curiosity is higher!"</p>
                <p class="text-xs text-gray-500 italic mt-2">- The Glacier Gazette</p>
            </div>
        </div>

        <!-- Featured Albums (same as before, ensure paths and IDs are correct) -->
        <div class="my-8">
            <h2 class="text-2xl sm:text-3xl font-semibold text-teal-400 mb-6 text-center">Featured Albums</h2>
            <!-- ... your featured album loop ... -->
             <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                 <!-- Prof Pengwin's Teaser -->
                <div class="bg-gray-700 rounded-lg shadow-lg overflow-hidden transform hover:scale-105 transition-transform duration-300 border-2 border-yellow-500 hover:border-yellow-400">
                    <img src="assets/prof_pengwin_teaser.jpg" alt="Professor Pengwin Teaser" class="w-full h-48 object-cover"> <!-- You'll need this image -->
                    <div class="p-5">
                        <h3 class="text-xl font-bold text-yellow-400">Professor A. Pengwin [CLASSIFIED]</h3>
                        <p class="text-gray-400 text-sm mb-3">Contains highly sensitive research. Access is... theoretically restricted.</p>
                        <a href="view_album.php?album_id=1" class="text-yellow-300 hover:text-yellow-200 font-semibold">Attempt to View Album (ID: 1) →</a>
                    </div>
                </div>
                 <!-- Add other featured albums here -->
            </div>
        </div>


        <nav class="mt-8 sm:mt-10 pt-6 border-t border-gray-700 text-center">
            <a href="logout.php" class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2.5 px-6 sm:py-3 sm:px-8 rounded-lg shadow hover:shadow-md transition-all duration-200 text-base sm:text-lg">
                Logout of PenguinstaGram
            </a>
        </nav>
    </div>
    <footer class="text-center text-gray-600 mt-8 sm:mt-10 pb-5 text-xs">
        © <?php echo date("Y"); ?> PenguinstaGram - Happy Snapping!
    </footer>
</body>
</html>
