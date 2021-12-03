FROM nginx:1.21.4-alpine

# nginx
COPY nginx.conf /etc/nginx/
COPY colllect-ssl.conf /etc/nginx/sites-available/
RUN mkdir -p /etc/nginx/sites-enabled \
    && ln -sf /etc/nginx/sites-available/colllect-ssl.conf /etc/nginx/sites-enabled/colllect-ssl.conf \
    && rm /etc/nginx/conf.d/default.conf
RUN echo "upstream php-upstream { server back-php:9000; }" > /etc/nginx/conf.d/upstream.conf

# Install wait-for
RUN curl -sL -o /usr/bin/wait-for https://raw.githubusercontent.com/eficode/wait-for/v2.2.1/wait-for \
    && chmod +x /usr/bin/wait-for

RUN adduser --uid 1000 --disabled-password --system --ingroup www-data www-data

EXPOSE 443

COPY entrypoint.sh /docker-entrypoint.d/99-wait-for-php.sh
