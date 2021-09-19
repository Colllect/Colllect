FROM nginx:1.21.3-alpine

# nginx
COPY nginx.conf /etc/nginx/
COPY ingress.conf ingress-ssl.conf /etc/nginx/sites-available/
RUN mkdir -p /etc/nginx/sites-enabled \
    && ln -sf /etc/nginx/sites-available/ingress.conf /etc/nginx/sites-enabled/ingress.conf \
    && ln -sf /etc/nginx/sites-available/ingress-ssl.conf /etc/nginx/sites-enabled/ingress-ssl.conf \
    && rm /etc/nginx/conf.d/default.conf

RUN adduser --uid 1000 --disabled-password --system --ingroup www-data www-data

EXPOSE 80
EXPOSE 443
