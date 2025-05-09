  import express from 'express';
    import jwt from 'jsonwebtoken';
    import cookieParser from 'cookie-parser';
    import dotenv from 'dotenv';
    import fs from 'fs/promises'; // For reading flag file
    import path from 'path';
    import { fileURLToPath } from 'url';

    // Load environment variables (like the secret)
    dotenv.config();

    const __filename = fileURLToPath(import.meta.url);
    const __dirname = path.dirname(__filename);

    const app = express();
    const PORT = process.env.PORT || 3000;

    // THE WEAK SECRET! Get from .env or default if not set
    const JWT_SECRET = process.env.JWT_SECRET || 'alchemy'; // Hardcoded default if .env fails

    // Middleware
    app.use(express.json()); // for parsing application/json
    app.use(express.urlencoded({ extended: true })); // for parsing application/x-www-form-urlencoded
    app.use(cookieParser()); // To parse cookies, where we'll store the JWT

    // --- Dummy User Database ---
    const users = {
        guest: { id: 2, password: 'password', role: 'guest' },
        admin: { id: 1, password: 'SuperSecretAdminPasswordYouCantGuess', role: 'admin' }
    };

    // --- Routes ---

    // Homepage / Login Form
    app.get('/', (req, res) => {
        // Simple login form
        res.send(`
      <!DOCTYPE html>
      <html lang="en">
      <head><meta charset="UTF-8"><title>alchemy Login</title><script src="https://cdn.tailwindcss.com"></script></head>
      <body class="bg-gray-800 text-white flex items-center justify-center min-h-screen">
        <div class="bg-gray-700 p-8 rounded shadow-lg w-full max-w-sm">
          <h1 class="text-2xl font-bold mb-6 text-center text-yellow-400">alchemy login</h1>
          <form action="/login" method="POST">
            <div class="mb-4">
              <label for="username" class="block mb-2">Username:</label>
              <input type="text" id="username" name="username" required class="w-full p-2 rounded bg-gray-600 border border-gray-500 focus:outline-none focus:border-yellow-500">
            </div>
            <div class="mb-6">
              <label for="password" class="block mb-2">Password:</label>
              <input type="password" id="password" name="password" required class="w-full p-2 rounded bg-gray-600 border border-gray-500 focus:outline-none focus:border-yellow-500">
            </div>
            <button type="submit" class="w-full bg-yellow-600 hover:bg-yellow-700 text-black font-bold py-2 px-4 rounded">Enter Laboratory</button>
          </form>
          <p class="text-xs text-gray-400 mt-4 text-center">Use guest/password to get started.</p>
        </div>
      </body>
      </html>
    `);
    });

    // Login Endpoint
    app.post('/login', (req, res) => {
        const { username, password } = req.body;
        const user = users[username];

        if (user && user.password === password) {
            // User authenticated, generate JWT
            const payload = {
                userId: user.id,
                username: username,
                role: user.role
                // iat: Math.floor(Date.now() / 1000) // Issued at timestamp (optional)
                // exp: Math.floor(Date.now() / 1000) + (60 * 60) // Expires in 1 hour (optional)
            };

            // Sign the token with the WEAK SECRET
            const token = jwt.sign(payload, JWT_SECRET, { algorithm: 'HS256' });

            // Set token in an HTTPOnly cookie (standard practice)
            // For the CTF, players will need to extract this from browser storage or network requests.
            res.cookie('auth_token', token, {
                httpOnly: true, // Important for security, but player needs to find it
                // secure: true, // Should be true in production with HTTPS
                // sameSite: 'strict'
                maxAge: 3600000 // 1 hour
            });

            res.redirect('/dashboard');
        } else {
            res.status(401).send('Invalid credentials. <a href="/">Try again</a>');
        }
    });

    // Simple Dashboard
    app.get('/dashboard', (req, res) => {
        const token = req.cookies.auth_token;
        if (!token) return res.redirect('/');

        try {
            const decoded = jwt.verify(token, JWT_SECRET); // Verify with the same weak secret
            res.send(`
        <!DOCTYPE html><html lang="en"><head><title>Dashboard</title><script src="https://cdn.tailwindcss.com"></script></head>
        <body class="bg-gray-800 text-white p-8">
          <h1 class="text-3xl font-bold text-yellow-400 mb-4">Welcome, ${decoded.username}!</h1>
          <p class="mb-2">Your Role: ${decoded.role}</p>
          <p class="mb-6">Your authentication token is stored in your browser's cookies.</p>
          <a href="/secrets" class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded mr-4">Try Accessing Secrets</a>
          <a href="/logout" class="text-red-400 hover:text-red-300">Logout</a>
          <div class="mt-8 p-4 bg-gray-700 rounded text-xs">
            <p>Your JWT (for analysis):</p>
            <pre class="whitespace-pre-wrap break-all bg-gray-900 p-2 rounded mt-2">${token}</pre>
          </div>
        </body></html>
      `);
        } catch (err) {
            console.error("Dashboard verify error:", err.message);
            res.clearCookie('auth_token');
            res.status(401).send('Invalid or expired token. <a href="/">Login again</a>');
        }
    });

    // Protected Secrets Endpoint
    app.get('/secrets', async (req, res) => {
        const token = req.cookies.auth_token;

        if (!token) {
            return res.status(401).send('Access Denied: No token provided. <a href="/">Login</a>');
        }

        try {
            // Verify the token with the weak secret
            const decoded = jwt.verify(token, JWT_SECRET);

            // Check if the user has the 'admin' role
            if (decoded.role === 'admin') {
                // User is admin, reveal the secret flag
                try {
                    const flag = await fs.readFile(path.join(__dirname, 'flag.txt'), 'utf-8');
                    res.send(`
            <!DOCTYPE html><html lang="en"><head><title>SECRET REVEALED</title><script src="https://cdn.tailwindcss.com"></script></head>
            <body class="bg-green-900 text-white p-10 text-center">
              <h1 class="text-4xl font-bold text-yellow-300 mb-6">ACCESS GRANTED - ALCHEMY MASTER!</h1>
              <p class="text-xl mb-4">You have proven your mastery. The secret of digital transmutation is:</p>
              <p class="font-mono bg-gray-900 p-4 rounded inline-block text-2xl text-yellow-400 shadow-lg">${flag.trim()}</p>
              <p class="mt-8"><a href="/dashboard" class="text-sky-300 hover:text-sky-100">← Back to Dashboard</a></p>
            </body></html>
          `);
                } catch (flagReadError) {
                    console.error("Error reading flag file:", flagReadError);
                    res.status(500).send('Internal Error: Could not retrieve the secret knowledge.');
                }
            } else {
                // User is not admin
                res.status(403).send(`Access Denied: Welcome, ${decoded.username}. Your role ('${decoded.role}') does not permit access to the ultimate secrets. Only 'admin' may enter. <a href="/dashboard">Go Back</a>`);
            }
        } catch (err) {
            // Token is invalid or expired
            console.error("Secrets verify error:", err.message);
            res.clearCookie('auth_token');
            res.status(401).send('Access Denied: Invalid or expired token. <a href="/">Login again</a>');
        }
    });

    // Logout
    app.get('/logout', (req, res) => {
        res.clearCookie('auth_token');
        res.redirect('/');
    });

    // Start Server
    app.listen(PORT, () => {
        console.log(`Alchemist's JWT Elixir listening on port ${PORT}`);
        console.log(`Using JWT Secret: "${JWT_SECRET}"`); // Log the secret for easy verification during testing
    });
