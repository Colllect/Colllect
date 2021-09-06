FROM nginx:1.21.0-alpine

MAINTAINER Alexandre DEMDOE <contact@alex-d.fr>

# OpenSSL
RUN apk add --no-cache \
    openssl

# SSL for localhost development
COPY openssl.cnf /etc/nginx/ssl/
RUN mkdir -p /etc/nginx/ssl \
    && openssl rand 48 > /etc/nginx/ssl/ticket.key \
    && openssl dhparam -out /etc/nginx/ssl/dhparam4.pem 2048 \
    && openssl req -x509 -out /etc/nginx/ssl/fullchain.pem -keyout /etc/nginx/ssl/privkey.pem \
         -newkey rsa:2048 -nodes -sha256 \
         -subj '/CN=localhost' -extensions EXT -config /etc/nginx/ssl/openssl.cnf

# nginx
COPY nginx.conf /etc/nginx/
COPY colllect.conf colllect-ssl.conf /etc/nginx/sites-available/
RUN mkdir -p /etc/nginx/sites-enabled
RUN echo "upstream php-upstream { server php:9000; }" > /etc/nginx/conf.d/upstream.conf

RUN adduser --uid 1000 --disabled-password --system --ingroup www-data www-data

EXPOSE 80
EXPOSE 443

COPY entrypoint.sh /docker-entrypoint.d
