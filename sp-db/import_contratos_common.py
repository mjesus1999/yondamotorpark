# -*- coding: utf-8 -*-
"""Genera SQL de importación desde TSV (mismo layout para cada mes)."""
import csv
import os
import re
from datetime import date, datetime, timedelta

CUOTA_COLS = [
    (24, 25, 26, 27, 28),
    (29, 30, 32, 31, 33),
    (34, 35, 37, 36, 38),
    (39, 40, 42, 41, 43),
    (44, 45, 47, 46, None),
]
MAS_DEUDAS_IDX = 48
WIDE_CUOTA_START = 25
WIDE_CUOTA_BLOCK = 9
WIDE_NUM_CUOTAS = 4

SIMPLIFIED_HEADER = (
    "ID DEL CONTRATO\tFECHA DE LA FIRMA\tDNI\tNOMBRE\tDIRECCION\tCIUDAD\tCELULAR\t"
    "CARACTERISTICAS\tCOLOR\tCHASIS\tMOTOR\tPLACA\tINICIAL\tVALOR\tTIPO DE CONTRATO\t"
    "FECHA DE COMIENZO\tFECHA DE VENCIMIENTO\tVENDEDOR\tCONDICION\t"
    "DURACIÓN DEL CONTRATO (MESES)\tCUOTA POR MES\t% DE MORA\t"
    "MORA PASANDO LOS 3 DIAS SE APLICA\tTOTAL MONTO A PAGAR CON MORA\t"
    "CUOTA 1\tFECHA EN LA QUE PAGO EL CLIENTE\tGPS\tMORA\tDEUDAS PENDIENTES\t"
    "Cuota 2\tFECHA EN LA QUE PAGO EL CLIENTE\tMORA\tGPS\tDEUDAS PENDIENTES\t"
    "Cuota 3\tFECHA EN LA QUE PAGO EL CLIENTE\tMORA\tGPS\tDEUDAS PENDIENTES\t"
    "Cuota 4\tFECHA EN LA QUE PAGO EL CLIENTE\tMORA\tGPS\tDEUDAS PENDIENTES\t"
    "Cuota 5\tFECHA EN LA QUE PAGO EL CLIENTE\tMORA\tGPS\tMAS DEDUDAS PENDIENTES"
)

MONTHS = {
    "enero": {
        "tsv": "enero_2026_datos.tsv",
        "sql": "import_contratos_enero_2026.sql",
        "table": "import_contratos_enero_2026",
        "label": "Enero",
        "anio": 2026,
    },
    "febrero": {
        "tsv": "febrero_2026_datos.tsv",
        "sql": "import_contratos_febrero_2026.sql",
        "table": "import_contratos_febrero_2026",
        "label": "Febrero",
        "anio": 2026,
    },
    "marzo": {
        "tsv": "marzo_2026_datos.tsv",
        "sql": "import_contratos_marzo_2026.sql",
        "table": "import_contratos_marzo_2026",
        "label": "Marzo",
        "anio": 2026,
    },
    "abril": {
        "tsv": "abril_2026_datos.tsv",
        "sql": "import_contratos_abril_2026.sql",
        "table": "import_contratos_abril_2026",
        "label": "Abril",
        "anio": 2026,
    },
    "mayo": {
        "tsv": "mayo_2026_datos.tsv",
        "sql": "import_contratos_mayo_2026.sql",
        "table": "import_contratos_mayo_2026",
        "label": "Mayo",
        "anio": 2026,
    },
}

INSERT_COLS = [
    "id_contrato", "fecha_firma", "dni", "cliente_nombre", "direccion", "ciudad", "celular",
    "caracteristicas", "color", "chasis", "motor", "placa", "monto_inicial", "monto_valor",
    "tipo_contrato", "fecha_comienzo", "fecha_vencimiento", "vendedor", "condicion", "duracion_meses",
    "cuota_mensual", "pct_mora", "mora_monto", "total_con_mora",
    "cuota_1", "fecha_pago_1", "gps_1", "mora_1", "deudas_1",
    "cuota_2", "fecha_pago_2", "mora_2", "gps_2", "deudas_2",
    "cuota_3", "fecha_pago_3", "mora_3", "gps_3", "deudas_3",
    "cuota_4", "fecha_pago_4", "mora_4", "gps_4", "deudas_4",
    "cuota_5", "fecha_pago_5", "mora_5", "gps_5",
    "mas_deudas_pendientes", "anio_fuente",
]


