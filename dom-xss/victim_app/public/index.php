<?php
// --- Configuration ---
$flag_file = 'flag.txt';
$flag_cookie_name = 'DOMinionSecret';

// --- Security Headers & Flag Setup ---
// MODIFIED CSP to allow Tailwind and 'unsafe-inline' for its config and our inline scripts
header("Content-Security-Policy: script-src 'self' https://cdn.tailwindcss.com 'unsafe-inline'; object-src 'none';");

if (file_exists($flag_file)) {
    $flag_value = trim(file_get_contents($flag_file));
    setcookie($flag_cookie_name, $flag_value, 0, "/", "", false, false);
} else {
    setcookie($flag_cookie_name, "FLAG_NOT_FOUND_ON_SERVER", 0, "/", "", false, false);
}

$theme_base_url_html = '';
if (isset($_GET['theme_base_url'])) {
    $theme_base_url_html = $_GET['theme_base_url']; // UNSAFE: Directly reflecting
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- TAILWIND CDN SCRIPT - MUST BE ALLOWED BY CSP -->
    <script src="https://cdn.tailwindcss.com"></script>
    <title>DOMinion Control Panel - Welcome!</title>

    <?php
    // VULNERABLE INJECTION POINT FOR <base> TAG
    echo $theme_base_url_html;
    ?>

    <!-- This script's source will be affected by the <base> tag -->
    <script src="/js/app.js"></script>

    <!-- TAILWIND CONFIG - REQUIRES 'unsafe-inline' in script-src if CSP is active -->
    <script>
      tailwind.config = {
        theme: {
          extend: {
            colors: {
              'dominion-blue': '#2563eb',
              'dominion-light-blue': '#60a5fa',
              'dominion-dark': '#1e293b',
              'dominion-gray': '#64748b',
              'dominion-light-gray': '#f1f5f9',
            }
          }
        }
      }
    </script>
    <style>
        /* You can add minor custom styles here if needed, complementing Tailwind */
        .hash-content { min-height: 100px; }
        body {
            /* Basic fallback if tailwind fails catastrophically, not strictly needed for the fix */
            font-family: sans-serif; 
        }
    </style>
</head>
<body class="bg-dominion-light-gray text-dominion-dark flex flex-col min-h-screen">

    <!-- Header -->
    <header class="bg-dominion-dark text-white p-6 shadow-lg">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-3xl font-bold tracking-tight">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8 inline-block mr-2 text-dominion-light-blue">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                </svg>
                DOMinion Control
            </h1>
            <nav>
                <a href="/index.php" class="px-3 py-2 rounded-md text-sm font-medium hover:bg-dominion-blue">Dashboard</a>
                <a href="#SystemStatus" class="px-3 py-2 rounded-md text-sm font-medium hover:bg-dominion-blue">System Status</a>
                <a href="#UserGuide" class="px-3 py-2 rounded-md text-sm font-medium hover:bg-dominion-blue">User Guide</a>
            </nav>
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="container mx-auto p-6 flex-grow">
        <div class="bg-white p-8 rounded-xl shadow-2xl">
            <div class="grid md:grid-cols-3 gap-6">
                <!-- Left Column / Main Dynamic Area -->
                <div class="md:col-span-2">
                    <h2 class="text-2xl font-semibold text-dominion-blue mb-1">Welcome, Administrator!</h2>
                    <p class="text-dominion-gray mb-4">This is your central hub for managing DOMinion services. Current active module content is shown below.</p>
                    
                    <div id="dynamic-content-area" class="hash-content bg-gray-50 p-6 rounded-lg border border-dashed border-dominion-light-blue">
                        <p class="text-gray-500">Select a module from the navigation or directly append a hash (e.g., `#<b>Hello!</b>`) to load content.</p>
                    </div>
                </div>

                <!-- Right Column / Info & Hints -->
                <aside class="md:col-span-1 bg-blue-50 p-6 rounded-lg border border-dominion-light-blue">
                    <h3 class="text-xl font-semibold text-dominion-blue mb-3">System Configuration</h3>
                    <div class="mb-4">
                        <label for="theme_url_display" class="block text-sm font-medium text-dominion-gray">Theme Base URL (Experimental):</label>
                        <input type="text" id="theme_url_display" name="theme_url_display" 
                               class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-dominion-blue focus:border-dominion-blue sm:text-sm"
                               placeholder="e.g., /themes/custom/" disabled>
                        <p class="mt-1 text-xs text-dominion-gray">
                            Hint: This setting is currently controlled via the <code>?theme_base_url=</code> URL parameter.
                            Be careful what you inject here! The page's scripts are loaded relative to this base.
                        </p>
                    </div>
                    
                    <h3 class="text-xl font-semibold text-dominion-blue mb-3 mt-6">Security Advisory</h3>
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-md">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.636-1.026 2.092-1.026 2.728 0l5.076 8.198c.59.951-.104 2.203-1.228 2.203H4.159c-1.124 0-1.818-1.252-1.228-2.203L8.257 3.099zM9 10a1 1 0 1 1 2 0v2a1 1 0 1 1-2 0v-2zm1 5a1 1 0 1 0 0-2 1 1 0 0 0 0 2z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-yellow-700">
                                    Our current Content Security Policy is: <br>
                                    <code class="text-xs bg-yellow-100 p-1 rounded">script-src 'self' https://cdn.tailwindcss.com 'unsafe-inline'; object-src 'none';</code>
                                    <br>Ensure all custom integrations comply.
                                </p>
                            </div>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-dominion-dark text-dominion-light-gray text-center p-6 mt-auto">
        © <?php echo date("Y"); ?> DOMinion Web Services. All Rights Reserved.
        <p class="text-xs">"We DOMinate the Web, Securely!" (We hope)</p>
    </footer>

    <!-- MAIN PAGE SCRIPT - REQUIRES 'unsafe-inline' in script-src if CSP is active -->
    <script>
        function displayContentFromHash() {
            const contentArea = document.getElementById('dynamic-content-area');
            if (window.location.hash) {
                const hashData = decodeURIComponent(window.location.hash.substring(1));
                contentArea.innerHTML = `<div class="animate-pulse p-4 bg-green-50 border border-green-200 rounded-md">${hashData}</div>`;
                console.log("DOM XSS Sink: Updated content from hash: ", hashData);
            } else {
                contentArea.innerHTML = '<p class="text-gray-500 p-4">No module selected. Use navigation or add #your_content to the URL.</p>';
            }
        }

        window.addEventListener('hashchange', displayContentFromHash);
        
        document.addEventListener('DOMContentLoaded', () => {
            displayContentFromHash();
            
            const urlParams = new URLSearchParams(window.location.search);
            const themeBase = urlParams.get('theme_base_url');
            if (themeBase && document.getElementById('theme_url_display')) { // check if element exists
                document.getElementById('theme_url_display').value = themeBase;
            }
        });
    </script>
</body>
</html>
