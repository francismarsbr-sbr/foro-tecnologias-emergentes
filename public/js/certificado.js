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
      if (typeof html2pdf === "undefined") {
        alert("No se pudo cargar el generador de PDF. Recargue la página.");
        return;
      }

      const filename = certificado.dataset.filename || "Certificado_Asistencia.pdf";
      const original = btnPdf.textContent;
      btnPdf.disabled = true;
      btnPdf.textContent = "Generando PDF…";

      try {
        // Esperar a que la imagen de fondo esté lista
        const bg = certificado.querySelector(".cert-bg");
        if (bg && !bg.complete) {
          await new Promise((resolve) => {
            bg.onload = resolve;
            bg.onerror = resolve;
          });
        }

        const opt = {
          margin: 0,
          filename,
          image: { type: "jpeg", quality: 0.98 },
          html2canvas: {
            scale: 2,
            useCORS: true,
            allowTaint: true,
            backgroundColor: "#ffffff",
            logging: false,
          },
          jsPDF: {
            unit: "mm",
            format: "a4",
            orientation: "landscape",
          },
          pagebreak: { mode: ["avoid-all"] },
        };

        await html2pdf().set(opt).from(certificado).save();
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
