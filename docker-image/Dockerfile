FROM ubuntu

RUN apt-get update
RUN DEBIAN_FRONTEND=noninteractive TZ=America/Sao_Paulo apt-get -y install tzdata
RUN apt-get -y install vim nano git apache2 php8.1 libapache2-mod-php8.1 php-soap php-xml php-curl php-opcache php-gd php-sqlite3 php-mysql php-mbstring

RUN apt-get -y install python3 python3-pip
RUN python3 -m pip install requests
RUN python3 -m pip install yfinance

COPY ./deploy-config.sh /
RUN sh /deploy-config.sh
RUN rm /deploy-config.sh

COPY ./repo-config.sh /
RUN sh /repo-config.sh
RUN rm /repo-config.sh

RUN chmod -R 777 /var/www/html/
RUN echo "<?php phpinfo(); ?>" >> /var/www/html/index.php
RUN rm -rf /var/www/html/index.html

EXPOSE 80
EXPOSE 443

VOLUME /var/www/html/

CMD ["bash", "apachectl", "-D", "FOREGROUND"]
