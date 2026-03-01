#!/bin/bash
set -e

# Set installation directory based on environment
if [ -n "$GITHUB_ACTIONS" ]; then
    INSTALL_DIR="/home/runner/curl-impersonate"
else
    INSTALL_DIR="./curl-impersonate"
fi

mkdir -p "$INSTALL_DIR" || true
cd "$INSTALL_DIR"

wget -O libcurl-impersonate.tar.gz https://github.com/lexiforest/curl-impersonate/releases/download/v1.3.1/libcurl-impersonate-v1.3.1.x86_64-linux-gnu.tar.gz
echo "14166d04cac7fb241c041a0f847de96ccca77e7eda483edf73d65f906e1d38e6  libcurl-impersonate.tar.gz" > checksum.sha256
sha256sum --check checksum.sha256 || exit 1
tar -xf libcurl-impersonate.tar.gz

patchelf --set-soname libcurl.so.4 "libcurl-impersonate.so"

ls -l
