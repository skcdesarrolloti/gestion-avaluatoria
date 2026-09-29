from pathlib import Path
import re

from reportlab.lib import colors
from reportlab.lib.enums import TA_CENTER
from reportlab.lib.pagesizes import letter
from reportlab.lib.styles import getSampleStyleSheet, ParagraphStyle
from reportlab.lib.units import inch
from reportlab.platypus import Paragraph, SimpleDocTemplate, Spacer


ROOT = Path(__file__).resolve().parents[1]
SOURCE = ROOT / "docs" / "ACADEMIA_NIIF_VALOR_RAZONABLE.md"
OUTPUT = ROOT / "output" / "pdf" / "academia-niif-valor-razonable.pdf"


def inline_md(value: str) -> str:
    value = value.replace("&", "&amp;").replace("<", "&lt;").replace(">", "&gt;")
    value = re.sub(r"\*\*(.+?)\*\*", r"<b>\1</b>", value)
    return value.replace("`", "")


def styles():
    sample = getSampleStyleSheet()
    sample.add(ParagraphStyle(name="TitleGA", parent=sample["Title"], fontName="Helvetica-Bold",
        fontSize=18, leading=22, textColor=colors.HexColor("#0f172a"),
        alignment=TA_CENTER, spaceAfter=14))
    sample.add(ParagraphStyle(name="H1GA", parent=sample["Heading1"], fontName="Helvetica-Bold",
        fontSize=14, leading=18, textColor=colors.HexColor("#0f766e"),
        spaceBefore=12, spaceAfter=8))
    sample.add(ParagraphStyle(name="H2GA", parent=sample["Heading2"], fontName="Helvetica-Bold",
        fontSize=12, leading=15, textColor=colors.HexColor("#1e3a8a"),
        spaceBefore=10, spaceAfter=6))
    sample.add(ParagraphStyle(name="BodyGA", parent=sample["BodyText"], fontName="Helvetica",
        fontSize=9.5, leading=14, textColor=colors.HexColor("#1f2937"),
        spaceAfter=6))
    sample.add(ParagraphStyle(name="BulletLineGA", parent=sample["BodyGA"],
        leftIndent=13, firstLineIndent=-8, spaceAfter=3))
    sample.add(ParagraphStyle(name="QuoteGA", parent=sample["BodyGA"], leftIndent=12,
        rightIndent=6, borderColor=colors.HexColor("#bfdbfe"), borderWidth=0.7,
        borderPadding=7, backColor=colors.HexColor("#eff6ff"), spaceBefore=4,
        spaceAfter=8))
    return sample


def footer(canvas, doc):
    canvas.saveState()
    canvas.setFont("Helvetica", 7.5)
    canvas.setFillColor(colors.HexColor("#64748b"))
    canvas.drawString(doc.leftMargin, 0.42 * inch, "SuCasa - Academia NIIF / Valor razonable")
    canvas.drawRightString(letter[0] - doc.rightMargin, 0.42 * inch, f"Pagina {doc.page}")
    canvas.restoreState()


def build_story(text: str):
    style = styles()
    story = [
        Paragraph("Academia NIIF - Valor razonable y jerarquia de datos", style["TitleGA"]),
        Paragraph("Ficha academica para Normas NIIF y para el capitulo metodologico cuando el encargo active medicion financiera, valor razonable, deterioro o revelacion.", style["BodyGA"]),
        Spacer(1, 6),
    ]
    quote_buffer: list[str] = []

    def flush_quote() -> None:
        nonlocal quote_buffer
        if quote_buffer:
            story.append(Paragraph("<br/>".join(quote_buffer), style["QuoteGA"]))
            quote_buffer = []

    for raw in text.splitlines()[2:]:
        line = raw.rstrip()
        if not line:
            flush_quote()
            story.append(Spacer(1, 3))
        elif line.startswith("## "):
            flush_quote()
            story.append(Paragraph(inline_md(line[3:]), style["H1GA"]))
        elif line.startswith("### "):
            flush_quote()
            story.append(Paragraph(inline_md(line[4:]), style["H2GA"]))
        elif line.startswith("- "):
            flush_quote()
            story.append(Paragraph("- " + inline_md(line[2:]), style["BulletLineGA"]))
        elif line.startswith(">"):
            quote_buffer.append(inline_md(line.lstrip("> ").strip()))
        else:
            flush_quote()
            story.append(Paragraph(inline_md(line), style["BodyGA"]))
    flush_quote()
    return story


def main() -> None:
    OUTPUT.parent.mkdir(parents=True, exist_ok=True)
    doc = SimpleDocTemplate(str(OUTPUT), pagesize=letter, rightMargin=0.7 * inch,
        leftMargin=0.7 * inch, topMargin=0.65 * inch, bottomMargin=0.65 * inch)
    doc.build(build_story(SOURCE.read_text(encoding="utf-8")), onFirstPage=footer,
        onLaterPages=footer)
    print(OUTPUT)


if __name__ == "__main__":
    main()
