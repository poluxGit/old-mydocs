# Jenkins CI plateform
FROM voduytuan/jenkins-php-docker

MAINTAINER Polux <polux@poluxfr.org>


USER root

RUN apt-get update && apt-get install -y --force-yes \
     php5-xsl
RUN php5enmod xsl

# PHP Unit installation

RUN wget https://phar.phpunit.de/phpunit.phar && chmod +x ./phpunit.phar && mv ./phpunit.phar /usr/bin/phpunit
RUN curl -OL https://squizlabs.github.io/PHP_CodeSniffer/phpcs.phar && chmod +x ./phpcs.phar && mv ./phpcs.phar /usr/bin/phpcs
RUN curl -OL https://squizlabs.github.io/PHP_CodeSniffer/phpcbf.phar && chmod +x ./phpcbf.phar && mv ./phpcbf.phar /usr/bin/phpcbf
RUN wget https://phar.phpunit.de/phploc.phar && chmod +x ./phploc.phar && mv ./phploc.phar /usr/bin/phploc
RUN wget http://static.pdepend.org/php/2.2.4/pdepend.phar && chmod +x ./pdepend.phar && mv ./pdepend.phar /usr/bin/pdepend
RUN wget http://static.phpmd.org/php/latest/phpmd.phar && chmod +x ./phpmd.phar && mv ./phpmd.phar /usr/bin/phpmd
RUN wget https://phar.phpunit.de/phpcpd.phar && chmod +x ./phpcpd.phar && mv ./phpcpd.phar /usr/bin/phpcpd
RUN wget https://github.com/theseer/phpdox/releases/download/0.9.0/phpdox-0.9.0.phar && chmod +x phpdox-0.9.0.phar && mv phpdox-0.9.0.phar /usr/bin/phpdox

EXPOSE  8080

#ENTRYPOINT ["/usr/sbin/apache2ctl", "-D", "FOREGROUND"]
