# -*- coding: utf-8 -*-
"""Extrae filas Feb-* de _paste_full.tsv hacia febrero_2026_datos.tsv"""
import os
import re

base = os.path.dirname(os.path.abspath(__file__))
src = os.path.join(base, "_paste_full.tsv")
dst = os.path.join(base, "febrero_2026_datos.tsv")

with open(src, encoding="utf-8") as f:
    lines = f.readlines()

if not lines:
    raise SystemExit("Falta _paste_full.tsv")

header = lines[0]
feb = [ln for ln in lines[1:] if ln.strip() and ln.lstrip().lower().startswith("feb-")]

replacements = (
    ("Feb-035-2060", "Feb-035-2026"),
    ("Feb-036-2061", "Feb-036-2026"),
    ("Feb-037-2062", "Feb-037-2026"),
    ("Feb-038-2063", "Feb-038-2026"),
    ("28-03-20226", "28-03-2026"),
)

out_lines = [header]
for ln in feb:
    for a, b in replacements:
        ln = ln.replace(a, b)
    ln = ln.replace("\n\n", " ").replace("\r\n", "\n")
    if "s/80 not rec" in ln:
        ln = re.sub(r"\s+s/80 not rec[^\t\n]*", "\ts/80 nota rec 06/04", ln)
    out_lines.append(ln if ln.endswith("\n") else ln + "\n")

with open(dst, "w", encoding="utf-8") as f:
    f.writelines(out_lines)

print("febrero rows", len(feb), "->", dst)