def sql_escape(s: str) -> str:
    return s.replace("\\", "\\\\").replace("'", "''")


def normalize_id_contrato(s: str) -> str:
    s = str(s).strip()
    m = re.match(r"(?i)^(Mar)-(\d+)-(\d{4})$", s)
    if m:
        yr = int(m.group(3))
        if 2064 <= yr <= 2099:
            seq = yr - 2063
            return f"Mar-{seq:03d}-2026"
    m = re.match(r"(?i)^(Abr)-(\d+)-(\d{4})$", s)
    if m:
        yr = int(m.group(3))
        if 2097 <= yr <= 2118:
            seq = yr - 2096
            return f"Abr-{seq:03d}-2026"
    m = re.match(r"(?i)^(May)-(\d+)-(\d{4})$", s)
    if m:
        yr = int(m.group(3))
        if 2119 <= yr <= 2149:
            seq = yr - 2118
            return f"May-{seq:03d}-2026"
    s = re.sub(r"(?i)^(Feb-\d+)-206[0-9]$", r"\1-2026", s)
    m = re.match(r"(?i)^(Ene)-(\d+)-2024$", s)
    if m:
        return f"Ene-{m.group(2)}-2026"
    return s


def is_wide_header(header: list) -> bool:
    joined = " ".join((c or "") for c in header).upper()
    return "MES EN EL QUE PAGO" in joined or "LO QUE DEBE PAGAR" in joined


def _merge_cuota_extras(parts: list, off: int, deudas: str) -> str:
    extras = []
    mes = cell(parts, off + 2)
    num = cell(parts, off + 3)
    tiene = cell(parts, off + 6)
    pago_mora = cell(parts, off + 7)
    if mes:
        extras.append("Mes:" + mes)
    if num:
        extras.append("Nº:" + num)
    if tiene and tiene.upper() not in ("-", "NO", "NULL"):
        extras.append("Tiene mora:" + tiene)
    if pago_mora and pago_mora.upper() not in ("-", "NO", "NULL"):
        extras.append("Pago mora:" + pago_mora)
    if not extras:
        return deudas
    suffix = " | ".join(extras)
    if not deudas or deudas.upper() == "NULL":
        return suffix
    return deudas + " | " + suffix


def wide_row_to_simplified(parts: list) -> list:
    """Excel ancho (4 cuotas × 9 cols) → TSV de 49 columnas para el generador SQL."""
    out = [cell(parts, i) for i in range(14)]
    out.append(cell(parts, 15))
    for i in range(16, 25):
        out.append(cell(parts, i))

    for i in range(WIDE_NUM_CUOTAS):
        off = WIDE_CUOTA_START + i * WIDE_CUOTA_BLOCK
        cuota = cell(parts, off)
        fecha = cell(parts, off + 1)
        gps = cell(parts, off + 4)
        mora = cell(parts, off + 5)
        deudas = _merge_cuota_extras(parts, off, cell(parts, off + 8))
        if i == 0:
            out.extend([cuota, fecha, gps, mora, deudas])
        else:
            out.extend([cuota, fecha, mora, gps, deudas])

    out.extend(["", "", "", ""])
    deuda_fin = cell(parts, 14)
    mas = ""
    if deuda_fin:
        mas = "Debe cliente: " + deuda_fin
    out.append(mas)
    return out


def read_import_tsv(tsv_path: str):
    with open(tsv_path, encoding="utf-8") as f:
        reader = csv.reader(f, delimiter="\t")
        header = next(reader)
        rows = [p for p in reader if p and any((c or "").strip() for c in p)]
    wide = is_wide_header(header)
    if wide:
        rows = [wide_row_to_simplified(p) for p in rows]
        header = SIMPLIFIED_HEADER.split("\t")
    return header, rows, wide


