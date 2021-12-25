# p5 Création d'un blog en php

RUN composer update

RUN yarn install

RUN sass assets/scss/styles.scss:public/css/styles.css

RUN php -S localhost:8000 -t public
