FROM php:8.2-cli-alpine
# Instalar extensiones necesarias para conectar a MariaDB/MySQL usando PDO
RUN docker-php-ext-install pdo pdo_mysql
# Establecer el directorio de trabajo dentro del contenedor
WORKDIR /var/www/html
# Copiar todos los archivos de tu proyecto al contenedor
COPY . .
# Exponer el puerto 6000 hacia el contenedor
EXPOSE 6000
# COMANDO CLAVE PARA MVC: Iniciamos el servidor nativo apuntando a la carpeta /public
# El archivo public/index.php actuará como el enrutador (reemplaza al .htaccess)
CMD ["php", "-S", "0.0.0.0:6000", "-t", "public", "public/index.php"]
