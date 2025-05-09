#!/bin/sh
set -e # Exit on error

echo "Setting up RootReggie's scattered secrets..."

# Place the first clue in /tmp/
# Note: /tmp/ might be cleared on container restarts depending on Docker setup/base image.
# For this lab, it's fine as it's set up on first run.
cp /app_clues/clue1_in_tmp.txt /tmp/reggie_note_alpha.txt
chmod 444 /tmp/reggie_note_alpha.txt
echo "Clue 1 placed in /tmp/reggie_note_alpha.txt"

# Create the directory for the second clue and place the final flag file there
mkdir -p /opt/secret_server_configs/config_backups_v3_final_DO_NOT_DELETE
cp /app_clues/final_flag_location.txt /opt/secret_server_configs/config_backups_v3_final_DO_NOT_DELETE/access_codes_archive.txt.bak
chmod 444 /opt/secret_server_configs/config_backups_v3_final_DO_NOT_DELETE/access_codes_archive.txt.bak
echo "Final flag file placed in /opt/secret_server_configs/config_backups_v3_final_DO_NOT_DELETE/access_codes_archive.txt.bak"

echo "Setup complete. RootReggie's secrets are now scattered."

# Now, execute the original command for the container (e.g., start Apache)
# This script will be run via Dockerfile's CMD or ENTRYPOINT after copying clues.
# For php:apache, the default CMD is 'apache2-foreground'. We'll call that.
exec "$@"
