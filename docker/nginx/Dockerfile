FROM nginx

RUN rm /etc/nginx/conf.d/default.conf
COPY cert/ /etc/nginx/cert/
COPY nginx.conf /etc/nginx/
COPY default.conf /etc/nginx/conf.d/

RUN echo "upstream php-upstream { server php:9001; }" > /etc/nginx/conf.d/upstream.conf

CMD ["nginx"]

EXPOSE 80
EXPOSE 443
