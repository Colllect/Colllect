FROM nginx:1.21.3-alpine

# nginx
COPY nginx.conf /etc/nginx/
COPY colllect-ssl.conf /etc/nginx/sites-available/
RUN mkdir -p /etc/nginx/sites-enabled \
    && ln -sf /etc/nginx/sites-available/colllect-ssl.conf /etc/nginx/sites-enabled/colllect-ssl.conf \
    && rm /etc/nginx/conf.d/default.conf
RUN echo "upstream php-upstream { server back-php:9000; }" > /etc/nginx/conf.d/upstream.conf

RUN adduser --uid 1000 --disabled-password --system --ingroup www-data www-data

EXPOSE 443
