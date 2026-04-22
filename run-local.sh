#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "$0")" && pwd)"
HOST="127.0.0.1"
PORT="8080"
URL="http://${HOST}:${PORT}/client/index.html"

if ! command -v php >/dev/null 2>&1; then
    echo "Error: php is not installed or not in PATH."
    exit 1
fi

echo "Starting local server from: ${ROOT_DIR}"
echo "URL: ${URL}"

if command -v lsof >/dev/null 2>&1 && lsof -iTCP:"${PORT}" -sTCP:LISTEN >/dev/null 2>&1; then
    echo "Warning: Port ${PORT} is already in use."
    echo "Close the existing process or edit PORT in run-local.sh."
    exit 1
fi

if command -v open >/dev/null 2>&1; then
    open "${URL}" >/dev/null 2>&1 || true
fi

cd "${ROOT_DIR}"
php -S "${HOST}:${PORT}" -t .