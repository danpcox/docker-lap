FROM ubuntu:14.04
# These all need to be on one line due to caching (http://stackoverflow.com/questions/37706635/in-docker-apt-get-install-fails-with-failed-to-fetch-http-archive-ubuntu-com)
RUN apt-get update && apt-get install -y apache2 && apt-get install -y php5 && apt-get install -y mysql-client && apt-get install -y php5-mysql

#ADD src /var/www/html
COPY etc/php5 /etc/php5
COPY etc/apache2 /etc/apache2

EXPOSE 80
EXPOSE 8080


CMD ["/usr/sbin/apache2ctl", "-D", "FOREGROUND"]
