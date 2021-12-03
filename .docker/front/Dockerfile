FROM node:16.13.1-alpine3.14

ARG NODE_ENV=development
ENV NODE_ENV="${NODE_ENV}"

ENV NPM_CONFIG_PREFIX=/home/node/.npm
ENV PATH="/home/node/.npm/bin:${PATH}"
RUN mkdir -p ${NPM_CONFIG_PREFIX} \
    && chown -R node:node ${NPM_CONFIG_PREFIX} \
    && npm install -g npm@8

RUN apk add --no-cache \
    make \
    curl

# Install wait-for
RUN curl -sL -o /usr/bin/wait-for https://raw.githubusercontent.com/eficode/wait-for/v2.2.1/wait-for \
    && chmod +x /usr/bin/wait-for

USER node

WORKDIR /var/www/front

COPY entrypoint.sh /opt/

ENTRYPOINT ["/opt/entrypoint.sh", "/usr/local/bin/docker-entrypoint.sh"]
CMD ["npm", "run", "dev"]
