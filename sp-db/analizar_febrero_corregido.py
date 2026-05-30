# -*- coding: utf-8 -*-
"""Análisis febrero 2026 con mapeo de columnas corregido (falta TIPO DE CONTRATO en datos)."""
import calendar
import csv
import os
import sys
from collections import Counter
from datetime import date, datetime

from import_contratos_common import (
    CUOTA_COLS,
    cell,
    normalize_id_contrato,
    parse_date_field,
    parse_dni,
    parse_int,
    parse_money,
    read_import_tsv,
)

BASE = os.path.dirname(os.path.abspath(__file__))
OUT = os.path.join(BASE, "reporte_febrero_2026_corregido.csv")

# Febrero: columna TIPO DE CONTRATO ausente → todo desde índice 14 corre -1 vs layout estándar
IDX = {
    "fc": 14,
    "fv": 15,
    "vendedor": 16,
    "condicion": 17,
    "plazo": 18,
    "cuota_base": 19,
    "pct_mora": 20,
    "mora_monto": 21,
    "total_mora": 22,
}
CUOTA_FEB = [
    tuple(x - 1 if x is not None else None for x in tup) for tup in CUOTA_COLS
]


def fecha_cronograma(inicio: date, num_cuota: int) -> date | None:
    if num_cuota < 1:
        return None
    meses = num_cuota - 1
    m0 = inicio.month - 1 + meses
    y = inicio.year + m0 // 12
    m = m0 % 12 + 1
    d = min(inicio.day, calendar.monthrange(y, m)[1])
    return date(y, m, d)


def resolver_tasa(principal: float, cuota: float, plazo: int) -> float | None:
    """Misma lógica de redondeo que CronogramaAmortizacionHelper (francés)."""
    if principal <= 0 or plazo <= 0 or cuota <= 0:
        return None
    lo, hi = 0.0, 0.5
    for _ in range(100):
        mid = (lo + hi) / 2
        saldo = principal
        for i in range(1, plazo + 1):
            interes = round(saldo * mid, 2)
            abono = round(cuota - interes, 2)
            if i == plazo:
                abono = round(saldo, 2)
            saldo = round(saldo - abono, 2)
        if abs(saldo) < 0.02:
            return mid
        if saldo > 0:
            lo = mid
        else:
            hi = mid
    return None


def cuotas_pago(parts: list) -> dict[int, str | None]:
    out: dict[int, str | None] = {}
    for n, (_ic, ife, _ig, _im, _idb) in enumerate(CUOTA_FEB, start=1):
        raw = cell(parts, ife) if ife is not None else ""
        out[n] = parse_date_field(raw) if raw else None
    return out


