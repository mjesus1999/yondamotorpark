
USE motorpark;

DROP PROCEDURE IF EXISTS sp_egresos_por_estado;
-- DELIMITER //
-- CREATE PROCEDURE sp_egresos_por_estado(IN requiereComprobante_ VARCHAR(10))
-- BEGIN

--     SELECT 
--     eg.idegreso,
--     DATE_FORMAT(eg.creado, '%d/%m/%Y') AS fecha,
--     ce.concepto,
--     CONCAT(p.apellidos,' ',' ',p.nombres) AS solicitante,
--     eg.monto,
--     eg.comentario
    
--     FROM egresos eg
--     JOIN  conceptoegreso ce ON eg.idconceptoegreso = ce.idconceptoegreso
--     JOIN colaboradores c ON eg.idcolsolicitante = c.idcolaborador
--     JOIN contratoslaborales cl ON c.idcontratolaboral = cl.idcontratolaboral
--     JOIN personas p ON cl.idpersona = p.idpersona 
--     WHERE requierecomprobante = requiereComprobante_ 
--     ORDER BY eg.creado DESC;   

-- END 

-- DELIMITER ;

SELECT * FROM contratoslaborales;







DELIMITER $$
CREATE PROCEDURE sp_egresos_por_estado(
    IN requiereComprobante_ VARCHAR(10)
)
BEGIN
  IF requiereComprobante_ = 'S' THEN
    SELECT
      eg.idegreso,
      DATE_FORMAT(eg.creado, '%d/%m/%Y') AS fecha,
      ce.concepto,
      CONCAT(p.apellidos, ' ', p.nombres) AS solicitante,
      eg.monto,
      eg.comentario
    FROM egresos eg
    JOIN conceptoegreso ce ON eg.idconceptoegreso = ce.idconceptoegreso
    JOIN colaboradores c ON eg.idcolsolicitante = c.idcolaborador
    JOIN contratoslaborales cl ON c.idcontratolaboral = cl.idcontratolaboral
    JOIN personas p ON cl.idpersona = p.idpersona
    JOIN comprobantes cm ON eg.idegreso = cm.idegreso
    WHERE eg.requierecomprobante = requiereComprobante_ AND cm.cargadocontabilidad = 'N'
    ORDER BY eg.creado DESC;
  ELSE
    SELECT
      eg.idegreso,
      DATE_FORMAT(eg.creado, '%d/%m/%Y') AS fecha,
      ce.concepto,
      CONCAT(p.apellidos, ' ', p.nombres) AS solicitante,
      eg.monto,
      eg.comentario
    FROM egresos eg
    JOIN conceptoegreso ce ON eg.idconceptoegreso = ce.idconceptoegreso
    JOIN colaboradores c ON eg.idcolsolicitante = c.idcolaborador
    JOIN contratoslaborales cl ON c.idcontratolaboral = cl.idcontratolaboral
    JOIN personas p ON cl.idpersona = p.idpersona
    WHERE eg.requierecomprobante = requiereComprobante_
    ORDER BY eg.creado DESC;
  END IF;
END
$$
DELIMITER ;


CALL sp_egresos_por_estado('S');



INSERT INTO egresos (idconceptoegreso,idcolacaja,idcolsolicitante,monto,comentario,requierecomprobante)
VALUES (1,2,6,3000.00,'Gastos','S');
        


DROP PROCEDURE IF EXISTS sp_egresos_add;
DELIMITER //

CREATE PROCEDURE sp_egresos_add(
    IN idconceptoegreso_ INT,
    IN idcolacaja_ INT,
    IN idcolsolicitante_ INT,
    IN monto_ DECIMAL(10,2),
    IN comentario_ VARCHAR(255),
    IN requierecomprobante_ CHAR(1)
)
BEGIN

    INSERT INTO egresos (idconceptoegreso, idcolacaja, idcolsolicitante, monto, comentario, requierecomprobante)
    VALUES (idconceptoegreso_, idcolacaja_, idcolsolicitante_, monto_, comentario_, requierecomprobante_);

    SELECT LAST_INSERT_ID() AS last_insert_id;


END

DELIMITER;

SELECT * FROM egresos;


CALL sp_egresos_add(1,2,6,5000.00,'Combustible','N');
DROP PROCEDURE IF EXISTS sp_egresos_add_comprobante;

SELECT * FROM egresos;

DELIMITER //
CREATE PROCEDURE sp_egresos_add_comprobante(
    IN idegreso_ INT,
    IN idproovedor_ INT,
    IN tipodoc_ VARCHAR(20),
    IN serie_ VARCHAR(50),
    IN numdocumento_ VARCHAR(50),
    IN monto_ DECIMAL(10,2),
    IN rutacomprobante_ VARCHAR(255)
)
BEGIN

    INSERT INTO comprobantes (idegreso,idproovedor,tipodoc, serie, numdocumento, monto, cargadocontabilidad, rutacomprobante)
    VALUES (idegreso_, idproovedor_,tipodoc_, serie_, numdocumento_, monto_, 'N', rutacomprobante_);


    SELECT LAST_INSERT_ID() AS last_insert_id;

END
DELIMITER;


CALL sp_egresos_add_comprobante(1,1,'F','F001','000123',3000.00,'/path/to/comprobante.pdf');


SELECT * FROM egresos;
SELECT * FROM comprobantes WHERE cargadocontabilidad = 'S';

UPDATE comprobantes SET rutacomprobante = 'boletas/boletas_68cd7f15bdcc2.pdf' WHERE idcomprobante = 7;





DELIMITER //
CREATE PROCEDURE sp_egresos_validados()
BEGIN
    SELECT 
        eg.idegreso,
        DATE_FORMAT(eg.creado, '%d/%m/%Y') AS fecha,
        ce.concepto,
        CONCAT(p.apellidos,' ',p.nombres) AS solicitante,
        eg.monto,
        eg.comentario,
        cm.numdocumento,
        cm.monto AS monto_validado
    FROM egresos eg
    JOIN conceptoegreso ce ON eg.idconceptoegreso = ce.idconceptoegreso
    JOIN colaboradores c ON eg.idcolsolicitante = c.idcolaborador
    JOIN contratoslaborales cl ON c.idcontratolaboral = cl.idcontratolaboral
    JOIN personas p ON cl.idpersona = p.idpersona
    JOIN comprobantes cm ON eg.idegreso = cm.idegreso
    WHERE eg.requierecomprobante = 'S' AND cm.cargadocontabilidad = 'S'
    ORDER BY eg.creado DESC;
END //
DELIMITER ;



SELECT * FROM conceptoegreso;



INSERT INTO conceptoegreso (concepto, descripcion)
VALUES ('Servicios de envío', 'Gastos relacionados con el envío de documentos, paquetes o encomiendas.');

