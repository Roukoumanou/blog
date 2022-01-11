<?php

use App\Repository\Manager;
use Doctrine\ORM\Tools\Console\ConsoleRunner;

$entityManager = (Manager::getInstance()->getEm());

return ConsoleRunner::createHelperSet($entityManager);
