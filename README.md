# projet5 Création d'un blog en php

Require 
php 7.2 >= 
;mysql 
;server web 

# Initialisation Configuration
create at the root of the project a file named .env with the following informations

# for Database connect
DB_NAME=...
DB_USER=...
DB_PASS=...
DB_HOST=...
DB_DRIVER=...
# Database connect

# for send Mailler connect
MAIL_USERNAME=...
MAIL_PASSWORD=...
# Mailler connect

import the sql file <<p5test.sql>> into your database

Update the project packages with the  <<composer install>> command

# Deploye
RUN php -S localhost:8000 -t public


# ENJOY #
