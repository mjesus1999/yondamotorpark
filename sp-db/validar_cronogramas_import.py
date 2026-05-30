# -*- coding: utf-8 -*-
"""
Reporte de validación cronograma — todos los imports enero–mayo 2026.
Genera CSV para revisar qué clientes necesitan corrección en el Excel/SQL.

Uso:
  python validar_cronogramas_import.py
  python validar_cronogramas_import.py --solo-alertas
"""
from __future__ import annotations

import argparse
import calendar
import csv
import os
import sys
from datetime import date, datetime

from import_contratos_common import (
    CUOTA_COLS,
    MONTHS,
    cell,
    normalize_id_contrato,
    parse_date_field,
    parse_dni,
    parse_int,
    parse_money,
    read_import_tsv,
)

BASE = os.path.dirname(os.path.abspath(__file__))
OUT_CSV = os.path.join(BASE, "reporte_validacion_cronogramas_2026.csv")
OUT_RESUMEN = os.path.join(BASE, "reporte_validacion_cronogramas_2026_resumen.txt")


def fecha_cronograma(inicio: date, num_cuota: int) -> date | None:
    if num_cuota < 1:
        return None
    meses = num_cuota - 1
    m0 = inicio.month - 1 + meses
    y = inicio.year + m0 // 12
    m = m0 % 12 + 1
    d = min(inicio.day, calendar.monthrange(y, m)[1])
    return date(y, m, d)


def cuotas_pago_desde_fila(parts: list) -> dict[int, str | None]:
    out: dict[int, str | None] = {}
    for n, (_ic, ife, _ig, _im, _idb) in enumerate(CUOTA_COLS, start=1):
        raw = cell(parts, ife)
        out[n] = parse_date_field(raw) if raw else None
    return out


def ultima_cuota_pagada(fechas: dict[int, str | None]) -> int:
    n = 0
    for i in range(1, 6):
        if fechas.get(i):
            n = i
    return n


def alertas_fila(
    parts: list,
    fc: str | None,
    plazo: int | None,
    cuota: float | None,
    monto_ini: float | None,
    monto_val: float | None,
    fechas: dict[int, str | None],
) -> list[str]:
    alerts: list[str] = []
    if not parse_dni(cell(parts, 2)):
        alerts.append("DNI_INVALIDO")
    if not fc:
        alerts.append("SIN_FECHA_COMIENZO")
    if not plazo or plazo <= 0:
        alerts.append("SIN_PLAZO")
    if not cuota or cuota <= 0:
        alerts.append("SIN_CUOTA_MENSUAL")
    monto_fin = max(0.0, (monto_val or 0) - (monto_ini or 0))
    if monto_fin <= 0 and (monto_val or 0) <= 0:
        alerts.append("SIN_MONTO_FINANCIAR")

    if not cell(parts, 9):
        alerts.append("SIN_CHASIS")

    pagadas = ultima_cuota_pagada(fechas)
    for i in range(1, 6):
        raw = cell(parts, CUOTA_COLS[i - 1][1])
        if raw and not fechas.get(i):
            alerts.append(f"FECHA_PAGO_{i}_NO_PARSEA")

    for i in range(2, pagadas + 1):
        if not fechas.get(i - 1):
            alerts.append(f"HUECO_CUOTA_{i - 1}")

    if fc:
        try:
            d_ini = datetime.strptime(fc, "%Y-%m-%d").date()
            for i, fp in fechas.items():
                if not fp:
                    continue
                d_p = datetime.strptime(fp, "%Y-%m-%d").date()
                if d_p < d_ini:
                    alerts.append(f"PAGO_C{i}_ANTES_INICIO")
        except ValueError:
            pass

        try:
            d_ini = datetime.strptime(fc, "%Y-%m-%d").date()
            fin = fecha_cronograma(d_ini, plazo or 0) if plazo else None
            if fin and fc:
                pass
        except ValueError:
            pass

    for n, (ic, _ife, _ig, _im, _idb) in enumerate(CUOTA_COLS, start=1):
        m = parse_money(cell(parts, ic))
        if m and cuota and m > cuota * 3:
            alerts.append(f"MONTO_CUOTA_{n}_SOSPECHOSO")

    if not alerts:
        alerts.append("OK")
    return alerts


