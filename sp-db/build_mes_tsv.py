# -*- coding: utf-8 -*-
"""Extrae filas por prefijo (Mar-, Feb-, etc.) hacia el TSV del mes."""
import os
import re
import sys

PREFIX = {
    "mayo": "may-",
    "abril": "abr-",
    "marzo": "mar-",
    "febrero": "feb-",
    "enero": "ene-",
}

REPLACEMENTS = (
    ("Feb-035-2060", "Feb-035-2026"),
    ("Feb-036-2061", "Feb-036-2026"),
    ("Feb-037-2062", "Feb-037-2026"),
    ("Feb-038-2063", "Feb-038-2026"),
    ("28-03-20226", "28-03-2026"),
)

def main():
    mes = (sys.argv[1] if len(sys.argv) > 1 else "marzo").lower()
    pfx = PREFIX.get(mes)
    if not pfx:
        raise SystemExit(f"Mes no soportado: {mes}")

    base = os.path.dirname(os.path.abspath(__file__))
    src = os.path.join(base, "_paste_full.tsv")
    out_name = {
        "mayo": "mayo_2026_datos.tsv",
        "abril": "abril_2026_datos.tsv",
        "marzo": "marzo_2026_datos.tsv",
        "febrero": "febrero_2026_datos.tsv",
        "enero": "enero_2026_datos.tsv",
    }[mes] 
    dst = os.path.join(base, out_name)

    with open(src, encoding="utf-8") as f:
        lines = f.readlines()

    header = lines[0]
    picked = [ln for ln in lines[1:] if ln.strip() and ln.lstrip().lower().startswith(pfx)]

    out_lines = [header]
    for ln in picked:
        for a, b in REPLACEMENTS:
            ln = ln.replace(a, b)
        ln = ln.replace("\r\n", "\n")
        if "s/80 not rec" in ln.lower():
            ln = re.sub(r"\s+s/80 not rec[^\t\n]*", "\ts/80 nota rec veh", ln, flags=re.I)
        if "S/80 NOT REC" in ln:
            ln = re.sub(r"S/80 NOT REC[^\t\n]*", "S/80 nota rec veh", ln, flags=re.I)
        out_lines.append(ln if ln.endswith("\n") else ln + "\n")

    with open(dst, "w", encoding="utf-8") as f:
        f.writelines(out_lines)
    print(mes, "rows", len(picked), "->", dst)


if __name__ == "__main__":
    main()
