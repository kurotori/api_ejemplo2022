
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

CREATE TABLE `sesion`(
  id binary(16) not null unique primary key,
  estado enum("abierta","cerrada") not null default "abierta",
  inicio timestamp not null default current_timestamp
);

CREATE TABLE inicia(
  usuario_ci int not null,
  sesion_id binary(16) not null primary key
);

alter table inicia
add constraint fk_usuario_inicia
foreign key (usuario_ci)
references usuario(ci)
on update cascade
on delete no action;

alter table inicia
add constraint fk_inicia_sesion
foreign key (sesion_id)
references sesion(id)
on update cascade
on delete no action;


/* PROCEDIMIENTOS */


/* Procedimiento para cerrar sesiones de un usuario */
delimiter //
drop procedure if exists cerrar_sesiones//
create procedure cerrar_sesiones(
  IN usuario_ci int
)
BEGIN

  /*Variables auxiliares para el procedimiento*/
  DECLARE s_cant int DEFAULT 0;

  /*Cambiamos el estado de toda otra sesión abierta por el usuario*/
  UPDATE sesion SET estado = "cerrada" 
  WHERE id in
    (SELECT sesion_id from inicia where `usuario_ci` = usuario_ci)
  AND estado = "abierta" ;

END //

delimiter ;

/*Procedimiento para abrir una sesión nueva*/
delimiter //
drop procedure if exists iniciar_sesion//
create procedure iniciar_sesion(
  IN usuario_ci int
)
BEGIN

  /*Variables auxiliares para el procedimiento*/
  DECLARE s_id binary(16);


  /*Cambiamos el estado de toda otra sesión abierta por el usuario*/
  CALL cerrar_sesiones(usuario_ci);

  /* Creamos una  nueva sesión y almacenamos su valor en la variable s_id*/
  SELECT UUID_TO_BIN(UUID()) INTO s_id;
  INSERT INTO sesion(id) values(s_id);

  /*Vinculamos al usuario con la nueva sesión*/
  INSERT INTO inicia(`usuario_ci`, sesion_id)
  values(usuario_ci, s_id);

  SELECT BIN_TO_UUID(s_id) as `id_sesion`;

END //

delimiter ;
