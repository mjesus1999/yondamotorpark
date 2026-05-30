# -*- coding: utf-8 -*-
"""Convierte pegado ancho (febrero_2026_paste_wide.tsv) → febrero_2026_datos.tsv + SQL."""
import os
import sys

from import_contratos_common import generate, read_import_tsv, write_simplified_tsv


def main():
    base = os.path.dirname(os.path.abspath(__file__))
    wide = os.path.join(base, "febrero_2026_paste_wide.tsv")
    simple = os.path.join(base, "febrero_2026_datos.tsv")
    src = sys.argv[1] if len(sys.argv) > 1 else wide
    if not os.path.isfile(src):
        print("No existe:", src)
        sys.exit(1)
    header, rows, was_wide = read_import_tsv(src)
    # Solo contratos de febrero si el pegado mezcla otros meses
    rows = [r for r in rows if (r[0] or "").strip().upper().startswith("FEB-")]
    write_simplified_tsv(simple, header, rows)
    n = generate("febrero")
    print("Origen:", src, "| ancho:", was_wide, "| filas febrero:", len(rows), "| SQL:", n)


if __name__ == "__main__":
    main()
