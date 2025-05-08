#!/bin/bash
set -e # Exit immediately if a command exits with a non-zero status.

# Define the path to the virtual environment (should match Dockerfile ENV VENV_PATH)
VENV_PATH="/opt/flask_venv"

echo "Entrypoint: Starting internal flag service in background using venv..."
# Change to the directory where flag_app.py and flag.txt are located
cd /opt/internal_service

# Start the Python Flask app using the Python from the virtual environment
# The '&' runs it in the background.
# Output Python logs to stdout/stderr for Docker logging
${VENV_PATH}/bin/python3 flag_app.py &

# Capture the Process ID (PID) of the backgrounded Python process (optional, for logging)
PID_PYTHON=$!
echo "Entrypoint: Internal flag service (venv) started with PID $PID_PYTHON."

# Give the Python app a moment to start (optional, but can help avoid race conditions in some environments)
sleep 1

echo "Entrypoint: Starting Apache server in foreground..."
# Change directory back to root or a neutral location (good practice)
cd /
# Start Apache in the foreground. This will be the main process of the container.
exec apache2-foreground
