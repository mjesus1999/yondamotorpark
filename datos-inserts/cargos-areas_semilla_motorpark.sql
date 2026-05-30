-- BD seleccionada en phpMyAdmin (ej. u322322994_motorpark).
-- Catálogo mínimo de áreas y cargos con IDs alineados al dump motorpark de referencia.
-- Ejecutar ANTES de accesos-usuarios.sql si tu tabla `cargos` está vacía o sin estos ids.
-- INSERT IGNORE: si ya existe la PK idarea/idcargo, no sobrescribe.

INSERT IGNORE INTO areas (idarea, area) VALUES
    (1, 'Sistemas'),
    (2, 'Recursos Humanos'),
    (3, 'Contabilidad'),
    (4, 'Marketing'),
    (5, 'Ventas'),
    (6, 'Caja'),
    (7, 'Cobranza'),
    (8, 'Legal'),
    (9, 'Logística');

INSERT IGNORE INTO cargos (idcargo, idarea, cargo) VALUES
    (1, 1, 'Jefe de sistemas'),
    (2, 1, 'Analista desarrollador'),
    (3, 1, 'Practicante'),
    (8, 9, 'Jefe de Logística'),
    (9, 9, 'Asistente de Logística'),
    (10, 2, 'Jefe de Recursos Humanos'),
    (13, 3, 'Jefe de Contabilidad'),
    (16, 5, 'Jefe de Ventas'),
    (17, 6, 'Jefe de Caja'),
    (18, 5, 'Asesor de Ventas'),
    (19, 7, 'Jefe de Cobranza'),
    (20, 3, 'Analista de Contabilidad'),
    (21, 8, 'Asesor Legal');
