<?php
$nombreCompleto = mb_strtoupper(
    trim(($estudiante['nombre'] ?? '') . ' ' . ($estudiante['apellido'] ?? '')),
    'UTF-8'
);
$cedulaRaw = preg_replace('/\D+/', '', (string) ($estudiante['cedula'] ?? '')) ?? '';
$cedulaFmt = $cedulaRaw !== ''
    ? 'V - ' . number_format((int) $cedulaRaw, 0, ',', '.')
    : '';
$archivoPdf = 'Certificado_Asistencia_' . preg_replace('/[^A-Za-z0-9_-]+/', '_', $nombreCompleto) . '.pdf';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificado de Asistencia — <?= htmlspecialchars($nombreCompleto, ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:wght@400;700&family=PT+Serif:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/certificado.css">
</head>
<body class="cert-page">
    <div class="cert-toolbar no-print">
        <a href="/certificados" class="btn-tool">← Volver</a>
        <div class="cert-toolbar-actions">
            <button type="button" class="btn-tool" id="btn-imprimir">Imprimir</button>
            <button type="button" class="btn-tool btn-tool-primary" id="btn-descargar-pdf">Descargar PDF</button>
        </div>
    </div>

    <div class="cert-viewport">
        <article class="certificate" id="certificado" aria-label="Certificado de asistencia"
                 data-filename="<?= htmlspecialchars($archivoPdf, ENT_QUOTES, 'UTF-8') ?>"
                 data-nombre="<?= htmlspecialchars($nombreCompleto, ENT_QUOTES, 'UTF-8') ?>"
                 data-cedula="<?= htmlspecialchars($cedulaFmt, ENT_QUOTES, 'UTF-8') ?>"
                 data-template="/img/plantilla-certificado.pdf">
            <img class="cert-bg" src="/img/plantilla-certificado-hd.png" alt="Plantilla del certificado">
            <div class="cert-overlay">
                <p class="cert-name"><?= htmlspecialchars($nombreCompleto, ENT_QUOTES, 'UTF-8') ?></p>
                <p class="cert-cedula"><?= htmlspecialchars($cedulaFmt, ENT_QUOTES, 'UTF-8') ?></p>
            </div>
        </article>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/pdf-lib@1.17.1/dist/pdf-lib.min.js"></script>
    <script src="/js/certificado.js"></script>
</body>
</html>
