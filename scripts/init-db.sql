CREATE SCHEMA `e-gens` DEFAULT CHARACTER SET utf8 ;
USE `e-gens`;
CREATE USER `gens-agent`@`localhost` IDENTIFIED BY 'my-secret-pw';
GRANT ALL PRIVILEGES ON * . * TO `gens-agent`@`localhost`;
FLUSH PRIVILEGES;
