# jwt-alchemist-elixir/Dockerfile
FROM node:18-slim

WORKDIR /usr/src/app

# Copy package files and install dependencies
COPY app/package*.json ./
RUN npm install --only=production # Install only production dependencies

# Copy application source code
COPY app/ .

# Default port the app listens on
EXPOSE 3000

# Command to run the application
CMD [ "node", "server.js" ]
