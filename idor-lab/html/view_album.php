<?php
// html/view_album.php
require_once 'auth_check.php'; // Route Protection!

ini_set('display_errors', 1);
error_reporting(E_ALL);

define('THE_ULTIMATE_PENGUIN_SECRET', 'flag{P0w3r3d_P3ngu1n_S3cr3ts!}');
$requested_album_id_str = $_GET['album_id'] ?? null;
$reveal_action = isset($_GET['reveal_secret']) && $_GET['reveal_secret'] === 'true';

$page_title = "Penguin Album";
$album_content_html = "";
$access_message = "";
$flag_display_html = "";

// --- SVG Definitions (can be put in a separate included file or functions for tidiness) ---
function get_file_icon_svg($classes = "inline-block w-5 h-5 sm:w-6 sm:h-6 mr-2 flex-shrink-0") {
    return '<svg xmlns="http://www.w3.org/2000/svg" class="'.$classes.'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>';
}

function get_secret_scroll_icon_svg($classes = "inline-block w-5 h-5 sm:w-6 sm:h-6 mr-2 -mt-1") {
    return '<svg xmlns="http://www.w3.org/2000/svg" class="'.$classes.'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"></path><polyline points="14 2 14 8 20 8"></polyline><circle cx="12" cy="15" r="1"></circle><path d="M12 12v1"></path></svg>'; // Simplified scroll/lock
}

function get_treasure_icon_svg($classes = "w-8 h-8 sm:w-10 sm:h-10 mr-3 animate-pulse") {
    return '<svg xmlns="http://www.w3.org/2000/svg" class="'.$classes.'" viewBox="0 0 24 24" fill="rgb(250 204 21)" stroke="rgb(249 115 22)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>';
}
// --- End SVG Definitions ---


