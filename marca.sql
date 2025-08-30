  
  USE motorpark;
  SELECT
      MR.idmarca, MR.marca, COUNT(MD.idmodelo) 'modelos'
      FROM marcas MR
        LEFT JOIN modelos MD ON MD.idmarca = MR.idmarca
        GROUP BY MR.idmarca, MR.marca;

SELECT * FROM marcas;

SELECT * FROM cronogramas;

SELECT * FROM pagos;

SELECT * FROM contratos;


SELECT * FROM cronogramas;