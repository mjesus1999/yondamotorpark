USE motorpark;
SET GLOBAL event_scheduler = ON;

SHOW VARIABLES LIKE 'event_scheduler';


DROP EVENT IF EXISTS ev_actualizar_vencimiento_y_penalidad;


-- Se jecuta cada dia
DELIMITER $$

CREATE EVENT ev_actualizar_vencimiento_y_penalidad
ON SCHEDULE EVERY 1 DAY
STARTS NOW()
DO
BEGIN
    -- Paso 1: marcar como Vencido las cuotas pendientes cuya fecha ya pasó
    UPDATE cronogramas
    SET estado = 'Vencido'
    WHERE estado = 'Pendiente'
    AND fechapago < CURDATE();

    -- Paso 2: aplicar penalidad acumulativa después de 3 días de vencido y que no tenga penalidad aplicada
    UPDATE cronogramas
    SET 
        penalidad = penalidad + 300,
        aplicapenalidad = 'S'
    WHERE estado = 'Vencido'
    AND (aplicapenalidad = 'N' OR aplicapenalidad IS NULL)
    AND fechapago < CURDATE() - INTERVAL 3 DAY;
END$$

DELIMITER ;




SHOW EVENTS

DROP EVENT ev_actualizar_penalidad_cronogramas

SHOW EVENTS FROM motorpark;

SHOW EVENTS FROM motorpark;




SELECT *
FROM cronogramas
WHERE estado = 'Pendiente'
  AND fechapago < CURDATE();






SELECT 
    EVENT_NAME, LAST_EXECUTED, STATUS, SQL_MODE
FROM INFORMATION_SCHEMA.EVENTS
WHERE EVENT_NAME = 'ev_actualizar_vencimiento_y_penalidad';


SHOW GRANTS FOR CURRENT_USER;

GRANT EVENT ON *.* TO 'root'@'localhost';
