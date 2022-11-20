FROM nginx:stable-alpine

WORKDIR /var/www/app

COPY docker/nginx/nginx.conf  /etc/nginx/
COPY docker/nginx/default.conf /etc/nginx/conf.d/

RUN echo "upstream php-upstream { server php:9000; }" > /etc/nginx/conf.d/upstream.conf

CMD ["nginx"]

EXPOSE 8000
