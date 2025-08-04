
FROM oven/bun:alpine AS build
WORKDIR /app
COPY package.json ./
RUN bun install
COPY . .
RUN bun run build

FROM nginx:alpine AS runtime

LABEL maintainer="Jorge Thomas <info@notakrista.com>"
LABEL org.opencontainers.image.title="www.notakrista.com Dockerfile"
LABEL org.opencontainers.image.description="Dockerfile for www.notakrista.com"
LABEL org.opencontainers.image.source=https://github.com/akrista/www.notakrista.com

COPY ./nginx.conf /etc/nginx/nginx.conf
COPY --from=build /app/dist /usr/share/nginx/html
EXPOSE 8000
