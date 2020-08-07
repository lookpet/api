FROM nginx:1.19.1-alpine

WORKDIR /app/public
COPY ./public /app/public
COPY ./docker/nginx/nginx.conf /etc/nginx/nginx.conf
COPY ./docker/nginx/sites/ /etc/nginx/sites-available
COPY ./docker/nginx/conf.d/ /etc/nginx/conf.d
#COPY ./docker/nginx/ssl/ssl.crt /lookpet.ssl.crt
#COPY ./docker/nginx/ssl/ssl.key /lookpet.ssl.key

CMD ["nginx"]

EXPOSE 80 443