if ($requested_album_id_str === null) { /* ... error handling ... */ }
elseif (!ctype_digit($requested_album_id_str) || (int)$requested_album_id_str <= 0) { /* ... error handling ... */ }
else {
    $requested_album_id = (int)$requested_album_id_str;
    $page_title = "Album " . htmlspecialchars($requested_album_id);
    $album_path_on_server = __DIR__ . "/user_albums/" . $requested_album_id;

    if ($requested_album_id === 1 && $reveal_action) {
        $flag_display_html = "
            <div id='flag-reveal-box' class='my-6 p-5 sm:p-6 bg-green-800 bg-opacity-90 border-2 border-green-600 rounded-xl text-center shadow-2xl backdrop-blur-sm'>
                <h3 class='text-2xl sm:text-3xl font-bold text-yellow-300 mb-3 flex items-center justify-center'>"
                . get_treasure_icon_svg() .
                "PROFESSOR'S SECRET UNLOCKED!</h3>
                <p class='text-green-200 mb-3 text-sm sm:text-base'>The flag is:</p>
                <pre class='bg-gray-900 text-yellow-300 font-mono text-base sm:text-lg p-3 rounded-md inline-block break-all select-all cursor-text whitespace-pre-wrap'>" . htmlspecialchars(THE_ULTIMATE_PENGUIN_SECRET) . "</pre>
                <p class='text-xs text-green-300 mt-4'>(Submit this entire string as the flag)</p>
            </div>";
    }

    if (is_dir($album_path_on_server)) {
        $files = array_diff(scandir($album_path_on_server), array('.', '..'));

        if (empty($files) && $requested_album_id === 1 && empty($flag_display_html)) {
             $album_content_html .= "<p class='text-yellow-300 italic text-center py-3 bg-yellow-800 bg-opacity-40 rounded-md border border-yellow-700'>Professor Pengwin's special album (ID: 1) seems to be just for his top-secret notes... Is there a way to reveal them?</p>";
        } elseif (empty($files) && $requested_album_id !== 1) { /* ... empty album message ... */ }

        if (!empty($files)) {
            $album_content_html .= "<ul class='list-none pl-0 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 mt-4'>";
            foreach ($files as $file) {
                $file_web_path = "user_albums/" . $requested_album_id . "/" . $file;
                $album_content_html .= "<li class='bg-gray-700 p-4 rounded-lg shadow-md hover:shadow-xl transition-shadow duration-300 flex flex-col justify-between border border-gray-600'>";
                $album_content_html .= "<div class='flex-grow'>";
                $album_content_html .= "<a href='" . htmlspecialchars($file_web_path) . "' target='_blank' class='text-blue-300 hover:text-blue-200 block truncate font-medium mb-2 flex items-center'>"
                . get_file_icon_svg() . // Using SVG function
                htmlspecialchars($file) . "</a></div>";
                $album_content_html .= "<a href='" . htmlspecialchars($file_web_path) . "' download='" . htmlspecialchars($file) ."' target='_blank' class='mt-3 text-xs bg-sky-600 hover:bg-sky-500 text-white py-1.5 px-3 rounded-md inline-block text-center w-full shadow-sm'>Download / View File</a>";
                $album_content_html .= "</li>";
            }
            $album_content_html .= "</ul>";
        }

        if ($requested_album_id === 1) {
            $access_message = "<p class='text-green-300'>You are currently viewing Professor Pengwin's highly classified research album (ID: 1)!</p>";
            if (empty($flag_display_html)) {
                $access_message .= "
                    <div class='mt-4 text-center'>
                        <a href='view_album.php?album_id=" . $requested_album_id . "&reveal_secret=true'
                           class='bg-yellow-500 hover:bg-yellow-600 text-black font-bold py-2.5 px-5 sm:py-3 sm:px-6 rounded-lg shadow-lg hover:shadow-xl transition-all duration-200 text-base sm:text-lg transform hover:scale-105 inline-block'>"
                           . get_secret_scroll_icon_svg() . // Using SVG function
                           " Reveal Professor's Hidden Secret!</a></div>";
            }
        } elseif ($requested_album_id === $loggedInUserAlbumId) { /* ... own album message ... */ }
        else { /* ... other album message ... */ }

    } else { /* ... album does not exist message ... */ }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?> - PenguinstaGram</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="assets/style.css">
    <style>
        @keyframes pulse { 0%, 100% { opacity: 1; transform: scale(1); } 50% { opacity: 0.7; transform: scale(1.1); } }
        .animate-pulse { animation: pulse 1.5s infinite; }
        .select-all { user-select: all; -webkit-user-select: all; -moz-user-select: all; -ms-user-select: all; }
        .cursor-text { cursor: text; }
    </style>
</head>
<body class="bg-gray-900 text-gray-200 min-h-screen flex flex-col items-center pt-6 sm:pt-10 font-sans">
    <div class="container mx-auto p-4 sm:p-6 md:p-8 bg-gray-800 rounded-xl shadow-2xl w-full max-w-5xl">
        <header class="text-center mb-6 sm:mb-8">
             <a href="index.php" title="Back to Dashboard">
                 <!-- Text Logo -->
                <div class="text-4xl sm:text-5xl font-bold text-blue-400 mb-2 hover:opacity-80 transition-opacity">
                     <span class="text-sky-400">🐧</span> PenguinstaGram
                </div>
                <!-- End Text Logo -->
            </a>
            <h1 class="text-3xl sm:text-4xl font-bold text-blue-400"><?php echo htmlspecialchars($page_title); ?></h1>
            <p class="text-gray-400 text-sm sm:text-base">Logged in as: <strong class="text-teal-300"><?php echo htmlspecialchars($loggedInFullName); ?></strong> (Your Album ID: <?php echo htmlspecialchars($loggedInUserAlbumId); ?>)</p>
        </header>

        <?php if (!empty($flag_display_html)) { echo $flag_display_html; } ?>
        <?php if (!empty($access_message)): ?>
            <div class="my-4 p-3 <?php echo empty($flag_display_html) && $requested_album_id === 1 ? 'bg-yellow-800 bg-opacity-30 border border-yellow-700' : 'bg-gray-700 border border-gray-600'; ?> rounded-md text-center text-sm">
                <?php echo $access_message; ?>
            </div>
        <?php endif; ?>

        <?php
        $showAlbumGrid = true;
        if ((!empty($flag_display_html) && $requested_album_id === 1) || (strpos($album_content_html, 'Error:') !== false) || (strpos($album_content_html, 'No album ID') !== false) ) {
            $showAlbumGrid = false;
        }
        ?>
        <?php if($showAlbumGrid): ?>
        <main class="album-grid my-6 sm:my-8">
            <?php echo $album_content_html; ?>
        </main>
        <?php elseif (empty($flag_display_html) && (strpos($album_content_html, 'Error:') !== false || strpos($album_content_html, 'No album ID') !== false)):
            echo "<main class='my-6 sm:my-8'>{$album_content_html}</main>";
        ?>
        <?php endif; ?>

        <nav class="mt-6 sm:mt-10 pt-6 border-t border-gray-700 text-center">
             <!-- ... nav links ... -->
        </nav>
    </div>
    <footer class="text-center text-gray-600 mt-8 sm:mt-10 pb-5 text-xs">
        © <?php echo date("Y"); ?> PenguinstaGram - Snap. Share. Squawk!
    </footer>
</body>
</html>
