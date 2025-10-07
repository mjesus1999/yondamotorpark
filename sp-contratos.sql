
USE MOTORPARK;

SELECT * FROM cronogramas;
SELECT * FROM cotizaciones;
SELECT * FROM fichasolicitud;
SELECT * FROM cotizaciones WHERE estadocotizacion = 'O';
UPDATE cotizaciones SET estadocotizacion = 'A' WHERE idcotizacion =27;

SELECT * FROM cotizaciones;





CREATE TABLE conceptospago(
idconcepto    INT PRIMARY KEY AUTO_INCREMENT,
idcolregistra INT NOT NULL,
idcolactualiza  INT NULL,
concepto     VARCHAR(150) NOT NULL,
descripcion   TEXT NULL,
montosugerido DECIMAL(10,2)  NULL,
)ENGINE=INNODB;


CREATE TABLE pagosvarios(
idpagovario   INT PRIMARY KEY AUTO_INCREMENT,
idcotizacion  INT NOT NULL,
idconcepto    INT NOT NULL,
idcuentapago  INT NOT NULL,
montofinal    DECIMAL(10,2) NOT NULL,
fechapago     DATE NOT NULL,
)ENGINE=INNODB;



SELECT * FROM empresas;

SELECT
                            c.idcliente,
                            e.idempresa,
                            CONCAT(dep.departamento, ' / ', p.provincia, ' / ', d.distrito) AS ubicacion,
                            e.direccion,
                            e.representante AS responsable,
                            e.ruc,
                            e.nombrecomercial,
                            e.telprimario,
                            e.email,
                            e.estado
                        FROM clientes c
                        INNER JOIN empresas e ON c.idempresa = e.idempresa
                        INNER JOIN distritos d ON e.iddistrito = d.iddistrito
                        INNER JOIN provincias p ON d.idprovincia = p.idprovincia
                        INNER JOIN departamentos dep ON p.iddepartamento = dep.iddepartamento
                        WHERE c.tipocliente = 'E'  AND c.estado = 'ACT'
                        ORDER BY c.idcliente DESC;