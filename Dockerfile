FROM php:8.2-cli-alpine
# Instalar extensiones necesarias para conectar a MariaDB/MySQL usando PDO
RUN docker-php-ext-install pdo pdo_mysql
# Establecer el directorio de trabajo dentro del contenedor
WORKDIR /var/www/html
# Copiar todos los archivos de tu proyecto al contenedor
COPY . .
# Exponer el puerto 8080 hacia el contenedor
EXPOSE 8081
CMD ["php", "-S", "0.0.0.0:8081", "-t", "public", "public/index.php"]
