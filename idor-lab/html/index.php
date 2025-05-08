<?php
// html/index.php
require_once 'auth_check.php'; // Ensures user is logged in
                               // Provides $loggedInUsername, $loggedInUserAlbumId, etc.

// Fictional list of "featured" or "other" albums
// Professor Pengwin's album is ID 1
$all_albums_on_dashboard = [
    // ... other albums ...
    [
        'id' => 1,
        'owner' => 'Professor A. Pengwin',
        'title' => 'Top Secret Research [PRIVATE]',
        'description' => 'Contains highly sensitive research photos. Access restricted... or is it?',
        'image' => 'assets/prof_pengwin_teaser.jpg',
        'is_private' => true // Add a flag to indicate this is special
    ],
    [
        'id' => $loggedInUserAlbumId, // The user's own album
        'owner' => $loggedInFullName, // Or $loggedInUsername
        'title' => 'My Personal Album',
        'description' => 'My collection of amazing penguin photos!',
        'image' => 'assets/my_album_cover.jpg', // Placeholder
        'is_private' => false
    ],
    [
        'id' => 3, // Example other public album
        'owner' => 'PercyThePuffin',
        'title' => 'Puffin Adventures',
        'description' => 'Stunning Arctic Views & Fishy Feasts!',
        'image' => 'assets/puffin_album_cover.jpg',
        'is_private' => false
    ],
    // ... more albums ...
];

// A little note from Professor Pengwin (as before)
$professor_note_for_pip = "";
if ($loggedInUsername === 'pip_assistant') {
    $professor_note_for_pip = "<p class='text-sm text-yellow-300 bg-yellow-800 bg-opacity-50 p-3 rounded-md my-4'>\"Pip, my dear assistant! Remember, my groundbreaking research is in my album (ID 1). Don't go peeking before it's public, but do make sure everything's in order for the grand reveal!\" - Prof. A. Pengwin</p>";
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
    <style> /* ... your pulse animation and other styles ... */ </style>
</head>
<body class="bg-gray-900 text-gray-200 min-h-screen flex flex-col items-center pt-6 sm:pt-10 font-sans">
    <div class="container mx-auto p-4 sm:p-6 md:p-8 bg-gray-800 rounded-xl shadow-2xl w-full max-w-5xl">
        <header class="text-center mb-6 sm:mb-8">
            <a href="index.php" title="Back to Dashboard">
                <div class="text-4xl sm:text-5xl font-bold text-blue-400 mb-2 hover:opacity-80 transition-opacity">
                    <span class="text-sky-400">🐧</span> PenguinstaGram
                </div>
            </a>
            <h1 class="text-3xl sm:text-4xl font-bold text-blue-400">Welcome, <?php echo htmlspecialchars($loggedInFullName); ?>!</h1>
            <p class="text-gray-400 text-sm sm:text-base">Your personal hub for penguin photography.</p>
        </header>

        <?php if (!empty($professor_note_for_pip)) { echo $professor_note_for_pip; } ?>

        <div class="my-8">
            <h2 class="text-3xl font-semibold text-teal-400 mb-6 text-center">Browse Albums</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($all_albums_on_dashboard as $album): ?>
                    <?php
                        // Determine if the current user should have a direct link to this album
                        // Professor Pengwin can always see his own album directly.
                        // Other users cannot directly click on Prof. Pengwin's private album (ID 1).
                        $can_directly_view_album = true;
                        if ($album['id'] === 1 && $loggedInUsername !== 'archie_p') { // Assuming 'archie_p' is Prof. Pengwin's username
                            $can_directly_view_album = false;
                        }
                    ?>
                    <div class="bg-gray-700 rounded-lg shadow-lg overflow-hidden transform hover:scale-105 transition-transform duration-300 <?php echo ($album['id'] === 1 && !$can_directly_view_album) ? 'border-2 border-yellow-600 opacity-80' : 'border border-gray-600'; ?>">
                        <img src="<?php echo htmlspecialchars($album['image']); ?>" alt="<?php echo htmlspecialchars($album['title']); ?>" class="w-full h-48 object-cover <?php echo ($album['id'] === 1 && !$can_directly_view_album) ? 'filter grayscale contrast-50' : ''; ?>">
                        <div class="p-5">
                            <h3 class="text-xl font-bold <?php echo ($album['id'] === 1 && !$can_directly_view_album) ? 'text-yellow-400' : 'text-teal-300'; ?>">
                                <?php echo htmlspecialchars($album['title']); ?>
                            </h3>
                            <p class="text-sm text-gray-400 mb-1">Owner: <?php echo htmlspecialchars($album['owner']); ?></p>
                            <p class="text-gray-400 text-xs mb-3 h-12 overflow-hidden"><?php echo htmlspecialchars($album['description']); ?></p>

                            <?php if ($can_directly_view_album): ?>
                                <a href="view_album.php?album_id=<?php echo htmlspecialchars($album['id']); ?>"
                                   class="text-blue-400 hover:text-blue-300 font-semibold inline-block mt-2 py-1 px-3 bg-gray-600 hover:bg-gray-500 rounded-md text-sm">
                                    View Album (ID: <?php echo htmlspecialchars($album['id']); ?>) →
                                </a>
                            <?php else: // Case for Prof. Pengwin's private album when viewed by others ?>
                                <div class="mt-2 py-1 px-3 bg-yellow-700 text-yellow-200 rounded-md text-sm text-center">
                                    <span class="font-semibold">ID: <?php echo htmlspecialchars($album['id']); ?> (Private Research)</span>
                                    <p class="text-xs italic text-yellow-300">Requires special access...</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <nav class="mt-6 sm:mt-10 pt-6 border-t border-gray-700 text-center">
            <a href="logout.php" class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2.5 px-6 rounded-lg shadow hover:shadow-md transition-all duration-200 text-base">
                Logout of PenguinstaGram
            </a>
        </nav>
    </div>
    <footer class="text-center text-gray-600 mt-8 sm:mt-10 pb-5 text-xs">
        © <?php echo date("Y"); ?> PenguinstaGram - Snap. Share. Squawk!
    </footer>
</body>
</html>
