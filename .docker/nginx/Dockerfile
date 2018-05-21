FROM nginx:1.15.0

MAINTAINER Alexandre DEMDOE <contact@alex-d.fr>

ARG SERVER_NAME

# nginx
ADD nginx.conf /etc/nginx/
ADD colllect.conf /etc/nginx/sites-available/
ADD colllect-ssl.conf /etc/nginx/sites-available/
RUN sed -i s/{{SERVER_NAME}}/${SERVER_NAME}/g /etc/nginx/sites-available/colllect*
RUN mkdir -p /etc/nginx/sites-enabled && \
    ln -s /etc/nginx/sites-available/colllect.conf /etc/nginx/sites-enabled/colllect
RUN echo "upstream php-upstream { server php:9000; }" > /etc/nginx/conf.d/upstream.conf

# Let's Encrypt
RUN apt-get update && apt-get install -y --no-install-recommends ca-certificates git \
    && git clone https://github.com/letsencrypt/letsencrypt /opt/letsencrypt --depth=1 \
    && rm -rf /var/lib/apt/lists/*

RUN usermod -u 1000 www-data

CMD ["nginx"]

EXPOSE 80
EXPOSE 443
