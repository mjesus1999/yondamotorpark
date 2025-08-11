USE motorpark;
USE motorpark;


DELIMITER //

CREATE PROCEDURE sp_get_cronogramas_by_idcontrato(IN idcontrato_ INT)
BEGIN

	SELECT 
    
    cro.idcronograma,
    
    
    FROM cronogramas cro
    INNER JOIN contratos cont ON cro.idcontrato = cont.idcontrato
    
    WHERE cont.idcontrato = idcontrato_;

END //

 CALL sp_get_cronogramas_by_idcontrato(1);