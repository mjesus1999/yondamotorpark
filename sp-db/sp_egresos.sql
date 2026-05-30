-- USE motorpark; -- hosting: seleccionar BD en phpMyAdmin

DROP PROCEDURE IF EXISTS sp_egresos_por_estado;
DROP PROCEDURE IF EXISTS sp_egresos_add;
DROP PROCEDURE IF EXISTS sp_egresos_add_comprobante;
DROP PROCEDURE IF EXISTS sp_egresos_validados;
DROP PROCEDURE IF EXISTS sp_obtener_reporte_egresos_completo;
DROP PROCEDURE IF EXISTS sp_obtener_egresos_diarios_por_concepto_hoy;

DELIMITER $$

CREATE PROCEDURE sp_egresos_por_estado(IN requiereComprobante_ VARCHAR(10))
BEGIN
  IF requiereComprobante_ = 'S' THEN
    SELECT
      eg.idegreso,
      DATE_FORMAT(eg.creado, '%d/%m/%Y') AS fecha,
      ce.concepto,
      COALESCE(NULLIF(eg.solicitante_nombre, ''), CONCAT(p.apellidos, ' ', p.nombres)) AS solicitante,
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
      COALESCE(NULLIF(eg.solicitante_nombre, ''), CONCAT(p.apellidos, ' ', p.nombres)) AS solicitante,
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
END$$


CREATE PROCEDURE sp_egresos_add(
    IN idconceptoegreso_ INT,
    IN idcolacaja_ INT,
    IN idcolsolicitante_ INT,
    IN monto_ DECIMAL(10,2),
    IN comentario_ VARCHAR(255),
    IN requierecomprobante_ CHAR(1)
)
BEGIN
    INSERT INTO egresos (idconceptoegreso, idcolacaja, idcolsolicitante, monto, comentario, requierecomprobante, fecha)
    VALUES (idconceptoegreso_, idcolacaja_, idcolsolicitante_, monto_, comentario_, requierecomprobante_, CURDATE());

    SELECT LAST_INSERT_ID() AS last_insert_id;
END$$


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
    INSERT INTO comprobantes (idegreso, idproovedor, tipodoc, serie, numdocumento, monto, cargadocontabilidad, rutacomprobante)
    VALUES (idegreso_, idproovedor_, tipodoc_, serie_, numdocumento_, monto_, 'N', rutacomprobante_);

    SELECT LAST_INSERT_ID() AS last_insert_id;
END$$


CREATE PROCEDURE sp_egresos_validados()
BEGIN
    SELECT
        eg.idegreso,
        DATE_FORMAT(eg.creado, '%d/%m/%Y') AS fecha,
        ce.concepto,
        COALESCE(NULLIF(eg.solicitante_nombre, ''), CONCAT(p.apellidos,' ',p.nombres)) AS solicitante,
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
END$$


