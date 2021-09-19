FROM node:16.9.1-alpine3.14

ARG NODE_ENV=development
ENV NODE_ENV="${NODE_ENV}"

ENV NPM_CONFIG_PREFIX=/home/node/.npm
ENV PATH="/home/node/.npm/bin:${PATH}"
RUN npm install -g npm@7.24.0

RUN apk add --no-cache \
    make \
    curl

# Install wait-for
RUN curl -sL -o /usr/bin/wait-for https://raw.githubusercontent.com/eficode/wait-for/v2.1.3/wait-for \
    && chmod +x /usr/bin/wait-for

USER node

WORKDIR /var/www/front

COPY entrypoint.sh /opt/

ENTRYPOINT ["/opt/entrypoint.sh", "/usr/local/bin/docker-entrypoint.sh"]
CMD ["npm", "run", "dev"]
