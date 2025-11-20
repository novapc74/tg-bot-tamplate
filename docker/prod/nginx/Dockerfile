FROM nginx:1.25

# Копируем конфиг nginx (предполагаем, что у вас есть prod-конфиг, аналогичный dev, но с SSL)
#COPY docker/embed/nginx/default.prod.conf /etc/nginx/conf.d/default.conf
COPY docker/embed/nginx/prod /etc/nginx/conf.d

# Копируем сайт
COPY project/public /var/www/html

# Создаем директории для сертификатов (если их нет, certbot создаст)
RUN mkdir -p /etc/letsencrypt/live/yourdomain.com /var/www/html/.well-known

# Устанавливаем права
RUN chown -R nginx:nginx /var/www/html /etc/letsencrypt

EXPOSE 80 443

CMD ["nginx", "-g", "daemon off;"]
