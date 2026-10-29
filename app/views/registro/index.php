<?php
$old = $old ?? [];
$errores = $errores ?? [];
$mensaje = $mensaje ?? null;
$tipo = $tipo ?? null;

function old_val(array $old, string $key): string {
    return htmlspecialchars((string) ($old[$key] ?? ''), ENT_QUOTES, 'UTF-8');
}
function err_class(array $errores, string $key): string {
    return isset($errores[$key]) ? ' is-invalid' : '';
}
?>
<section class="section section-registro py-4 py-md-5">
    <div class="container" style="max-width: 760px;">
        <header class="section-head mb-4">
            <span class="badge rounded-pill text-bg-primary mb-2">06 de noviembre de 2026 · 8:00 AM</span>
            <h1 class="h2 mb-1">Registro de Asistentes</h1>
            <p class="text-muted mb-0">II Foro de Tecnologías Emergentes: Inteligencia Artificial</p>
        </header>

        <div id="alerta"
             class="alert<?= $mensaje ? ' alert-' . ($tipo === 'error' ? 'danger' : htmlspecialchars((string) $tipo, ENT_QUOTES, 'UTF-8')) . ' show' : ' d-none' ?>"
             role="status" aria-live="polite">
            <?= $mensaje ? htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8') : '' ?>
        </div>

        <form id="form-registro" class="card shadow-sm border-0 p-3 p-md-4" method="POST" action="/registro" novalidate>
            <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>">

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label" for="cedula">Cédula <span class="text-danger">*</span></label>
                    <input type="text" class="form-control<?= err_class($errores, 'cedula') ?>" id="cedula" name="cedula"
                           maxlength="10" inputmode="numeric" pattern="[0-9]{6,10}"
                           placeholder="Ingrese su número de cédula"
                           value="<?= old_val($old, 'cedula') ?>" autocomplete="off" required>
                    <div class="invalid-feedback d-block field-error" data-error-for="cedula"><?= isset($errores['cedula']) ? htmlspecialchars($errores['cedula'], ENT_QUOTES, 'UTF-8') : '' ?></div>
                </div>

                <div class="col-md-6">
                    <label class="form-label" for="nombre">Primer nombre <span class="text-danger">*</span></label>
                    <input type="text" class="form-control<?= err_class($errores, 'nombre') ?>" id="nombre" name="nombre"
                           maxlength="30" placeholder="Ingrese su primer nombre"
                           value="<?= old_val($old, 'nombre') ?>" autocomplete="given-name" required>
                    <div class="invalid-feedback d-block field-error" data-error-for="nombre"><?= isset($errores['nombre']) ? htmlspecialchars($errores['nombre'], ENT_QUOTES, 'UTF-8') : '' ?></div>
                </div>

                <div class="col-md-6">
                    <label class="form-label" for="apellido">Primer apellido <span class="text-danger">*</span></label>
                    <input type="text" class="form-control<?= err_class($errores, 'apellido') ?>" id="apellido" name="apellido"
                           maxlength="30" placeholder="Ingrese su primer apellido"
                           value="<?= old_val($old, 'apellido') ?>" autocomplete="family-name" required>
                    <div class="invalid-feedback d-block field-error" data-error-for="apellido"><?= isset($errores['apellido']) ? htmlspecialchars($errores['apellido'], ENT_QUOTES, 'UTF-8') : '' ?></div>
                </div>

                <div class="col-md-6">
                    <label class="form-label" for="fecha_nacimiento">Fecha de nacimiento <span class="text-danger">*</span></label>
                    <input type="date" class="form-control<?= err_class($errores, 'fecha_nacimiento') ?>" id="fecha_nacimiento"
                           name="fecha_nacimiento" value="<?= old_val($old, 'fecha_nacimiento') ?>"
                           placeholder="Ingrese su fecha de nacimiento"
                           max="<?= date('Y-m-d') ?>" required>
                    <div class="invalid-feedback d-block field-error" data-error-for="fecha_nacimiento"><?= isset($errores['fecha_nacimiento']) ? htmlspecialchars($errores['fecha_nacimiento'], ENT_QUOTES, 'UTF-8') : '' ?></div>
                </div>

                <div class="col-md-6">
                    <label class="form-label" for="edad">Edad</label>
                    <input type="text" class="form-control" id="edad" name="edad_display" readonly
                           placeholder="Su edad se calcula automáticamente" value="" tabindex="-1" aria-readonly="true">
                </div>

                <div class="col-md-6">
                    <label class="form-label" for="correo">Correo electrónico <span class="text-danger">*</span></label>
                    <input type="email" class="form-control<?= err_class($errores, 'correo') ?>" id="correo" name="correo"
                           maxlength="150" placeholder="Ingrese su correo electrónico"
                           value="<?= old_val($old, 'correo') ?>" autocomplete="email" required>
                    <div class="invalid-feedback d-block field-error" data-error-for="correo"><?= isset($errores['correo']) ? htmlspecialchars($errores['correo'], ENT_QUOTES, 'UTF-8') : '' ?></div>
                </div>

                <div class="col-md-6">
                    <label class="form-label" for="telefono">Teléfono <span class="text-danger">*</span></label>
                    <input type="tel" class="form-control<?= err_class($errores, 'telefono') ?>" id="telefono" name="telefono"
                           maxlength="15" inputmode="numeric" pattern="[0-9]{7,15}"
                           placeholder="Ingrese su número de teléfono"
                           value="<?= old_val($old, 'telefono') ?>" autocomplete="tel" required>
                    <div class="invalid-feedback d-block field-error" data-error-for="telefono"><?= isset($errores['telefono']) ? htmlspecialchars($errores['telefono'], ENT_QUOTES, 'UTF-8') : '' ?></div>
                </div>

                <div class="col-12">
                    <label class="form-label" for="viene_institucion">¿Vienes de otra universidad, empresa o institución?</label>
                    <?php $vieneOld = old_val($old, 'viene_institucion'); ?>
                    <select class="form-select<?= err_class($errores, 'viene_institucion') ?>" id="viene_institucion" name="viene_institucion">
                        <option value="" <?= $vieneOld === '' ? 'selected' : '' ?>>Seleccione…</option>
                        <option value="no" <?= $vieneOld === 'no' ? 'selected' : '' ?>>No</option>
                        <option value="si" <?= in_array($vieneOld, ['si', 'sí'], true) ? 'selected' : '' ?>>Sí</option>
                    </select>
                    <div class="invalid-feedback d-block field-error" data-error-for="viene_institucion"><?= isset($errores['viene_institucion']) ? htmlspecialchars($errores['viene_institucion'], ENT_QUOTES, 'UTF-8') : '' ?></div>
                </div>

                <?php
                $showInst = in_array($vieneOld, ['si', 'sí'], true);
                $tipoOld = old_val($old, 'institution_type');
                ?>
                <div class="col-md-6 inst-extra<?= $showInst ? '' : ' d-none' ?>" id="wrap-tipo-institucion">
                    <label class="form-label" for="institution_type">Tipo de procedencia</label>
                    <select class="form-select<?= err_class($errores, 'institution_type') ?>" id="institution_type" name="institution_type">
                        <option value="">Seleccione…</option>
                        <option value="universidad" <?= $tipoOld === 'universidad' ? 'selected' : '' ?>>Universidad</option>
                        <option value="empresa" <?= $tipoOld === 'empresa' ? 'selected' : '' ?>>Empresa</option>
                        <option value="otra" <?= $tipoOld === 'otra' ? 'selected' : '' ?>>Otra institución</option>
                    </select>
                    <div class="invalid-feedback d-block field-error" data-error-for="institution_type"><?= isset($errores['institution_type']) ? htmlspecialchars($errores['institution_type'], ENT_QUOTES, 'UTF-8') : '' ?></div>
                </div>

                <div class="col-md-6 inst-extra<?= $showInst ? '' : ' d-none' ?>" id="wrap-nombre-institucion">
                    <label class="form-label" for="institution_name">Indica la universidad, empresa o institución de la que vienes</label>
                    <input type="text" class="form-control<?= err_class($errores, 'institution_name') ?>" id="institution_name"
                           name="institution_name" maxlength="150"
                           placeholder="Indica la universidad, empresa o institución de la que vienes"
                           value="<?= old_val($old, 'institution_name') ?>">
                    <div class="invalid-feedback d-block field-error" data-error-for="institution_name"><?= isset($errores['institution_name']) ? htmlspecialchars($errores['institution_name'], ENT_QUOTES, 'UTF-8') : '' ?></div>
                </div>
            </div>

            <div class="d-flex justify-content-end mt-4">
                <button type="submit" class="btn btn-primary px-4" id="btn-registrar">Registrar</button>
            </div>
        </form>
    </div>
