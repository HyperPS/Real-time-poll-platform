#!/bin/bash
set -e

echo "============================================="
echo "  Real-Time Poll Platform - Docker Launcher"
echo "============================================="
echo ""

NETWORK_NAME="poll_network"
MYSQL_CONTAINER="poll_mysql"
APP_CONTAINER="poll_app"
MYSQL_ROOT_PASS="rootpass123"
MYSQL_DB="polling_system"
MYSQL_USER="polluser"
MYSQL_PASS="pollpass123"
APP_PORT="${1:-8080}"
SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"

# Cleanup function
cleanup() {
    echo ""
    echo "[*] Cleaning up existing containers..."
    docker stop $APP_CONTAINER 2>/dev/null || true
    docker rm $APP_CONTAINER 2>/dev/null || true
    docker stop $MYSQL_CONTAINER 2>/dev/null || true
    docker rm $MYSQL_CONTAINER 2>/dev/null || true
    docker network rm $NETWORK_NAME 2>/dev/null || true
}

# Run cleanup first
cleanup

echo ""
echo "[1/5] Creating Docker network..."
docker network create $NETWORK_NAME

echo ""
echo "[2/5] Starting MySQL 8.0 container..."
docker run -d \
    --name $MYSQL_CONTAINER \
    --network $NETWORK_NAME \
    -e MYSQL_ROOT_PASSWORD=$MYSQL_ROOT_PASS \
    -e MYSQL_DATABASE=$MYSQL_DB \
    -e MYSQL_USER=$MYSQL_USER \
    -e MYSQL_PASSWORD=$MYSQL_PASS \
    -p 3307:3306 \
    mysql:8.0 \
    --default-authentication-plugin=mysql_native_password

echo ""
echo "[3/5] Waiting for MySQL to become ready..."
RETRIES=0
MAX_RETRIES=90
until docker exec $MYSQL_CONTAINER mysqladmin ping -h 127.0.0.1 -u root -p"$MYSQL_ROOT_PASS" --silent 2>/dev/null; do
    RETRIES=$((RETRIES + 1))
    if [ $RETRIES -ge $MAX_RETRIES ]; then
        echo ""
        echo "[ERROR] MySQL failed to start after ${MAX_RETRIES} seconds!"
        echo "Check logs: docker logs $MYSQL_CONTAINER"
        exit 1
    fi
    printf "."
    sleep 1
done
echo ""
echo "[✓] MySQL is ready!"

# Wait extra seconds for full initialization
sleep 3

echo ""
echo "[3.5/5] Importing database schema and seed data..."
docker exec -i $MYSQL_CONTAINER mysql -u root -p"$MYSQL_ROOT_PASS" "$MYSQL_DB" < "$SCRIPT_DIR/database/init/01-schema.sql"
echo "[✓] Database schema imported!"

# Grant privileges to the app user
docker exec $MYSQL_CONTAINER mysql -u root -p"$MYSQL_ROOT_PASS" -e "GRANT ALL PRIVILEGES ON ${MYSQL_DB}.* TO '${MYSQL_USER}'@'%'; FLUSH PRIVILEGES;" 2>/dev/null
echo "[✓] Database user privileges configured!"

echo ""
echo "[4/5] Building PHP application image..."
docker build -t poll_app_image "$SCRIPT_DIR"

echo ""
echo "[5/5] Starting application container..."
docker run -d \
    --name $APP_CONTAINER \
    --network $NETWORK_NAME \
    -e DB_HOST=$MYSQL_CONTAINER \
    -e DB_NAME=$MYSQL_DB \
    -e DB_USER=$MYSQL_USER \
    -e DB_PASS=$MYSQL_PASS \
    -p ${APP_PORT}:80 \
    poll_app_image

# Wait for Apache to start
sleep 2

echo ""
echo "============================================="
echo "  ✅ Platform is RUNNING!"
echo "============================================="
echo ""
echo "  🌐 Application URL: http://localhost:${APP_PORT}"
echo ""
echo "  📋 Test Credentials:"
echo "     Admin: admin@polling.test / admin123"
echo "     User:  user@polling.test / user123"
echo ""
echo "  🔧 API Endpoints:"
echo "     GET  http://localhost:${APP_PORT}/api/polls"
echo "     POST http://localhost:${APP_PORT}/api/vote/cast"
echo "     GET  http://localhost:${APP_PORT}/api/results?poll_id=1"
echo "     GET  http://localhost:${APP_PORT}/api/vote/status?poll_id=1"
echo ""
echo "  🛠 Management:"
echo "     docker logs $APP_CONTAINER    # App logs"
echo "     docker logs $MYSQL_CONTAINER  # DB logs"
echo "     docker stop $APP_CONTAINER $MYSQL_CONTAINER  # Stop"
echo "============================================="
