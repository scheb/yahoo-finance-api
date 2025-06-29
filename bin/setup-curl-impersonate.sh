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

wget -O libcurl-impersonate.tar.gz https://github.com/lexiforest/curl-impersonate/releases/download/v1.0.3/libcurl-impersonate-v1.0.3.x86_64-linux-gnu.tar.gz
echo "e4d2066f7f1c544a2a0ddadfe1d164cb3daffdefcb3143363b0afd6d2f2125e7  libcurl-impersonate.tar.gz" > checksum.sha256
sha256sum --check checksum.sha256 || exit 1
tar -xf libcurl-impersonate.tar.gz

patchelf --set-soname libcurl.so.4 "libcurl-impersonate.so"

ls -l
