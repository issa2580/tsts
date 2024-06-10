FROM mysql:5.7

ENV MYSQL_ROOT_PASSWORD=root
ENV MYSQL_DATABASE=redline

ADD database/redline.sql /docker-entrypoint-initdb.d/