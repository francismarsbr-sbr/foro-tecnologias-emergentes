<?php
$estudiantes = $estudiantes ?? [];
$error = $error ?? null;
$asistenciaHabilitada = !empty($asistenciaHabilitada);
$total = count($estudiantes);
$asistentes = count(array_filter($estudiantes, static fn($e) => filter_var($e['asistio'], FILTER_VALIDATE_BOOLEAN)));
$colspan = $asistenciaHabilitada ? 9 : 8;

$labelTipo = static function (string $tipo): string {
    return match ($tipo) {
        'universidad' => 'Universidad',
        'empresa'     => 'Empresa',
        'otra'        => 'Otra institución',
        default       => 'No indicó',
    };
};
?>
<section class="section py-4">
    <div class="container">
        <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-3">
            <div>
                <h1 class="h3 mb-1">Panel de logística</h1>
                <p class="text-muted mb-0">
                    Evento: <?= htmlspecialchars($eventoFecha, ENT_QUOTES, 'UTF-8') ?> · <?= htmlspecialchars($eventoHora, ENT_QUOTES, 'UTF-8') ?>
                    · Sesión: <strong><?= htmlspecialchars((string) ($usuario ?? ''), ENT_QUOTES, 'UTF-8') ?></strong>
                </p>
            </div>
            <div class="d-flex flex-wrap align-items-center gap-3">
                <span class="badge text-bg-primary fs-6"><span id="stat-total"><?= $total ?></span> registrados</span>
                <?php if ($asistenciaHabilitada): ?>
                    <span class="badge text-bg-success fs-6"><span id="stat-asistentes"><?= $asistentes ?></span> asistieron</span>
                <?php endif; ?>
                <a class="btn btn-outline-primary btn-sm" href="/logout">Cerrar sesión</a>
            </div>
        </div>

        <?php if ($asistenciaHabilitada): ?>
            <div class="alert alert-success" role="status">
                Modo prueba activo: use el selector <strong>¿Vino?</strong> para marcar asistencia y generar certificados.
            </div>
        <?php else: ?>
            <div class="alert alert-info" role="status">
                Listado de inscritos en vivo. El control de asistencia se habilitará el <strong>06 de noviembre de 2026</strong>.
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
            <div class="flex-grow-1" style="max-width: 360px;">
                <label class="visually-hidden" for="buscador">Buscar</label>
                <input type="search" class="form-control" id="buscador"
                       placeholder="Buscar por cédula o apellido…" autocomplete="off">
            </div>
            <p class="text-muted small mb-0">Actualización automática cada 15 s</p>
        </div>

        <div class="filtro-procedencia mb-3" role="group" aria-label="Filtrar por procedencia">
            <span class="filtro-label">Procedencia:</span>
            <button type="button" class="btn btn-sm btn-primary filtro-tipo is-active" data-tipo="todos">Todos</button>
            <button type="button" class="btn btn-sm btn-outline-primary filtro-tipo" data-tipo="universidad">Universidad</button>
            <button type="button" class="btn btn-sm btn-outline-primary filtro-tipo" data-tipo="empresa">Empresa</button>
            <button type="button" class="btn btn-sm btn-outline-primary filtro-tipo" data-tipo="otra">Otra Institución</button>
            <button type="button" class="btn btn-sm btn-outline-primary filtro-tipo" data-tipo="no_indico">No indicó</button>
        </div>

        <div class="table-responsive card shadow-sm border-0">
            <table class="table table-hover table-striped align-middle mb-0" id="tabla-asistencia"
                   data-asistencia="<?= $asistenciaHabilitada ? '1' : '0' ?>">
                <thead class="table-primary">
                    <tr>
                        <th>Cédula</th>
                        <th>Apellido</th>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Teléfono</th>
                        <th>Institución</th>
                        <?php if ($asistenciaHabilitada): ?>
                            <th>¿Vino?</th>
                            <th>Certificado</th>
                        <?php else: ?>
                            <th>Registro</th>
                        <?php endif; ?>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="tbody-estudiantes">
                <?php if ($estudiantes === []): ?>
                    <tr class="empty-row">
                        <td colspan="<?= $colspan ?>" class="text-center text-muted py-4">No hay participantes registrados aún.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($estudiantes as $est): ?>
                        <?php
                        $asistio = filter_var($est['asistio'], FILTER_VALIDATE_BOOLEAN);
                        $id = (int) $est['id'];
                        $fechaReg = !empty($est['fecha_registro'])
                            ? date('d/m/Y H:i', strtotime((string) $est['fecha_registro']))
                            : '—';
                        $tipoInst = (string) ($est['institution_type'] ?? 'no_indico');
                        $nombreInst = (string) ($est['institution_name'] ?? 'No indicó institución');
                        ?>
                        <tr data-id="<?= $id ?>"
                            data-cedula="<?= htmlspecialchars(strtolower((string) $est['cedula']), ENT_QUOTES, 'UTF-8') ?>"
                            data-apellido="<?= htmlspecialchars(mb_strtolower((string) $est['apellido'], 'UTF-8'), ENT_QUOTES, 'UTF-8') ?>"
                            data-tipo="<?= htmlspecialchars($tipoInst, ENT_QUOTES, 'UTF-8') ?>">
                            <td><?= htmlspecialchars((string) $est['cedula'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars((string) $est['apellido'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars((string) $est['nombre'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars((string) $est['correo'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars((string) $est['telefono'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td>
                                <span class="badge text-bg-light border"><?= htmlspecialchars($labelTipo($tipoInst), ENT_QUOTES, 'UTF-8') ?></span>
                                <div class="small text-muted"><?= htmlspecialchars($nombreInst, ENT_QUOTES, 'UTF-8') ?></div>
                            </td>
                            <?php if ($asistenciaHabilitada): ?>
                                <td>
                                    <?php if ($asistio): ?>
                                        <select class="form-select form-select-sm select-asistencia" data-id="<?= $id ?>" disabled
                                                title="Asistencia confirmada: no se puede modificar"
                                                aria-label="Asistencia confirmada de <?= htmlspecialchars((string) $est['nombre'] . ' ' . $est['apellido'], ENT_QUOTES, 'UTF-8') ?>">
                                            <option value="1" selected>Sí</option>
                                        </select>
                                    <?php else: ?>
                                        <select class="form-select form-select-sm select-asistencia" data-id="<?= $id ?>"
                                                aria-label="Asistencia de <?= htmlspecialchars((string) $est['nombre'] . ' ' . $est['apellido'], ENT_QUOTES, 'UTF-8') ?>">
                                            <option value="0" selected>No</option>
                                            <option value="1">Sí</option>
                                        </select>
                                    <?php endif; ?>
                                </td>
                                <td class="cell-cert">
                                    <?php if ($asistio): ?>
                                        <a class="btn btn-sm btn-outline-primary" href="/certificados/<?= $id ?>" target="_blank" rel="noopener">Ver</a>
                                    <?php else: ?>
                                        <span class="text-muted">—</span>
                                    <?php endif; ?>
                                </td>
                            <?php else: ?>
                                <td><?= htmlspecialchars($fechaReg, ENT_QUOTES, 'UTF-8') ?></td>
                            <?php endif; ?>
                            <td>
                                <button type="button" class="btn btn-sm btn-outline-danger btn-eliminar"
                                        data-id="<?= $id ?>"
                                        data-nombre="<?= htmlspecialchars((string) $est['nombre'] . ' ' . $est['apellido'], ENT_QUOTES, 'UTF-8') ?>">
                                    Eliminar
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

        <input type="hidden" id="csrf-logistica" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>">
        <p id="toast-logistica" class="toast" role="status" aria-live="polite"></p>
    </div>
</section>

<div class="confirm-modal" id="modal-eliminar" hidden role="dialog" aria-modal="true" aria-labelledby="modal-eliminar-titulo">
    <div class="confirm-modal-backdrop" data-close-confirm></div>
    <div class="confirm-modal-card">
        <h2 id="modal-eliminar-titulo">Eliminar registro</h2>
        <p id="modal-eliminar-texto">¿Seguro que desea eliminar este asistente? Esta acción no se puede deshacer.</p>
        <div class="confirm-modal-actions">
            <button type="button" class="btn btn-ghost" data-close-confirm>Cancelar</button>
            <button type="button" class="btn btn-danger" id="btn-confirmar-eliminar">Sí, eliminar</button>
        </div>
    </div>
</div>
