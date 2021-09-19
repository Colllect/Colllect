#!/usr/bin/env sh
set -xe

wait-for back-nginx:443 -t 60
make install

exec "$@"
