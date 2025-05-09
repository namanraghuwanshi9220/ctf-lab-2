<?php
// pingmaster_scattered_secrets/html/index.php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$output = null;
$error_message = null;
$submitted_host = '';

if (isset($_GET['host'])) {
    $host_to_ping = $_GET['host'];
    $submitted_host = htmlspecialchars($host_to_ping);

    if (empty(trim($host_to_ping))) {
        $error_message = "RootReggie says: 'You gotta give me a host to ping, mate!'";
    } else {
        $command = "ping -c 3 -W 2 " . $host_to_ping;
        $output = shell_exec($command . " 2>&1");

        if ($output === null && $host_to_ping !== '') {
            $error_message = "RootReggie's system whimpers: 'Ping command failed or isn't installed properly.'";
        } elseif (empty(trim($output)) && $host_to_ping !== '') {
            $output = "[PingMaster executed. No direct output received from the command.]";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PingMaster Supreme - RootReggie's Edition</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Orbitron', sans-serif; letter-spacing: 0.5px; }
        @import url('https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&display=swap');
        .terminal-output {
            background-color: #0D1117; /* GitHub Dark Dimmed BG */
            color: #C9D1D9; /* GitHub Dark Dimmed Text */
            border: 1px solid #30363D;
            max-height: 500px; overflow-y: auto; white-space: pre-wrap; word-wrap: break-word;
            line-height: 1.6; font-family: 'Fira Code', 'Consolas', monospace;
        }
        .scanlines {
             position: relative;
             overflow: hidden;
        }
        .scanlines::before {
             content: ""; position: absolute; top: 0; left: 0; width: 100%; height: 100%;
             opacity: 0.08; pointer-events: none;
             background: linear-gradient(rgba(0,0,0,0.5) 50%, rgba(0,0,0,0) 50%), linear-gradient(90deg, rgba(255,0,0,0.06), rgba(0,255,0,0.02), rgba(0,0,255,0.06));
             background-size: 100% 3px, 5px 100%;
             animation: scan 0.2s linear infinite;
        }
        @keyframes scan { 0% { background-position: 0 0; } 100% { background-position: 0 3px; } }
    </style>
</head>
<body class="bg-gradient-to-br from-indigo-900 via-purple-900 to-blue-900 text-gray-100 min-h-screen p-4 sm:p-8">
    <div class="container mx-auto max-w-4xl bg-slate-800 bg-opacity-80 backdrop-blur-md shadow-2xl rounded-xl p-6 sm:p-10 border border-purple-700 scanlines">
        <header class="text-center mb-10">
            <h1 class="text-4xl sm:text-5xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-sky-400 via-fuchsia-500 to-pink-500 pb-2">
                PingMaster <span class="text-purple-400">Supreme</span>
            </h1>
            <p class="text-indigo-300 mt-3 text-lg">RootReggie's Personal Network Diagnostic Suite!</p>
        </header>

        <form method="GET" action="index.php" class="mb-10 p-6 bg-slate-700 bg-opacity-70 rounded-lg shadow-xl">
            <div class="flex flex-col sm:flex-row gap-4 items-center sm:items-end">
                <div class="flex-grow w-full">
                    <label for="host" class="block text-sm font-semibold text-indigo-300 mb-1.5">Target Host/IP (or other... commands?):</label>
                    <input type="text" name="host" id="host"
                           class="w-full px-4 py-3 bg-slate-600 border-2 border-purple-600 rounded-lg text-gray-100 focus:outline-none focus:ring-2 focus:ring-fuchsia-500 focus:border-transparent placeholder-slate-400 text-base"
                           value="<?php echo $submitted_host; ?>"
                           placeholder="8.8.8.8; id">
                </div>
                <button type="submit"
                        class="w-full sm:w-auto bg-gradient-to-r from-purple-600 to-fuchsia-600 hover:from-purple-700 hover:to-fuchsia-700 text-white font-bold py-3 px-8 rounded-lg shadow-lg hover:shadow-xl transition-all text-base whitespace-nowrap transform hover:scale-105">
                    Execute
                </button>
            </div>
        </form>

        <?php if ($error_message): ?>
            <div class="my-6 p-4 bg-red-900 bg-opacity-60 text-red-300 border border-red-700 rounded-lg text-sm shadow-md">
                <p class="font-semibold text-red-200">System Alert from RootReggie:</p>
                <p><?php echo htmlspecialchars($error_message); ?></p>
            </div>
        <?php endif; ?>

        <?php if ($output !== null): ?>
            <div class="my-6">
                <h2 class="text-2xl font-semibold text-indigo-300 mb-4">System Output for <code class="text-base bg-slate-700 p-1 rounded-md"><?php echo $submitted_host; ?></code>:</h2>
                <pre class="terminal-output p-5 rounded-lg shadow-inner text-sm"><?php echo htmlspecialchars($output); ?></pre>
            </div>
        <?php endif; ?>

        <div class="mt-12 text-center text-xs text-indigo-400 opacity-80 border-t border-purple-800 pt-6">
            <p>Remember RootReggie's motto: "Efficiency over obsessive tidiness!" There might be clues scattered around.</p>
            <p class="mt-1">He often uses `/tmp/`, `/opt/`, and sometimes forgets where he puts backups...</p>
        </div>
    </div>
    <footer class="text-center text-purple-400 opacity-70 py-6 mt-8 text-sm">
        © <?php echo date("Y"); ?> Overly-Trusting Systems Inc. Use Responsibly.
    </footer>
</body>
</html>