def write_simplified_tsv(tsv_path: str, header: list, rows: list, *, from_wide: bool = False) -> None:
    """Escribe TSV de 49 columnas. Solo convierte con wide_row_to_simplified si from_wide=True."""
    ncol = len(SIMPLIFIED_HEADER.split("\t"))
    out_header = header if header and len(header) >= ncol - 5 else SIMPLIFIED_HEADER.split("\t")
    with open(tsv_path, "w", encoding="utf-8", newline="") as f:
        w = csv.writer(f, delimiter="\t", lineterminator="\n")
        w.writerow(out_header)
        for parts in rows:
            if from_wide:
                row = wide_row_to_simplified(parts)
            else:
                row = list(parts)
                if len(row) < ncol:
                    row.extend([""] * (ncol - len(row)))
                row = row[:ncol]
            w.writerow(row)


def parse_money(s: str):
    if s is None:
        return None
    s = str(s).strip().strip('"').strip("'")
    if s == "" or s.upper() in ("NULL", "N/A", "-", "---", "#VALUE!"):
        return None
    s = re.sub(r"(?i)s/?\.?\s*", "", s)
    s = s.replace(" ", "")
    # Excel: 348.722 → 348.72 (antes del patrón de miles)
    if re.match(r"^\d{1,4}\.\d{3}$", s):
        a, b = s.split(".", 1)
        s = f"{a}.{b[:2]}"
    # Perú: 3.000,00 (miles con punto, decimales con coma) o 10,700.00 (estilo US)
    elif re.match(r"^\d{1,3}(\.\d{3})+(,\d+)?$", s):
        s = s.replace(".", "").replace(",", ".")
    elif re.match(r"^\d{1,3}(,\d{3})+(\.\d+)?$", s):
        s = s.replace(",", "")
    elif re.match(r"^\d{1,6},\d{1,2}$", s):
        s = s.replace(",", ".")
    else:
        s = s.replace(",", "")
    low = s.lower()
    if low.startswith("por pagar") or "falta pagar" in low or "por revisar" in low:
        return None
    if re.fullmatch(r"144191\.?\d*", s):
        return 1441.91
    try:
        return float(s)
    except ValueError:
        return None


def parse_pct(s: str):
    if not s or not str(s).strip():
        return 10.0
    s = str(s).strip().replace(",", ".")
    if "%" in s:
        s = s.replace("%", "")
        try:
            return float(s)
        except ValueError:
            return 10.0
    try:
        v = float(s)
        if 0 < v <= 1:
            return v * 100.0
        return v
    except ValueError:
        return 10.0


def parse_int(s: str):
    if not s or not str(s).strip() or str(s).strip().upper() == "NULL":
        return None
    try:
        return int(float(str(s).strip()))
    except ValueError:
        return None


def parse_dni(s: str):
    if not s or not str(s).strip():
        return None
    s = str(s).strip()
    if re.match(r"^-?\d+(\.\d+)?$", s):
        try:
            n = int(float(s))
            if n <= 0:
                return None
            s = str(n)
            if len(s) > 8:
                s = s[-8:]
            return s
        except ValueError:
            pass
    m = re.sub(r"[^\d]", "", s)
    if len(m) > 8:
        m = m[-8:]
    return m if m else None


def excel_serial_to_date(n: int) -> str:
    base = date(1899, 12, 30)
    return (base + timedelta(days=n)).isoformat()


def normalize_date_string(s: str) -> str:
    s = s.strip()
    s = re.sub(r"(\d{1,2})-(\d{1,2})\.(\d{4})", r"\1-\2-\3", s)
    m = re.match(r"^(\d{1,2})-(\d{1,2})-(\d{4,5})$", s)
    if m and len(m.group(3)) > 4:
        s = f"{m.group(1)}-{m.group(2)}-{m.group(3)[:4]}"
    return s


