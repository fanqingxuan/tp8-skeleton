# 使用官方PHP镜像作为基础镜像
FROM php:8.1-fpm

# 设置工作目录
WORKDIR /var/www/html

# 安装必要的系统工具和扩展
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    && docker-php-ext-install pdo_mysql gd mbstring xml

RUN pecl install redis \
    && docker-php-ext-enable redis

# 安装Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# 将项目文件复制到容器中
COPY . .

# 安装项目依赖
RUN composer install --no-scripts --no-autoloader

# 生成自动加载文件
RUN composer dump-autoload

# 设置权限
RUN chown -R www-data:www-data /var/www/html

# 暴露端口
EXPOSE 9000

# 启动PHP-FPM服务
CMD ["php-fpm"]