# -*- coding: utf-8 -*-
"""Regenera febrero_2026_datos.tsv + import_contratos_febrero_2026.sql desde _paste_full.tsv."""
import os
import sys

from import_contratos_common import generate, read_import_tsv, write_simplified_tsv

BASE = os.path.dirname(os.path.abspath(__file__))
PASTE = os.path.join(BASE, "_paste_full.tsv")
OUT = os.path.join(BASE, "febrero_2026_datos.tsv")


def main() -> int:
    if not os.path.isfile(PASTE):
        print("Falta:", PASTE)
        return 1
    header, rows, was_wide = read_import_tsv(PASTE)
    feb = [r for r in rows if (r[0] or "").strip().upper().startswith("FEB-")]
    write_simplified_tsv(OUT, header, feb, from_wide=was_wide)
    n = generate("febrero")
    print("Origen:", PASTE, "| ancho:", was_wide, "| filas febrero:", len(feb), "| SQL filas:", n)
    return 0


if __name__ == "__main__":
    sys.exit(main())
