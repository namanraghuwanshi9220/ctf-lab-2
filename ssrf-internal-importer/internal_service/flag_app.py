# ssrf-internal-importer/internal_service/flag_app.py
from flask import Flask, Response
import logging # Use standard logging

app = Flask(__name__)

# Configure logging to minimize Flask's default output for a cleaner CTF console
log = logging.getLogger('werkzeug')
log.setLevel(logging.ERROR) # Only show errors from Werkzeug (Flask's dev server)
app.logger.setLevel(logging.ERROR) # Only show errors from the app itself

@app.route('/')
def hello():
    # print("Internal service / route hit", flush=True) # For debugging if needed
    return "Internal EmployeeOfTheMonth Service. Nothing at root.\n"

@app.route('/get_flag')
def get_flag_route():
    # print("Internal service /get_flag route hit", flush=True) # For debugging
    try:
        # Assuming flag.txt is in the same directory as this script
        # The WORKDIR in Dockerfile and `cd` in entrypoint.sh ensure this.
        with open('flag.txt', 'r') as f:
            flag_content = f.read().strip()
        return Response(flag_content, mimetype='text/plain')
    except FileNotFoundError:
        # print("ERROR: flag.txt not found by internal service!", flush=True)
        return "ERROR: Flag file not found on internal service!", 500
    except Exception as e:
        # print(f"Internal Server Error in flag_app: {str(e)}", flush=True)
        return f"Internal Server Error: {str(e)}", 500

if __name__ == '__main__':
    print("Starting internal flag service on 127.0.0.1:12345...", flush=True)
    # Listen only on localhost (127.0.0.1) within its container environment
    app.run(host='127.0.0.1', port=12345, debug=False)
