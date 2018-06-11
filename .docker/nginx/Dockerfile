FROM nginx:1.15.0

MAINTAINER Alexandre DEMDOE <contact@alex-d.fr>

# Let's Encrypt
RUN apt-get update && apt-get install -y --no-install-recommends \
    openssl \
    && rm -rf /var/lib/apt/lists/*

# SSL for localhost development
COPY openssl.cnf /etc/nginx/ssl/
RUN mkdir -p /etc/nginx/ssl \
    && openssl rand 48 > /etc/nginx/ssl/ticket.key \
    && openssl dhparam -out /etc/nginx/ssl/dhparam4.pem 4096 \
    && openssl req -x509 -out /etc/nginx/ssl/fullchain.pem -keyout /etc/nginx/ssl/privkey.pem \
         -newkey rsa:2048 -nodes -sha256 \
         -subj '/CN=localhost' -extensions EXT -config /etc/nginx/ssl/openssl.cnf

# nginx
COPY nginx.conf /etc/nginx/
COPY colllect.conf /etc/nginx/sites-available/
COPY colllect-ssl.conf /etc/nginx/sites-available/
RUN mkdir -p /etc/nginx/sites-enabled
RUN echo "upstream php-upstream { server php:9000; }" > /etc/nginx/conf.d/upstream.conf

RUN usermod -u 1000 www-data

EXPOSE 80
EXPOSE 443

COPY docker-entrypoint.sh /opt/
RUN chmod +x /opt/docker-entrypoint.sh

ENTRYPOINT ["/opt/docker-entrypoint.sh"]
