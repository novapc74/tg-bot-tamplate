FROM nginx:1.25

COPY ./embed/nginx/conf.d /etc/nginx/conf.d

HEALTHCHECK --interval=30s --timeout=5s --retries=3 \
    CMD curl --fail http://localhost:80 || exit 1
