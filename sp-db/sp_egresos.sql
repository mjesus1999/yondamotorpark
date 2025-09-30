
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

    INSERT INTO egresos (idconceptoegreso, idcolacaja, idcolsolicitante, monto, comentario, requierecomprobante,fecha)
    VALUES (idconceptoegreso_, idcolacaja_, idcolsolicitante_, monto_, comentario_, requierecomprobante_,CURDATE());

    SELECT LAST_INSERT_ID() AS last_insert_id;


END

DELIMITER;

SELECT * FROM egresos;

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






drop procedure  sp_obtener_reporte_egresos_completo;



DELIMITER //

CREATE PROCEDURE sp_obtener_reporte_egresos_completo(
    IN fecha_inicio DATE,
    IN fecha_fin DATE
)
BEGIN
    -- Resultado 1: Detalle de egresos que NO requieren comprobante ('N')
    SELECT
        e.idegreso,
        ce.concepto,
        e.monto,
        e.comentario,
        e.creado,
        CONCAT(perreg.apellidos, ' ', perreg.nombres) AS registrador,
        CONCAT(persol.apellidos, ' ', persol.nombres) AS solicitante
    FROM
        egresos AS e
    JOIN
        conceptoegreso AS ce ON e.idconceptoegreso = ce.idconceptoegreso
    JOIN
        colaboradores AS registrador ON e.idcolacaja = registrador.idcolaborador
    JOIN
        colaboradores AS solicitante ON e.idcolsolicitante = solicitante.idcolaborador
    JOIN
        contratoslaborales AS contlre ON registrador.idcontratolaboral = contlre.idcontratolaboral
    JOIN
        contratoslaborales AS contlsol ON solicitante.idcontratolaboral = contlsol.idcontratolaboral
    JOIN
        personas AS perreg ON contlre.idpersona = perreg.idpersona
    JOIN
        personas AS persol ON contlsol.idpersona = persol.idpersona
    WHERE
        e.requierecomprobante = 'N'
        AND e.creado >= fecha_inicio
        AND e.creado < DATE_ADD(fecha_fin, INTERVAL 1 DAY)
    ORDER BY
        e.creado DESC;

    -- Resultado 2: Detalle de egresos que SÍ requieren comprobante y están cargados ('S')
    SELECT
        e.idegreso,
        ce.concepto,
        e.monto,
        e.comentario,
        e.creado,
        com.modificado,
        CONCAT(perreg.apellidos, ' ', perreg.nombres) AS registrador,
        CONCAT(persol.apellidos, ' ', persol.nombres) AS solicitante,
        com.cargadocontabilidad AS estado_comprobante,
        com.monto AS monto_comprobante
    FROM
        egresos AS e
    JOIN
        conceptoegreso AS ce ON e.idconceptoegreso = ce.idconceptoegreso
    JOIN
        comprobantes AS com ON e.idegreso = com.idegreso
    JOIN
        colaboradores AS registrador ON e.idcolacaja = registrador.idcolaborador
    JOIN
        colaboradores AS solicitante ON e.idcolsolicitante = solicitante.idcolaborador
    JOIN
        contratoslaborales AS contlre ON registrador.idcontratolaboral = contlre.idcontratolaboral
    JOIN
        contratoslaborales AS contlsol ON solicitante.idcontratolaboral = contlsol.idcontratolaboral
    JOIN
        personas AS perreg ON contlre.idpersona = perreg.idpersona
    JOIN
        personas AS persol ON contlsol.idpersona = persol.idpersona
    WHERE
        e.requierecomprobante = 'S' 
        AND com.cargadocontabilidad = 'S'
        AND e.creado >= fecha_inicio
        AND e.creado < DATE_ADD(fecha_fin, INTERVAL 1 DAY)
    ORDER BY
        e.creado DESC;

    -- Resultado 3: Resumen por concepto de los egresos válidos
    SELECT
        ce.concepto,
        SUM(e.monto) AS total_por_concepto
    FROM
        egresos AS e
    JOIN
        conceptoegreso AS ce ON e.idconceptoegreso = ce.idconceptoegreso
    LEFT JOIN
        comprobantes AS com ON e.idegreso = com.idegreso
    WHERE
        (e.requierecomprobante = 'N' OR (e.requierecomprobante = 'S' AND com.cargadocontabilidad = 'S'))
        AND e.creado >= fecha_inicio
        AND e.creado < DATE_ADD(fecha_fin, INTERVAL 1 DAY)
    GROUP BY
        ce.concepto
    ORDER BY
        total_por_concepto DESC;

    -- Resultado 4: Resumen consolidado de totales
    SELECT
        SUM(CASE WHEN e.requierecomprobante = 'N' THEN e.monto ELSE 0 END) AS total_sin_comprobante,
        SUM(CASE WHEN e.requierecomprobante = 'S' AND com.cargadocontabilidad = 'S' THEN e.monto ELSE 0 END) AS total_con_comprobante_cargado,
        SUM(CASE WHEN e.requierecomprobante = 'N' OR (e.requierecomprobante = 'S' AND com.cargadocontabilidad = 'S') THEN e.monto ELSE 0 END) AS gran_total
    FROM
        egresos AS e
    LEFT JOIN
        comprobantes AS com ON e.idegreso = com.idegreso
    WHERE
        e.creado >= fecha_inicio
        AND e.creado < DATE_ADD(fecha_fin, INTERVAL 1 DAY);
END//

DELIMITER ;

CALL sp_obtener_reporte_egresos_completo(CURDATE(), CURDATE());






DELIMITER //

CREATE PROCEDURE sp_obtener_egresos_diarios_por_concepto_hoy()
BEGIN
  SELECT
    ce.concepto,
    SUM(e.monto) AS total_por_concepto
  FROM
    egresos AS e
  JOIN
    conceptoegreso AS ce ON e.idconceptoegreso = ce.idconceptoegreso
  LEFT JOIN
    comprobantes AS com ON e.idegreso = com.idegreso
  WHERE
    (e.requierecomprobante = 'N' OR (e.requierecomprobante = 'S' AND com.cargadocontabilidad = 'S'))
    AND DATE(e.creado) = CURDATE()
  GROUP BY
    ce.concepto
  ORDER BY
    total_por_concepto DESC;
END//

DELIMITER ;

CALL sp_obtener_egresos_diarios_por_concepto_hoy



SELECT * from egresos;