def cargar_filas() -> list[dict]:
    filas: list[dict] = []
    for mes_key in ("enero", "febrero", "marzo", "abril", "mayo"):
        cfg = MONTHS[mes_key]
        tsv_path = os.path.join(BASE, cfg["tsv"])
        if not os.path.isfile(tsv_path):
            continue
        _h, rows, _w = read_import_tsv(tsv_path)
        for parts in rows:
            id_raw = cell(parts, 0)
            id_norm = normalize_id_contrato(id_raw)
            fc = parse_date_field(cell(parts, 15))
            plazo = parse_int(cell(parts, 19))
            cuota = parse_money(cell(parts, 20))
            m_ini = parse_money(cell(parts, 12))
            m_val = parse_money(cell(parts, 13))
            fechas = cuotas_pago_desde_fila(parts)
            pagadas = ultima_cuota_pagada(fechas)

            crono_ini = crono_fin = ""
            if fc:
                try:
                    d0 = datetime.strptime(fc, "%Y-%m-%d").date()
                    crono_ini = d0.isoformat()
                    if plazo and plazo > 0:
                        crono_fin = fecha_cronograma(d0, plazo).isoformat()
                except ValueError:
                    pass

            alerts = alertas_fila(parts, fc, plazo, cuota, m_ini, m_val, fechas)
            filas.append(
                {
                    "mes_import": cfg["label"],
                    "tabla": cfg["table"],
                    "id_contrato": id_norm,
                    "id_contrato_excel": id_raw,
                    "dni": parse_dni(cell(parts, 2)) or "",
                    "cliente": cell(parts, 3),
                    "chasis": cell(parts, 9),
                    "fecha_comienzo": fc or "",
                    "fecha_fin_cronograma": crono_fin,
                    "plazo_meses": plazo or "",
                    "cuota_mensual": f"{cuota:.2f}" if cuota else "",
                    "monto_financiar": f"{max(0, (m_val or 0) - (m_ini or 0)):.2f}",
                    "cuotas_pagadas": pagadas,
                    "fecha_pago_1": fechas.get(1) or "",
                    "fecha_pago_2": fechas.get(2) or "",
                    "fecha_pago_3": fechas.get(3) or "",
                    "fecha_pago_4": fechas.get(4) or "",
                    "fecha_pago_5": fechas.get(5) or "",
                    "alertas": ";".join(alerts),
                    "requiere_revision": "NO" if alerts == ["OK"] else "SI",
                }
            )
    return filas


def marcar_duplicados_chasis(filas: list[dict]) -> None:
    by_chasis: dict[str, list[int]] = {}
    for i, r in enumerate(filas):
        ch = (r.get("chasis") or "").strip().upper()
        if not ch:
            continue
        by_chasis.setdefault(ch, []).append(i)
    for _ch, idxs in by_chasis.items():
        if len(idxs) <= 1:
            continue
        for i in idxs:
            a = filas[i]["alertas"]
            if a == "OK":
                filas[i]["alertas"] = "DUPLICADO_CHASIS_OTRO_MES"
            elif "DUPLICADO_CHASIS_OTRO_MES" not in a:
                filas[i]["alertas"] = a + ";DUPLICADO_CHASIS_OTRO_MES"
            filas[i]["requiere_revision"] = "SI"


def escribir_csv(filas: list[dict], path: str) -> None:
    if not filas:
        return
    cols = list(filas[0].keys())
    with open(path, "w", encoding="utf-8-sig", newline="") as f:
        w = csv.DictWriter(f, fieldnames=cols, delimiter=";")
        w.writeheader()
        w.writerows(filas)


def escribir_resumen(filas: list[dict], path: str) -> None:
    total = len(filas)
    ok = sum(1 for r in filas if r["requiere_revision"] == "NO")
    rev = total - ok
    por_mes: dict[str, list[int]] = {}
    por_alerta: dict[str, int] = {}
    for r in filas:
        por_mes.setdefault(r["mes_import"], [0, 0])
        por_mes[r["mes_import"]][0] += 1
        if r["requiere_revision"] == "SI":
            por_mes[r["mes_import"]][1] += 1
        for a in r["alertas"].split(";"):
            if a and a != "OK":
                por_alerta[a] = por_alerta.get(a, 0) + 1

    lines = [
        "REPORTE VALIDACIÓN CRONOGRAMAS — imports 2026",
        f"Generado: {datetime.now().strftime('%Y-%m-%d %H:%M')}",
        "",
        f"Total contratos: {total}",
        f"OK (sin alertas): {ok}",
        f"Requieren revisión: {rev}",
        "",
        "Por mes (total / con alertas):",
    ]
    for mes in ("Enero", "Febrero", "Marzo", "Abril", "Mayo"):
        if mes in por_mes:
            t, a = por_mes[mes]
            lines.append(f"  {mes}: {t} contratos, {a} con alertas")

    lines.extend(["", "Alertas más frecuentes:"])
    for k, v in sorted(por_alerta.items(), key=lambda x: -x[1])[:20]:
        lines.append(f"  {k}: {v}")

    lines.extend(
        [
            "",
            f"CSV detalle: {OUT_CSV}",
            "Abrir en Excel (separador ;). Filtrar columna requiere_revision = SI",
        ]
    )
    with open(path, "w", encoding="utf-8") as f:
        f.write("\n".join(lines) + "\n")


def main() -> int:
    parser = argparse.ArgumentParser()
    parser.add_argument(
        "--solo-alertas",
        action="store_true",
        help="CSV solo con filas que requieren revisión",
    )
    args = parser.parse_args()

    filas = cargar_filas()
    marcar_duplicados_chasis(filas)

    escribir_csv(filas, OUT_CSV)
    escribir_resumen(filas, OUT_RESUMEN)

    solo_alertas = [r for r in filas if r["requiere_revision"] == "SI"]
    if solo_alertas:
        path_alertas = OUT_CSV.replace(".csv", "_solo_alertas.csv")
        escribir_csv(solo_alertas, path_alertas)

    total = len(filas)
    rev = len(solo_alertas)
    print(f"Contratos analizados: {total}")
    print(f"OK: {total - rev} | Requieren revision: {rev}")
    print(f"CSV completo: {OUT_CSV}")
    if solo_alertas:
        print(f"CSV solo alertas: {OUT_CSV.replace('.csv', '_solo_alertas.csv')}")
    print(f"Resumen: {OUT_RESUMEN}")
    return 0


if __name__ == "__main__":
    sys.exit(main())
