# -*- coding: utf-8 -*-
"""Extrae filas Feb- de un pegado ancho (Ene+Feb) → febrero_2026_paste_wide.tsv + SQL."""
import os
import re
import sys

from import_contratos_common import generate, read_import_tsv, write_simplified_tsv

REPLACEMENTS = (
    ("Feb-035-2060", "Feb-035-2026"),
    ("Feb-036-2061", "Feb-036-2026"),
    ("Feb-037-2062", "Feb-037-2026"),
    ("Feb-038-2063", "Feb-038-2026"),
    ("28-03-20226", "28-03-2026"),
    ("02-282026", "02-28-2026"),
)


def main():
    base = os.path.dirname(os.path.abspath(__file__))
    src = sys.argv[1] if len(sys.argv) > 1 else os.path.join(base, "_paste_full_wide.tsv")
    wide_out = os.path.join(base, "febrero_2026_paste_wide.tsv")
    simple_out = os.path.join(base, "febrero_2026_datos.tsv")

    if not os.path.isfile(src):
        print("Crea el archivo con el pegado completo (cabecera + filas):", src)
        sys.exit(1)

    with open(src, encoding="utf-8") as f:
        lines = f.readlines()

    if not lines:
        sys.exit("Archivo vacío")

    header = lines[0]
    feb_lines = []
    for ln in lines[1:]:
        if not ln.strip():
            continue
        if not ln.lstrip().lower().startswith("feb-"):
            continue
        for a, b in REPLACEMENTS:
            ln = ln.replace(a, b)
        if "s/80 not rec" in ln.lower():
            ln = re.sub(r"\s+s/80 not rec[^\t\n]*", "\ts/80 nota rec veh", ln, flags=re.I)
        feb_lines.append(ln if ln.endswith("\n") else ln + "\n")

    with open(wide_out, "w", encoding="utf-8") as f:
        f.write(header if header.endswith("\n") else header + "\n")
        f.writelines(feb_lines)

    header2, rows, was_wide = read_import_tsv(wide_out)
    write_simplified_tsv(simple_out, header2, rows)
    n = generate("febrero")
    print("Wide:", wide_out, "| filas Feb:", len(feb_lines), "| SQL rows:", n)


if __name__ == "__main__":
    main()
