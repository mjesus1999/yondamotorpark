# -*- coding: utf-8 -*-
"""Convierte pegado ancho (enero_2026_paste_wide.tsv) → enero_2026_datos.tsv + SQL."""
import os
import sys

from import_contratos_common import generate, read_import_tsv, write_simplified_tsv


def main():
    base = os.path.dirname(os.path.abspath(__file__))
    wide = os.path.join(base, "enero_2026_paste_wide.tsv")
    simple = os.path.join(base, "enero_2026_datos.tsv")
    src = sys.argv[1] if len(sys.argv) > 1 else wide
    if not os.path.isfile(src):
        print("No existe:", src)
        sys.exit(1)
    header, rows, was_wide = read_import_tsv(src)
    write_simplified_tsv(simple, header, rows)
    n = generate("enero")
    print("Origen:", src, "| ancho:", was_wide, "| filas:", len(rows), "| SQL:", n)


if __name__ == "__main__":
    main()
