FROM ubuntu:14.04
ENV DEBIAN_FRONTEND noninteractive

# Set timezone to Asia/Jakarta
ENV TZ=Asia/Jakarta

# Update package lists and install necessary packages
RUN apt-get update && \
    apt-get install -y \
    apache2 \
    tzdata \
    apache2-utils \
    php5 \
    php5-cli \
    php5-curl \
    php5-gd \
    php5-mysql \
    rsync \ 
    openssh-client \
    cron \
    nano \
    joe \
    net-tools \
    iputils-ping \
    htop \
    stress \
    libapache2-mod-php5 \
    wget \
    nfs-client \
    lsb-release && \
    apt-get clean

# Copy configuration files
COPY ./php.ini /etc/php5/apache2/php.ini
COPY ./mpm_prefork.conf /etc/apache2/mods-enabled/mpm_prefork.conf
COPY ./apache2.conf /etc/apache2/apache2.conf
COPY ./000-default.conf /etc/apache2/sites-available/000-default.conf
COPY ./component /var/www/prg/app/component
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/prg/app|' /etc/apache2/sites-available/000-default.conf

# Create necessary directories and set permissions
RUN mkdir -p /var/www/prg/app \
    && chmod -R 777 /var/www/prg/app

# Remove default html directory
RUN rm -rf /var/www/html

# Enable Apache modules
RUN a2enmod rewrite

# Copy application files
COPY ./index.php /var/www/prg/app/

# Expose ports
#EXPOSE 80

# Install MySQL
ENV MYSQL_ROOT_PASSWORD your_root_password
ENV MYSQL_USER Assist
ENV MYSQL_PASSWORD Irac

RUN apt-get update && \
    apt-get install -y mysql-server mysql-client && \
    sed -i 's/127.0.0.1/0.0.0.0/g' /etc/mysql/my.cnf && \
    service mysql start && \
    mysql -e "CREATE USER 'Assist'@'localhost' IDENTIFIED BY '$MYSQL_PASSWORD'; \
              GRANT ALL PRIVILEGES ON *.* TO 'Assist'@'localhost'; \
              CREATE USER 'mysqlchkuser'@'localhost' IDENTIFIED BY '$MYSQL_PASSWORD'; \
              CREATE USER 'mmm_monitor'@'%' IDENTIFIED BY '$MYSQL_PASSWORD'; \
              CREATE USER 'mmm_agent'@'%' IDENTIFIED BY '$MYSQL_PASSWORD'; \
              CREATE USER 'replication'@'%' IDENTIFIED BY '$MYSQL_PASSWORD'; \
              CREATE USER 'Assist'@'%' IDENTIFIED BY '$MYSQL_PASSWORD'; \
              GRANT REPLICATION CLIENT ON *.* TO 'mmm_monitor'@'%'; \
              GRANT SUPER, REPLICATION CLIENT, PROCESS ON *.* TO 'mmm_agent'@'%'; \
              GRANT REPLICATION SLAVE ON *.* TO 'replication'@'%'; \
              GRANT ALL PRIVILEGES ON *.* TO 'Assist'@'%' WITH GRANT OPTION; \
              FLUSH HOSTS; FLUSH PRIVILEGES; FLUSH TABLES WITH READ LOCK;"

#EXPOSE 3306

# Copy custom my.cnf
COPY ./my.cnf /etc/mysql/my.cnf
RUN chown root:root /etc/mysql/my.cnf && chmod 644 /etc/mysql/my.cnf

# Start services
CMD service apache2 start && service mysql start && service rpcbind start && service cron start && tail -f /dev/null
# ini editan dari git 2
# ini editan dari git 8 juli 2025 2
  
