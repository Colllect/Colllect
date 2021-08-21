#!/bin/sh
set -ex

# TODO: Remove SSL things in prod
# rm /etc/nginx/ssl/*.pem /etc/nginx/ssl/*.key

sed -i s/{{SERVER_NAME}}/${SERVER_NAME}/g /etc/nginx/sites-available/colllect*
ln -sf /etc/nginx/sites-available/colllect.conf /etc/nginx/sites-enabled/colllect.conf
ln -sf /etc/nginx/sites-available/colllect-ssl.conf /etc/nginx/sites-enabled/colllect-ssl.conf
