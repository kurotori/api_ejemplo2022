
drop schema if exists registro;
create schema registro;
use registro;
CREATE TABLE `usuario` (
  `nombre` varchar(12) NOT NULL DEFAULT '...',
  `ci` int NOT NULL,
  `fecha_nac` date NOT NULL DEFAULT '1900-01-01',
  `email` varchar(60) NOT NULL DEFAULT '...',
  `hash_1` varchar(100) DEFAULT NULL,
  `hash_2` varchar(100) DEFAULT NULL,
  `sal` varchar(100) DEFAULT NULL,
  `marcaTiempo` bigint DEFAULT NULL,
  PRIMARY KEY (`ci`),
  UNIQUE KEY `ci` (`ci`)
);
