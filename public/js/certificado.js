(() => {
  "use strict";

  document.addEventListener("DOMContentLoaded", () => {
    const certificado = document.getElementById("certificado");
    const btnPdf = document.getElementById("btn-descargar-pdf");
    const btnPrint = document.getElementById("btn-imprimir");

    if (btnPrint) {
      btnPrint.addEventListener("click", () => window.print());
    }

    if (!btnPdf || !certificado) return;

    btnPdf.addEventListener("click", async () => {
      if (typeof PDFLib === "undefined") {
        alert("No se pudo cargar el generador de PDF. Recargue la página.");
        return;
      }

      const filename = certificado.dataset.filename || "Certificado_Asistencia.pdf";
      const nombre = certificado.dataset.nombre || "";
      const cedula = certificado.dataset.cedula || "";
      const templateUrl = certificado.dataset.template || "/img/plantilla-certificado.pdf";
      const original = btnPdf.textContent;
      btnPdf.disabled = true;
      btnPdf.textContent = "Generando PDF…";

      try {
        const res = await fetch(templateUrl);
        if (!res.ok) {
          throw new Error("No se pudo leer la plantilla PDF");
        }

        const bytes = await res.arrayBuffer();
        const pdf = await PDFLib.PDFDocument.load(bytes);
        const page = pdf.getPages()[0];
        const { width, height } = page.getSize();
        const fontBold = await pdf.embedFont(PDFLib.StandardFonts.TimesBold);
        const fontReg = await pdf.embedFont(PDFLib.StandardFonts.TimesRoman);
        const color = PDFLib.rgb(10 / 255, 33 / 255, 77 / 255);

        const fitSize = (text, font, maxSize, maxWidth) => {
          let size = maxSize;
          while (size > 10 && font.widthOfTextAtSize(text, size) > maxWidth) {
            size -= 0.5;
          }
          return size;
        };

        const maxWidth = width * 0.74;
        const nameSize = fitSize(nombre, fontBold, 22, maxWidth);
        const nameWidth = fontBold.widthOfTextAtSize(nombre, nameSize);
        page.drawText(nombre, {
          x: (width - nameWidth) / 2,
          y: height * 0.672,
          size: nameSize,
          font: fontBold,
          color,
        });

        if (cedula) {
          const cedSize = 12;
          const cedWidth = fontReg.widthOfTextAtSize(cedula, cedSize);
          page.drawText(cedula, {
            x: (width - cedWidth) / 2,
            y: height * 0.635,
            size: cedSize,
            font: fontReg,
            color,
          });
        }

        const out = await pdf.save();
        const blob = new Blob([out], { type: "application/pdf" });
        const url = URL.createObjectURL(blob);
        const a = document.createElement("a");
        a.href = url;
        a.download = filename;
        a.click();
        setTimeout(() => URL.revokeObjectURL(url), 1500);
      } catch (err) {
        console.error(err);
        alert("Ocurrió un error al generar el PDF. Intente nuevamente.");
      } finally {
        btnPdf.disabled = false;
        btnPdf.textContent = original;
      }
    });
  });
})();