CREATE PROCEDURE sp_obtener_reporte_egresos_completo(IN fecha_inicio DATE, IN fecha_fin DATE)
BEGIN
    SELECT
        e.idegreso,
        ce.concepto,
        e.monto,
        e.comentario,
        e.creado,
        CONCAT(perreg.apellidos, ' ', perreg.nombres) AS registrador,
        COALESCE(NULLIF(e.solicitante_nombre, ''), CONCAT(persol.apellidos, ' ', persol.nombres)) AS solicitante
    FROM egresos AS e
    JOIN conceptoegreso AS ce ON e.idconceptoegreso = ce.idconceptoegreso
    JOIN colaboradores AS registrador ON e.idcolacaja = registrador.idcolaborador
    JOIN colaboradores AS solicitante ON e.idcolsolicitante = solicitante.idcolaborador
    JOIN contratoslaborales AS contlre ON registrador.idcontratolaboral = contlre.idcontratolaboral
    JOIN contratoslaborales AS contlsol ON solicitante.idcontratolaboral = contlsol.idcontratolaboral
    JOIN personas AS perreg ON contlre.idpersona = perreg.idpersona
    JOIN personas AS persol ON contlsol.idpersona = persol.idpersona
    WHERE e.requierecomprobante = 'N'
      AND e.creado >= fecha_inicio
      AND e.creado < DATE_ADD(fecha_fin, INTERVAL 1 DAY)
    ORDER BY e.creado DESC;

    SELECT
        e.idegreso,
        ce.concepto,
        e.monto,
        e.comentario,
        e.creado,
        com.modificado,
        CONCAT(perreg.apellidos, ' ', perreg.nombres) AS registrador,
        COALESCE(NULLIF(e.solicitante_nombre, ''), CONCAT(persol.apellidos, ' ', persol.nombres)) AS solicitante,
        com.cargadocontabilidad AS estado_comprobante,
        com.monto AS monto_comprobante
    FROM egresos AS e
    JOIN conceptoegreso AS ce ON e.idconceptoegreso = ce.idconceptoegreso
    JOIN comprobantes AS com ON e.idegreso = com.idegreso
    JOIN colaboradores AS registrador ON e.idcolacaja = registrador.idcolaborador
    JOIN colaboradores AS solicitante ON e.idcolsolicitante = solicitante.idcolaborador
    JOIN contratoslaborales AS contlre ON registrador.idcontratolaboral = contlre.idcontratolaboral
    JOIN contratoslaborales AS contlsol ON solicitante.idcontratolaboral = contlsol.idcontratolaboral
    JOIN personas AS perreg ON contlre.idpersona = perreg.idpersona
    JOIN personas AS persol ON contlsol.idpersona = persol.idpersona
    WHERE e.requierecomprobante = 'S'
      AND com.cargadocontabilidad = 'S'
      AND e.creado >= fecha_inicio
      AND e.creado < DATE_ADD(fecha_fin, INTERVAL 1 DAY)
    ORDER BY e.creado DESC;

    SELECT
        ce.concepto,
        SUM(e.monto) AS total_por_concepto
    FROM egresos AS e
    JOIN conceptoegreso AS ce ON e.idconceptoegreso = ce.idconceptoegreso
    LEFT JOIN comprobantes AS com ON e.idegreso = com.idegreso
    WHERE (e.requierecomprobante = 'N' OR (e.requierecomprobante = 'S' AND com.cargadocontabilidad = 'S'))
      AND e.creado >= fecha_inicio
      AND e.creado < DATE_ADD(fecha_fin, INTERVAL 1 DAY)
    GROUP BY ce.concepto
    ORDER BY total_por_concepto DESC;

    SELECT
        SUM(CASE WHEN e.requierecomprobante = 'N' THEN e.monto ELSE 0 END) AS total_sin_comprobante,
        SUM(CASE WHEN e.requierecomprobante = 'S' AND com.cargadocontabilidad = 'S' THEN e.monto ELSE 0 END) AS total_con_comprobante_cargado,
        SUM(CASE WHEN e.requierecomprobante = 'N' OR (e.requierecomprobante = 'S' AND com.cargadocontabilidad = 'S') THEN e.monto ELSE 0 END) AS gran_total
    FROM egresos AS e
    LEFT JOIN comprobantes AS com ON e.idegreso = com.idegreso
    WHERE e.creado >= fecha_inicio
      AND e.creado < DATE_ADD(fecha_fin, INTERVAL 1 DAY);
END$$


CREATE PROCEDURE sp_obtener_egresos_diarios_por_concepto_hoy()
BEGIN
  SELECT
    ce.concepto,
    SUM(e.monto) AS total_por_concepto
  FROM egresos AS e
  JOIN conceptoegreso AS ce ON e.idconceptoegreso = ce.idconceptoegreso
  LEFT JOIN comprobantes AS com ON e.idegreso = com.idegreso
  WHERE (e.requierecomprobante = 'N' OR (e.requierecomprobante = 'S' AND com.cargadocontabilidad = 'S'))
    AND DATE(e.creado) = CURDATE()
  GROUP BY ce.concepto
  ORDER BY total_por_concepto DESC;
END$$

DELIMITER ;