def parse_date_field(s: str):
    s = normalize_date_string((s or "").strip().strip('"').strip("'"))
    if not s or s.upper() == "NULL":
        return None
    if re.fullmatch(r"\d{5,6}", s):
        return excel_serial_to_date(int(s))
    for fmt in (
        "%d-%m-%Y",
        "%d/%m/%Y",
        "%d-%m-%y",
        "%d/%m/%y",
        "%Y-%m-%d",
        "%Y/%m/%d",
        "%m/%d/%Y",
        "%m-%d-%Y",
    ):
        try:
            return datetime.strptime(s, fmt).date().isoformat()
        except ValueError:
            continue
    m = re.match(r"^(\d{1,2})[/-](\d{1,2})[/-](\d{4})$", s)
    if m:
        a, b, y = int(m.group(1)), int(m.group(2)), int(m.group(3))
        for d, mo in ((a, b), (b, a)):
            try:
                return date(y, mo, d).isoformat()
            except ValueError:
                continue
    return None


def cell(parts, i, default=""):
    if i is None or i >= len(parts):
        return default
    return parts[i].strip()


def Q(x):
    if x is None:
        return "NULL"
    s = str(x).strip()
    if s == "" or s.upper() == "NULL":
        return "NULL"
    return "'" + sql_escape(s) + "'"


def N(x):
    if x is None:
        return "NULL"
    if isinstance(x, float):
        return str(round(x, 4))
    return str(x)


def D(x):
    return "NULL" if x is None else "'" + x + "'"


def T(x):
    if x is None:
        return "NULL"
    s = str(x).strip()
    if s == "" or s.upper() == "NULL":
        return "NULL"
    return "'" + sql_escape(s) + "'"


def create_table_sql(table: str) -> str:
    return f"""CREATE TABLE {table} (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_contrato VARCHAR(50) NOT NULL,
  fecha_firma DATE NULL,
  dni VARCHAR(20) NULL,
  cliente_nombre VARCHAR(255) NULL,
  direccion TEXT NULL,
  ciudad VARCHAR(100) NULL,
  celular VARCHAR(50) NULL,
  caracteristicas TEXT NULL,
  color VARCHAR(80) NULL,
  chasis VARCHAR(100) NULL,
  motor VARCHAR(100) NULL,
  placa VARCHAR(50) NULL,
  monto_inicial DECIMAL(15,2) NULL,
  monto_valor DECIMAL(15,2) NULL,
  tipo_contrato VARCHAR(100) NULL,
  fecha_comienzo DATE NULL,
  fecha_vencimiento DATE NULL,
  vendedor VARCHAR(255) NULL,
  condicion VARCHAR(100) NULL,
  duracion_meses INT NULL,
  cuota_mensual DECIMAL(15,2) NULL,
  pct_mora DECIMAL(10,4) NULL,
  mora_monto DECIMAL(15,2) NULL,
  total_con_mora DECIMAL(15,2) NULL,
  cuota_1 VARCHAR(255) NULL, fecha_pago_1 DATE NULL, gps_1 VARCHAR(120) NULL, mora_1 VARCHAR(120) NULL, deudas_1 VARCHAR(255) NULL,
  cuota_2 VARCHAR(255) NULL, fecha_pago_2 DATE NULL, mora_2 VARCHAR(120) NULL, gps_2 VARCHAR(120) NULL, deudas_2 VARCHAR(255) NULL,
  cuota_3 VARCHAR(255) NULL, fecha_pago_3 DATE NULL, mora_3 VARCHAR(120) NULL, gps_3 VARCHAR(120) NULL, deudas_3 VARCHAR(255) NULL,
  cuota_4 VARCHAR(255) NULL, fecha_pago_4 DATE NULL, mora_4 VARCHAR(120) NULL, gps_4 VARCHAR(120) NULL, deudas_4 VARCHAR(255) NULL,
  cuota_5 VARCHAR(255) NULL, fecha_pago_5 DATE NULL, mora_5 VARCHAR(120) NULL, gps_5 VARCHAR(120) NULL,
  mas_deudas_pendientes VARCHAR(500) NULL,
  anio_fuente INT NOT NULL DEFAULT 2026,
  creado TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY idx_import_chasis (chasis(20)),
  KEY idx_import_dni (dni),
  KEY idx_import_id_contrato (id_contrato)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;"""