def analizar() -> list[dict]:
    path = os.path.join(BASE, "febrero_2026_datos.tsv")
    _h, rows, _w = read_import_tsv(path)
    resultados: list[dict] = []

    for parts in rows:
        id_raw = cell(parts, 0)
        id_norm = normalize_id_contrato(id_raw)
        dni = parse_dni(cell(parts, 2)) or ""
        nombre = cell(parts, 3)
        chasis = cell(parts, 9)
        m_ini = parse_money(cell(parts, 12)) or 0.0
        m_val = parse_money(cell(parts, 13)) or 0.0
        m_fin = max(0.0, m_val - m_ini)
        fc = parse_date_field(cell(parts, IDX["fc"]))
        fv = parse_date_field(cell(parts, IDX["fv"]))
        plazo = parse_int(cell(parts, IDX["plazo"]))
        cuota_base = parse_money(cell(parts, IDX["cuota_base"]))
        total_mora = parse_money(cell(parts, IDX["total_mora"]))
        mora_monto = parse_money(cell(parts, IDX["mora_monto"]))
        # Cuota del cronograma francés = base; total con mora es lo que paga el cliente
        cuota_cronograma = cuota_base or total_mora
        vendedor = cell(parts, IDX["vendedor"])
        condicion = cell(parts, IDX["condicion"])

        fechas = cuotas_pago(parts)
        pagadas = max([i for i in range(1, 6) if fechas.get(i)] or [0])

        alerts: list[str] = []
        if not dni:
            alerts.append("DNI_INVALIDO")
        if not fc:
            alerts.append("SIN_FECHA_COMIENZO")
        if not plazo:
            alerts.append("SIN_PLAZO")
        if not cuota_cronograma:
            alerts.append("SIN_CUOTA")
        if m_fin <= 0:
            alerts.append("SIN_MONTO_FINANCIAR")
        if not chasis:
            alerts.append("SIN_CHASIS")

        for i in range(1, 6):
            ife = CUOTA_FEB[i - 1][1]
            raw = cell(parts, ife) if ife is not None else ""
            if raw and not fechas.get(i):
                alerts.append(f"FECHA_PAGO_{i}_NO_PARSEA")

        for i in range(2, pagadas + 1):
            if not fechas.get(i - 1):
                alerts.append(f"HUECO_CUOTA_{i - 1}")

        if fc:
            d_ini = datetime.strptime(fc, "%Y-%m-%d").date()
            for i, fp in fechas.items():
                if fp and datetime.strptime(fp, "%Y-%m-%d").date() < d_ini:
                    alerts.append(f"PAGO_C{i}_ANTES_INICIO")
            if plazo:
                fin_crono = fecha_cronograma(d_ini, plazo)
                if fv and fin_crono:
                    diff = abs((datetime.strptime(fv, "%Y-%m-%d").date() - fin_crono).days)
                    if diff > 35:
                        alerts.append("VENCIMIENTO_NO_COINCIDE_CRONO")

        tasa_m = tasa_anual = None
        if m_fin > 0 and plazo and cuota_cronograma:
            tasa_m = resolver_tasa(m_fin, cuota_cronograma, plazo)
            if tasa_m is None:
                alerts.append("TASA_NO_CALCULA")
            else:
                tasa_anual = ((1 + tasa_m) ** 12 - 1) * 100
                if tasa_m <= 0 or tasa_m > 0.08:
                    alerts.append("TASA_SOSPECHOSA")
                elif tasa_anual > 100:
                    alerts.append("TASA_ANUAL_ALTA")

        # Col 24 en febrero suele ser fecha programada cuota 1, no fecha de pago real
        f1_excel = parse_date_field(cell(parts, 23 if len(parts) > 23 else None))
        f1_prog = parse_date_field(cell(parts, 24))
        if fc and f1_prog:
            d_ini = datetime.strptime(fc, "%Y-%m-%d").date()
            d_prog = datetime.strptime(f1_prog, "%Y-%m-%d").date()
            d_crono = fecha_cronograma(d_ini, 1)
            if d_prog < d_ini:
                alerts.append("FECHA_PROG_C1_ANTES_INICIO")
            elif d_prog != d_crono:
                diff = abs((d_prog - d_crono).days)
                if diff > 3:
                    alerts.append(f"FECHA_PROG_C1_DESFASE_{diff}D")

        if not alerts:
            alerts = ["OK"]

        resultados.append(
            {
                "id_contrato": id_norm,
                "dni": dni,
                "cliente": nombre,
                "chasis": chasis,
                "monto_financiar": f"{m_fin:.2f}",
                "plazo_meses": plazo or "",
                "cuota_cronograma": f"{cuota_cronograma:.2f}" if cuota_cronograma else "",
                "cuota_base": f"{cuota_base:.2f}" if cuota_base else "",
                "total_con_mora": f"{total_mora:.2f}" if total_mora else "",
                "mora_monto": f"{mora_monto:.2f}" if mora_monto else "",
                "fecha_comienzo": fc or "",
                "fecha_vencimiento": fv or "",
                "vendedor": vendedor,
                "condicion": condicion,
                "cuotas_pagadas": pagadas,
                "fecha_pago_1": fechas.get(1) or "",
                "fecha_pago_2": fechas.get(2) or "",
                "fecha_pago_3": fechas.get(3) or "",
                "fecha_pago_4": fechas.get(4) or "",
                "fecha_pago_5": fechas.get(5) or "",
                "tasa_mensual_pct": f"{tasa_m * 100:.3f}" if tasa_m else "",
                "tasa_anual_pct": f"{tasa_anual:.1f}" if tasa_anual else "",
                "alertas": ";".join(alerts),
                "requiere_revision": "NO" if alerts == ["OK"] else "SI",
            }
        )
    return resultados


def main() -> int:
    res = analizar()
    ok = [r for r in res if r["alertas"] == "OK"]
    bad = [r for r in res if r["alertas"] != "OK"]

    with open(OUT, "w", encoding="utf-8-sig", newline="") as f:
        w = csv.DictWriter(f, fieldnames=list(res[0].keys()), delimiter=";")
        w.writeheader()
        w.writerows(res)

    c: Counter[str] = Counter()
    for r in bad:
        for a in r["alertas"].split(";"):
            c[a] += 1

    print("=== FEBRERO 2026 — ANALISIS CON COLUMNAS CORREGIDAS ===")
    print(f"Total: {len(res)} | OK: {len(ok)} | Con alertas: {len(bad)}")
    print(f"CSV: {OUT}")
    print("\nAlertas:")
    for k, v in c.most_common():
        print(f"  {k}: {v}")
    print("\n--- Contratos con problemas ---")
    for r in bad:
        print(
            f"{r['id_contrato']} | DNI {r['dni']} | {r['cliente'][:35]} | "
            f"fin S/{r['monto_financiar']} | {r['plazo_meses']}m | cuota {r['cuota_cronograma']} | "
            f"pag {r['cuotas_pagadas']} | {r['alertas']}"
        )
    return 0


if __name__ == "__main__":
    sys.exit(main())
