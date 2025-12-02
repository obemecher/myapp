FROM mysql:8.0.19
COPY ./dump/maindata_backup.sql /docker-entrypoint-initdb.d/init.sql
