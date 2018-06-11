#!/bin/bash

set -ex

if [ "$ENVIRONMENT" = "production" ]
then
    rm /etc/nginx/ssl/*.pem /etc/nginx/ssl/*.key
fi

sed -i s/{{SERVER_NAME}}/${SERVER_NAME}/g /etc/nginx/sites-available/colllect*
ln -sf /etc/nginx/sites-available/colllect.conf /etc/nginx/sites-enabled/colllect.conf
ln -sf /etc/nginx/sites-available/colllect-ssl.conf /etc/nginx/sites-enabled/colllect-ssl.conf
nginx