def row_to_values(parts, anio: int) -> list:
    fc_raw = cell(parts, 15)
    fv_raw = cell(parts, 16)
    fc = parse_date_field(fc_raw)
    fv = parse_date_field(fv_raw)
    if fc_raw.isdigit() and len(fc_raw) >= 5:
        fc = excel_serial_to_date(int(fc_raw))
    if fv_raw.isdigit() and len(fv_raw) >= 5:
        fv = excel_serial_to_date(int(fv_raw))

    row = [
        Q(normalize_id_contrato(cell(parts, 0))),
        D(parse_date_field(cell(parts, 1))),
        Q(parse_dni(cell(parts, 2))),
        Q(cell(parts, 3)),
        Q(cell(parts, 4)),
        Q(cell(parts, 5)),
        Q(cell(parts, 6)),
        Q(cell(parts, 7)),
        Q(cell(parts, 8)),
        Q(cell(parts, 9) or None),
        Q(cell(parts, 10) or None),
        Q(cell(parts, 11) or None),
        N(parse_money(cell(parts, 12))),
        N(parse_money(cell(parts, 13))),
        Q(cell(parts, 14) or None),
        D(fc),
        D(fv),
        Q(cell(parts, 17) or None),
        Q(cell(parts, 18) or None),
        N(parse_int(cell(parts, 19))),
        N(parse_money(cell(parts, 20))),
        str(round(parse_pct(cell(parts, 21)), 4)),
        N(parse_money(cell(parts, 22))),
        N(parse_money(cell(parts, 23))),
    ]

    for n, (ic, ife, ig, im, idb) in enumerate(CUOTA_COLS, start=1):
        row.append(T(cell(parts, ic) or None))
        row.append(D(parse_date_field(cell(parts, ife))))
        if n == 1:
            row.append(T(cell(parts, ig) or None))
            row.append(T(cell(parts, im) or None))
            row.append(T(cell(parts, idb) or None) if idb is not None else "NULL")
        else:
            row.append(T(cell(parts, im) or None))
            row.append(T(cell(parts, ig) or None))
            if idb is not None:
                row.append(T(cell(parts, idb) or None))

    row.append(T(cell(parts, MAS_DEUDAS_IDX) or None))
    row.append(str(anio))
    return row


def generate(mes_key: str) -> int:
    cfg = MONTHS[mes_key]
    base = os.path.dirname(os.path.abspath(__file__))
    tsv_path = os.path.join(base, cfg["tsv"])
    out_path = os.path.join(base, cfg["sql"])
    table = cfg["table"]
    anio = cfg["anio"]
    label = cfg["label"]

    _, data_rows, _ = read_import_tsv(tsv_path)

    vals = ["(" + ",".join(row_to_values(parts, anio)) + ")" for parts in data_rows]

    out = [
        "SET NAMES utf8mb4;",
        "SET CHARACTER SET utf8mb4;",
        "",
        f"-- {label} {anio}: contratos + cuotas 1-5.",
        "-- USE u322322994_motorpark;",
        "",
        f"DROP TABLE IF EXISTS {table};",
        create_table_sql(table),
        "",
    ]
    if vals:
        out.append(
            f"INSERT INTO {table} (\n  "
            + ", ".join(INSERT_COLS)
            + "\n) VALUES\n"
            + ",\n".join(vals)
            + ";"
        )
    else:
        out.append(f"-- Sin filas en {cfg['tsv']}; pegar datos y volver a ejecutar el generador.")
    out.extend(["", f"SELECT COUNT(*) AS filas_importadas FROM {table};"])

    with open(out_path, "w", encoding="utf-8") as wf:
        wf.write("\n".join(out))
    print("Wrote", out_path, "rows", len(vals))
    return len(vals)
