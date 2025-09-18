
USE motorpark;

DROP PROCEDURE IF EXISTS sp_egresos_por_estado;
DELIMITER //
CREATE PROCEDURE sp_egresos_por_estado(IN requiereComprobante_ VARCHAR(10))
BEGIN

    SELECT 
    eg.idegreso,
    DATE_FORMAT(eg.creado, '%d/%m/%Y') AS fecha,
    ce.concepto,
    CONCAT(p.apellidos,' ',' ',p.nombres) AS solicitante,
    eg.monto,
    eg.comentario
    
    FROM egresos eg
    JOIN  conceptoegreso ce ON eg.idconceptoegreso = ce.idconceptoegreso
    JOIN colaboradores c ON eg.idcolsolicitante = c.idcolaborador
    JOIN contratoslaborales cl ON c.idcontratolaboral = cl.idcontratolaboral
    JOIN personas p ON cl.idpersona = p.idpersona    
    WHERE requierecomprobante = requiereComprobante_;

END 

DELIMITER ;

SELECT * FROM contratoslaborales;


call sp_egresos_por_estado('N');


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

SELECT * FROM egresos;

DELIMITER //
CREATE PROCEDURE sp_egresos_add_comprobante(
    IN idegreso_ INT,
    IN idproovedor_ INT,
    IN tipodocumento_ VARCHAR(20),
    IN serie_ VARCHAR(50),
    IN numdocumento_ VARCHAR(50),
    IN monto_ DECIMAL(10,2),
    IN rutacomprobante_ VARCHAR(255)
)
BEGIN

    INSERT INTO comprobantes (idegreso,idproovedor,tipodocumento, serie, numdocumento, monto, cargadocontabilidad, rutacomprobante)
    VALUES (idegreso_, idproovedor_,tipodocumento_, serie_, numdocumento_, monto_, 'N', rutacomprobante_);


    SELECT LAST_INSERT_ID() AS last_insert_id;

END
DELIMITER;




SELECT * FROM comprobantes;