</section>

<div class="success-modal<?= ($tipo ?? '') === 'success' ? ' is-open' : '' ?>" id="modal-registro-exito" role="dialog"
     aria-modal="true" aria-labelledby="modal-exito-titulo" <?= ($tipo ?? '') === 'success' ? '' : 'hidden' ?>>
    <div class="success-modal-backdrop" data-close-modal></div>
    <div class="success-modal-card">
        <div class="success-check" aria-hidden="true">
            <svg viewBox="0 0 72 72">
                <circle class="success-check-ring" cx="36" cy="36" r="32"></circle>
                <path class="success-check-mark" d="M20 37.5 L31.5 49 L53 24"></path>
            </svg>
        </div>
        <p class="success-kicker">II Foro · Inteligencia Artificial</p>
        <h2 id="modal-exito-titulo">¡Asistencia registrada!</h2>
        <p class="success-text" id="modal-exito-mensaje">
            <?= ($tipo ?? '') === 'success' && !empty($mensaje)
                ? htmlspecialchars((string) $mensaje, ENT_QUOTES, 'UTF-8')
                : 'Tu registro se guardó correctamente. ¡Te esperamos el 06 de noviembre de 2026!' ?>
        </p>
        <div class="success-actions">
            <button type="button" class="btn btn-primary" data-close-modal>Cerrar</button>
        </div>
    </div>
</div>
