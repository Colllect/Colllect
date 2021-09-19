#!/bin/sh
set -xe

make install

wait-for back-database:3306
make init-db

exec "$@"
