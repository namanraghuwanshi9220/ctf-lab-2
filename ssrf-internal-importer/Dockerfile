# ssrf-internal-importer/Dockerfile
FROM php:8.2-apache

# ARG for UV_VERSION to make it easily configurable at build time if needed
# Check for latest uv version: https://github.com/astral-sh/uv/releases
ARG UV_VERSION_ARG=0.1.33 # Example: Use a recent version of uv

# Install Python3, python3-venv (for creating virtual environments),
# curl (to download uv), ca-certificates (for HTTPS downloads)
RUN apt-get update && apt-get install -y \
    python3 \
    python3-venv \
    curl \
    ca-certificates \
    && rm -rf /var/lib/apt/lists/*

# Install uv (Astral's fast Python package installer)
ENV UV_INSTALL_DIR="/root/.local/bin"
RUN echo "Installing uv version ${UV_VERSION_ARG}..." \
    && mkdir -p ${UV_INSTALL_DIR} \
    && curl -LsSf https://astral.sh/uv/install.sh | sh \
    && echo "uv installed."

# Explicitly add uv's installation directory to the PATH
ENV PATH="${UV_INSTALL_DIR}:${PATH}"

# Verify uv installation (optional, good for debugging)
RUN uv --version

# Define the path for the Python virtual environment
ENV VENV_PATH="/opt/flask_venv"

# Create Python virtual environment and install Flask into it using uv
RUN echo "Creating Python virtual environment at ${VENV_PATH}..." \
    && python3 -m venv ${VENV_PATH} \
    && echo "Installing Flask with global 'uv' into the virtual environment '${VENV_PATH}'..." \
    && uv pip install Flask --python ${VENV_PATH}/bin/python3 \
    && echo "Flask installed in ${VENV_PATH}."

# Copy the PHP web application (importer)
# Assumes 'html' directory is in the build context
COPY html/ /var/www/html/

# Copy the Python internal flag service files
# Assumes 'internal_service' directory is in the build context
COPY internal_service/flag_app.py /opt/internal_service/flag_app.py
COPY internal_service/flag.txt /opt/internal_service/flag.txt

# WORKDIR for the context of commands run by entrypoint.sh or CMD,
# internal_service/flag_app.py will be run from /opt/internal_service due to `cd` in entrypoint.sh
WORKDIR /opt/internal_service

# Copy the entrypoint script and make it executable
# Assumes 'entrypoint.sh' is in the build context
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Apache runs on port 80
EXPOSE 80

# Set the entrypoint for the container
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
