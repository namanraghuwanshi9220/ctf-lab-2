<?php
// ssrf-internal-importer/html/index.php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$page_content = null;
$error_message = null;
$submitted_url = '';

if (isset($_GET['url'])) {
    $url_to_fetch = $_GET['url'];
    $submitted_url = htmlspecialchars($url_to_fetch);

    // Looser validation for CTF to allow various localhost formats easily
    // This is NOT secure validation for a real application.
    $is_potentially_valid = (filter_var($url_to_fetch, FILTER_VALIDATE_URL) !== false) ||
                            preg_match('/^http(s)?:\/\/(localhost|127\.0\.0\.1|\[::1\])/i', $url_to_fetch);

    if (!$is_potentially_valid) {
        $error_message = "URL format appears invalid. Please provide a full URL (e.g., http://example.com:12345/get_flag).";
    } else {
        $ctx_options = [
            'http' => [
                'timeout' => 3, // 3 second timeout
                'user_agent' => 'DataHarbor-Importer/1.0 (SSRF-Challenge)',
                'header' => "X-Definitely-Not-An-Attack: true\r\n" // Example custom header
            ],
            'ssl' => [ // Allow self-signed certs for HTTPS if user tries https://localhost (though our internal service is HTTP)
                'verify_peer' => false,
                'verify_peer_name' => false,
            ],
        ];
        $ctx = stream_context_create($ctx_options);

        // Suppress errors from file_get_contents to handle them manually
        $content = @file_get_contents($url_to_fetch, false, $ctx);

        if ($content === FALSE) {
            $last_error = error_get_last();
            $err_msg_detail = $last_error ? htmlspecialchars($last_error['message']) : 'Reason unknown (possibly a timeout or DNS issue).';

            if (strpos(strtolower($err_msg_detail), 'connection refused') !== false) {
                $error_message = "Error fetching URL: Connection refused. Ensure the target server and port are correct and the service is running.";
            } elseif (strpos(strtolower($err_msg_detail), 'timed out') !== false || strpos(strtolower($err_msg_detail), 'timeout') !== false){
                $error_message = "Error fetching URL: The request timed out. The target server might be too slow, unresponsive, or the URL is incorrect.";
            } else {
                $error_message = "Error fetching URL: Could not open stream or an error occurred. Detail: " . $err_msg_detail;
            }
        } else {
            $page_content = htmlspecialchars($content); // Display fetched content safely
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Web Page Importer - DataHarbor Inc.</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style> body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; } </style>
</head>
<body class="bg-slate-900 text-gray-200 min-h-screen p-4 sm:p-8">
    <div class="container mx-auto max-w-3xl bg-slate-800 shadow-2xl rounded-xl p-6 sm:p-10">
        <header class="text-center mb-8">
            <h1 class="text-4xl font-bold text-sky-400">DataHarbor Web Page Importer</h1>
            <p class="text-slate-300 mt-2">Enter a URL to fetch and preview its content.</p>
        </header>

        <form method="GET" action="index.php" class="mb-8 p-6 bg-slate-700 rounded-lg shadow-md">
            <div class="flex flex-col sm:flex-row gap-4 items-end">
                <div class="flex-grow w-full sm:w-auto">
                    <label for="url" class="block text-sm font-semibold text-slate-300 mb-1.5">URL to Fetch:</label>
                    <input type="text" name="url" id="url"
                           class="w-full px-4 py-3 bg-slate-600 border border-slate-500 rounded-lg text-gray-100 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent placeholder-slate-400 text-sm"
                           value="<?php echo $submitted_url; ?>"
                           placeholder="e.g., http://example.com ">
                </div>
                <button type="submit"
                        class="w-full sm:w-auto bg-sky-600 hover:bg-sky-500 text-white font-bold py-3 px-6 rounded-lg shadow-md hover:shadow-lg transition-all text-base whitespace-nowrap">
                    Fetch Content
                </button>
            </div>
        </form>

        <?php if ($error_message): ?>
            <div class="my-6 p-4 bg-red-800 bg-opacity-40 text-red-300 border border-red-700 rounded-lg text-sm shadow">
                <p class="font-semibold text-red-200">Fetch Error:</p>
                <p><?php echo $error_message; ?></p>
            </div>
        <?php endif; ?>

        <?php if ($page_content !== null): ?>
            <div class="my-6">
                <h2 class="text-2xl font-semibold text-sky-300 mb-3">Fetched Content from <code class="text-sm bg-slate-700 p-1 rounded-md"><?php echo $submitted_url; ?></code>:</h2>
                <pre class="bg-slate-900 p-4 rounded-lg shadow-inner text-slate-300 text-xs sm:text-sm whitespace-pre-wrap break-all max-h-[30rem] overflow-y-auto border border-slate-700"><?php echo $page_content; ?></pre>
            </div>
        <?php endif; ?>

         <div class="mt-10 text-center text-xs text-slate-400 border-t border-slate-700 pt-6">
            <p><strong>Challenge Hint:</strong> An "EmployeeOfTheMonth" service is running on port `12345`. The endpoint to retrieve sensitive data is `/get_flag`.</p>
        </div>
    </div>
    <footer class="text-center text-slate-500 py-6 mt-8 text-sm">
        © <?php echo date("Y"); ?> DataHarbor Inc. - Securely Importing The Web (Maybe)
    </footer>
</body>
</html>
