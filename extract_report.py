import sys
try:
    from pypdf import PdfReader
except ImportError:
    try:
        from PyPDF2 import PdfReader
    except ImportError:
        print("Error: neither pypdf nor PyPDF2 installed")
        sys.exit(1)

try:
    reader = PdfReader("TRƯỜNG ĐẠI HỌC QUY NHƠN.pdf")
    text = ""
    # Search for Section VII in the text to limit output if possible, but extracting all is safer
    for i, page in enumerate(reader.pages):
        page_text = page.extract_text()
        text += f"\n--- Page {i+1} ---\n{page_text}"
    
    with open("report_content.txt", "w", encoding="utf-8") as f:
        f.write(text)
    print("Done")
except Exception as e:
    print(f"Error reading PDF: {e}")
