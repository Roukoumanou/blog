# projet5 Création d'un blog en php

Require php 7.2 >=

RUN composer 

Create the .env file You will need to set the following global variables:

# ##> Database connect <## #
DB_NAME=...
DB_USER=...
DB_PASS=...
DB_HOST=...
DB_DRIVER=...
# ##! Database connect !## #

# ##> Mailler connect <## #
MAIL_USERNAME=...
MAIL_PASSWORD=...
# ##! Mailler connect !## #

# RUN this commande for create database
vendor/bin/doctrine orm:schema-tool:create

#
RUN php -S localhost:8000 -t public


# ENJOY #
