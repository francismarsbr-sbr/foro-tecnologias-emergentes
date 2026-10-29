(() => {
  "use strict";

  const CEDULA_RE = /^[0-9]{6,10}$/;
  const NOMBRE_RE = /^[\p{L}'\-]+$/u;
  const EMAIL_RE = /^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$/;
  const TELEFONO_RE = /^[0-9]{7,15}$/;

  document.addEventListener("DOMContentLoaded", () => {
    initRegistro();
    initLogistica();
  });

  function soloDigitos(value, max) {
    return String(value || "").replace(/\D+/g, "").slice(0, max);
  }

  function soloLetrasNombre(value, max) {
    return String(value || "")
      .replace(/[^\p{L}'\-]/gu, "")
      .slice(0, max);
  }

  function calcularEdad(fechaISO) {
    if (!fechaISO) return "";
    const nacimiento = new Date(fechaISO + "T00:00:00");
    if (Number.isNaN(nacimiento.getTime())) return "";

    const hoy = new Date();
    let edad = hoy.getFullYear() - nacimiento.getFullYear();
    const m = hoy.getMonth() - nacimiento.getMonth();
    if (m < 0 || (m === 0 && hoy.getDate() < nacimiento.getDate())) {
      edad -= 1;
    }
    return edad >= 0 ? String(edad) : "";
  }

  function setError(field, message) {
    const slot = document.querySelector(`[data-error-for="${field.id}"]`);
    field.classList.toggle("is-invalid", Boolean(message));
    if (slot) slot.textContent = message || "";
  }

  function clearErrors(form) {
    form.querySelectorAll(".is-invalid").forEach((f) => f.classList.remove("is-invalid"));
    form.querySelectorAll(".field-error").forEach((e) => {
      e.textContent = "";
    });
  }

  function showAlert(el, message, type) {
    if (!el) return;
    const bsType = type === "error" ? "danger" : type;
    el.textContent = message;
    el.className = `alert alert-${bsType} show`;
    el.classList.remove("d-none");
  }

  function validarRegistro(form) {
    clearErrors(form);
    let ok = true;

    const cedula = soloDigitos(form.cedula.value, 10);
    const nombre = soloLetrasNombre(form.nombre.value.trim(), 30);
    const apellido = soloLetrasNombre(form.apellido.value.trim(), 30);
    const fecha = form.fecha_nacimiento.value;
    const correo = form.correo.value.trim().toLowerCase();
    const telefono = soloDigitos(form.telefono.value, 15);
    const viene = String(form.viene_institucion?.value || "").toLowerCase();

    form.cedula.value = cedula;
    form.nombre.value = nombre;
    form.apellido.value = apellido;
    form.correo.value = correo;
    form.telefono.value = telefono;

    if (!CEDULA_RE.test(cedula)) {
      setError(form.cedula, "La cédula debe tener solo números (6 a 10 dígitos).");
      ok = false;
    }

    if (nombre.length < 2 || !NOMBRE_RE.test(nombre)) {
      setError(form.nombre, "Ingrese solo su primer nombre (letras, máx. 30).");
      ok = false;
    }

    if (apellido.length < 2 || !NOMBRE_RE.test(apellido)) {
      setError(form.apellido, "Ingrese solo su primer apellido (letras, máx. 30).");
      ok = false;
    }

    const edad = Number(calcularEdad(fecha));
    if (!fecha || Number.isNaN(edad) || edad < 12 || edad > 100) {
      setError(form.fecha_nacimiento, "Fecha inválida. La edad debe estar entre 12 y 100 años.");
      ok = false;
    }

    if (!EMAIL_RE.test(correo)) {
      setError(form.correo, "Correo electrónico inválido.");
      ok = false;
    }

    if (!TELEFONO_RE.test(telefono)) {
      setError(form.telefono, "El teléfono debe tener solo números (7 a 15 dígitos).");
      ok = false;
    }

    if (viene === "si" || viene === "sí") {
      const tipo = String(form.institution_type?.value || "");
      const instNombre = String(form.institution_name?.value || "").trim();
      if (!["universidad", "empresa", "otra"].includes(tipo)) {
        setError(form.institution_type, "Indique si viene de universidad, empresa u otra institución.");
        ok = false;
      }
      if (instNombre.length < 2) {
        setError(form.institution_name, "Indique la universidad, empresa o institución de la que viene.");
        ok = false;
      }
    }

    return ok;
  }

  function toggleInstitucion(form) {
    const viene = String(form.viene_institucion?.value || "").toLowerCase();
    const show = viene === "si" || viene === "sí";
    document.querySelectorAll(".inst-extra").forEach((el) => {
      el.classList.toggle("d-none", !show);
    });
    if (!show && form.institution_name) {
      form.institution_name.value = "";
      if (form.institution_type) form.institution_type.value = "";
    }
  }

  function openSuccessModal(message) {
    const modal = document.getElementById("modal-registro-exito");
    const slot = document.getElementById("modal-exito-mensaje");
    if (!modal) return;
    if (slot && message) slot.textContent = message;
    modal.hidden = false;
    modal.classList.add("is-open");
  }

  function closeSuccessModal() {
    const modal = document.getElementById("modal-registro-exito");
    if (!modal) return;
    modal.classList.remove("is-open");
    modal.hidden = true;
  }

  function initRegistro() {
    const form = document.getElementById("form-registro");
    if (!form) return;

    const fecha = form.fecha_nacimiento;
    const edadInput = form.edad;
    const alerta = document.getElementById("alerta");
    const btn = document.getElementById("btn-registrar");

    form.cedula.addEventListener("input", () => {
      form.cedula.value = soloDigitos(form.cedula.value, 10);
    });
    form.telefono.addEventListener("input", () => {
      form.telefono.value = soloDigitos(form.telefono.value, 15);
    });
    form.nombre.addEventListener("input", () => {
      form.nombre.value = soloLetrasNombre(form.nombre.value, 30);
    });
    form.apellido.addEventListener("input", () => {
      form.apellido.value = soloLetrasNombre(form.apellido.value, 30);
    });

    const syncEdad = () => {
      edadInput.value = calcularEdad(fecha.value);
    };

    fecha.addEventListener("change", syncEdad);
    fecha.addEventListener("input", syncEdad);
    syncEdad();

    if (form.viene_institucion) {
      form.viene_institucion.addEventListener("change", () => toggleInstitucion(form));
      toggleInstitucion(form);
    }

    document.querySelectorAll("[data-close-modal]").forEach((el) => {
      el.addEventListener("click", closeSuccessModal);
    });

    form.addEventListener("submit", async (event) => {
      event.preventDefault();

      if (!validarRegistro(form)) {
        showAlert(alerta, "Revise los campos del formulario.", "error");
        return;
      }

      btn.disabled = true;
      const original = btn.textContent;
      btn.textContent = "Guardando…";

      try {
        const body = new FormData(form);
        const response = await fetch("/registro", {
          method: "POST",
          headers: {
            Accept: "application/json",
            "X-Requested-With": "XMLHttpRequest",
          },
          body,
        });

        const data = await response.json().catch(() => null);

        if (!response.ok || !data?.ok) {
          if (data?.errores) {
            Object.entries(data.errores).forEach(([key, msg]) => {
              const field = form.elements.namedItem(key);
              if (field && field instanceof HTMLElement) setError(field, msg);
            });
          }
          showAlert(alerta, data?.mensaje || "No se pudo completar el registro.", "error");
          return;
        }

        form.reset();
        edadInput.value = "";
        toggleInstitucion(form);
        clearErrors(form);
        if (alerta) alerta.classList.add("d-none");
        openSuccessModal(data.mensaje);
      } catch (err) {
        showAlert(alerta, "Error de red. Intente nuevamente.", "error");
      } finally {
        btn.disabled = false;
        btn.textContent = original;
      }
    });
  }

  function escapeHtml(str) {
    return String(str)
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;");
  }

  function initLogistica() {
    const tabla = document.getElementById("tabla-asistencia");
    const tbody = document.getElementById("tbody-estudiantes");
    const buscador = document.getElementById("buscador");
    const csrf = document.getElementById("csrf-logistica");
    const toast = document.getElementById("toast-logistica");
    if (!tabla || !tbody || !csrf) return;

    let asistenciaHabilitada = tabla.dataset.asistencia === "1";
    let filtroTipo = "todos";
    let pendingDeleteId = null;

    const mostrarToast = (msg, isError = false) => {
      if (!toast) return;
      toast.textContent = msg;
      toast.classList.toggle("is-error", isError);
      toast.classList.add("is-visible");
      clearTimeout(mostrarToast._t);
      mostrarToast._t = setTimeout(() => toast.classList.remove("is-visible"), 2800);
    };

    const labelTipo = (tipo) => {
      if (tipo === "universidad") return "Universidad";
      if (tipo === "empresa") return "Empresa";
      if (tipo === "otra") return "Otra institución";
      return "No indicó";
    };

    const aplicarFiltro = () => {
      const q = buscador ? buscador.value.trim().toLowerCase() : "";
      tbody.querySelectorAll("tr").forEach((row) => {
        if (row.classList.contains("empty-row")) return;
        const cedula = row.dataset.cedula || "";
        const apellido = row.dataset.apellido || "";
        const tipo = row.dataset.tipo || "no_indico";
        const matchTexto = !q || cedula.includes(q) || apellido.includes(q);
        const matchTipo = filtroTipo === "todos" || tipo === filtroTipo;
        row.classList.toggle("is-hidden", !(matchTexto && matchTipo));
      });
    };

    const formatFecha = (raw) => {
      if (!raw) return "—";
      const d = new Date(raw);
      if (Number.isNaN(d.getTime())) return "—";
      const pad = (n) => String(n).padStart(2, "0");
      return `${pad(d.getDate())}/${pad(d.getMonth() + 1)}/${d.getFullYear()} ${pad(d.getHours())}:${pad(d.getMinutes())}`;
    };

    const celdaInstitucion = (e) => {
      const tipo = String(e.institution_type || "no_indico");
      const nombreInst = escapeHtml(e.institution_name || "No indicó institución");
      return `<span class="badge text-bg-light border">${escapeHtml(labelTipo(tipo))}</span>
              <div class="small text-muted">${nombreInst}</div>`;
    };

    const botonEliminar = (e, nombre, apellido) =>
      `<button type="button" class="btn btn-sm btn-outline-danger btn-eliminar" data-id="${e.id}" data-nombre="${nombre} ${apellido}">Eliminar</button>`;

    const renderFilas = (estudiantes) => {
      const cols = asistenciaHabilitada ? 9 : 8;
      if (!estudiantes.length) {
        tbody.innerHTML = `<tr class="empty-row"><td colspan="${cols}">No hay participantes registrados aún.</td></tr>`;
        return;
      }

      tbody.innerHTML = estudiantes
        .map((e) => {
          const nombre = escapeHtml(e.nombre);
          const apellido = escapeHtml(e.apellido);
          const cedula = escapeHtml(e.cedula);
          const correo = escapeHtml(e.correo);
          const telefono = escapeHtml(e.telefono);
          const cedulaKey = String(e.cedula).toLowerCase();
          const apellidoKey = String(e.apellido).toLowerCase();
          const tipo = escapeHtml(String(e.institution_type || "no_indico"));
          const inst = celdaInstitucion(e);
          const eliminar = botonEliminar(e, nombre, apellido);

          if (asistenciaHabilitada) {
            const cert = e.asistio
              ? `<a class="btn btn-sm btn-outline-primary" href="/certificados/${e.id}" target="_blank" rel="noopener">Ver</a>`
              : `<span class="text-muted">—</span>`;
            const selectHtml = e.asistio
              ? `<select class="form-select form-select-sm select-asistencia" data-id="${e.id}" disabled title="Asistencia confirmada: no se puede modificar" aria-label="Asistencia confirmada de ${nombre} ${apellido}">
                  <option value="1" selected>Sí</option>
                </select>`
              : `<select class="form-select form-select-sm select-asistencia" data-id="${e.id}" aria-label="Asistencia de ${nombre} ${apellido}">
                  <option value="0" selected>No</option>
                  <option value="1">Sí</option>
                </select>`;
            return `<tr data-id="${e.id}" data-cedula="${escapeHtml(cedulaKey)}" data-apellido="${escapeHtml(apellidoKey)}" data-tipo="${tipo}">
              <td>${cedula}</td><td>${apellido}</td><td>${nombre}</td><td>${correo}</td><td>${telefono}</td>
              <td>${inst}</td>
              <td>${selectHtml}</td>
              <td class="cell-cert">${cert}</td>
              <td>${eliminar}</td>
            </tr>`;
          }

          return `<tr data-id="${e.id}" data-cedula="${escapeHtml(cedulaKey)}" data-apellido="${escapeHtml(apellidoKey)}" data-tipo="${tipo}">
            <td>${cedula}</td><td>${apellido}</td><td>${nombre}</td><td>${correo}</td><td>${telefono}</td>
            <td>${inst}</td>
            <td>${escapeHtml(formatFecha(e.fecha_registro))}</td>
            <td>${eliminar}</td>
          </tr>`;
        })
        .join("");

      aplicarFiltro();
    };

    const actualizarStats = (total, asistentes) => {
      const totalEl = document.getElementById("stat-total");
      const asisEl = document.getElementById("stat-asistentes");
      if (totalEl) totalEl.textContent = String(total);
      if (asisEl) asisEl.textContent = String(asistentes);
    };

    const refrescarLista = async () => {
      try {
        const response = await fetch("/logistica/lista", {
          headers: { Accept: "application/json", "X-Requested-With": "XMLHttpRequest" },
        });
        if (response.status === 401) {
          window.location.href = "/login";
          return;
        }
        const data = await response.json().catch(() => null);
        if (!response.ok || !data?.ok) return;

        const prevAsistencia = asistenciaHabilitada;
        asistenciaHabilitada = Boolean(data.asistencia_habilitada);
        if (prevAsistencia !== asistenciaHabilitada) {
          window.location.reload();
          return;
        }

        // Conservar valores de selects en edición local no es crítico; re-render limpio
        const focused = document.activeElement;
        const editingSelect =
          focused instanceof HTMLSelectElement && focused.classList.contains("select-asistencia");
        if (editingSelect) return;

        renderFilas(data.estudiantes || []);
        actualizarStats(data.total ?? 0, data.asistentes ?? 0);
      } catch (err) {
        /* silencio: el panel sigue usable offline temporalmente */
      }
    };

    if (buscador) {
      buscador.addEventListener("input", aplicarFiltro);
    }

    document.querySelectorAll(".filtro-tipo").forEach((btn) => {
      btn.addEventListener("click", () => {
        filtroTipo = btn.dataset.tipo || "todos";
        document.querySelectorAll(".filtro-tipo").forEach((b) => {
          const active = b === btn;
          b.classList.toggle("is-active", active);
          b.classList.toggle("btn-primary", active);
          b.classList.toggle("btn-outline-primary", !active);
        });
        aplicarFiltro();
      });
    });

    const modalEliminar = document.getElementById("modal-eliminar");
    const textoEliminar = document.getElementById("modal-eliminar-texto");
    const btnConfirmarEliminar = document.getElementById("btn-confirmar-eliminar");

    const openDeleteModal = (id, nombre) => {
      pendingDeleteId = id;
      if (textoEliminar) {
        textoEliminar.textContent = `¿Seguro que desea eliminar a ${nombre}? Esta acción no se puede deshacer.`;
      }
      if (modalEliminar) {
        modalEliminar.hidden = false;
        modalEliminar.classList.add("is-open");
      }
    };

    const closeDeleteModal = () => {
      pendingDeleteId = null;
      if (modalEliminar) {
        modalEliminar.classList.remove("is-open");
        modalEliminar.hidden = true;
      }
    };

    tbody.addEventListener("click", (event) => {
      const btn = event.target instanceof Element ? event.target.closest(".btn-eliminar") : null;
      if (!btn) return;
      openDeleteModal(btn.dataset.id, btn.dataset.nombre || "este asistente");
    });

    document.querySelectorAll("[data-close-confirm]").forEach((el) => {
      el.addEventListener("click", closeDeleteModal);
    });

    if (btnConfirmarEliminar) {
      btnConfirmarEliminar.addEventListener("click", async () => {
        const id = pendingDeleteId;
        if (!id) return;
        btnConfirmarEliminar.disabled = true;
        try {
          const response = await fetch("/logistica/eliminar", {
            method: "POST",
            headers: {
              Accept: "application/json",
              "Content-Type": "application/json",
              "X-Requested-With": "XMLHttpRequest",
            },
            body: JSON.stringify({ id: Number(id), _csrf: csrf.value }),
          });
          const data = await response.json().catch(() => null);
          if (!response.ok || !data?.ok) {
            mostrarToast(data?.mensaje || "No se pudo eliminar el registro.", true);
            if (response.status === 401) window.location.href = "/login";
            return;
          }
          closeDeleteModal();
          mostrarToast(data.mensaje || "Registro eliminado.");
          const row = tbody.querySelector(`tr[data-id="${id}"]`);
          if (row) row.remove();
          const totalEl = document.getElementById("stat-total");
          if (totalEl) {
            const n = tbody.querySelectorAll("tr:not(.empty-row)").length;
            totalEl.textContent = String(n);
          }
        } catch (err) {
          mostrarToast("Error de red al eliminar.", true);
        } finally {
          btnConfirmarEliminar.disabled = false;
        }
      });
    }

    tabla.addEventListener("change", async (event) => {
      const select = event.target;
      if (!(select instanceof HTMLSelectElement) || !select.classList.contains("select-asistencia")) {
        return;
      }

      if (select.disabled) return;

      const id = select.dataset.id;
      const asistio = select.value === "1";
      const prev = "0";

      // Solo se permite confirmar (Sí). Si vuelve a No, revertir.
      if (!asistio) {
        select.value = prev;
        return;
      }

      select.disabled = true;

      try {
        const response = await fetch("/logistica/asistencia", {
          method: "POST",
          headers: {
            Accept: "application/json",
            "Content-Type": "application/json",
            "X-Requested-With": "XMLHttpRequest",
          },
          body: JSON.stringify({
            id: Number(id),
            asistio: true,
            _csrf: csrf.value,
          }),
        });

        const data = await response.json().catch(() => null);
        if (!response.ok || !data?.ok) {
          select.value = prev;
          select.disabled = false;
          mostrarToast(data?.mensaje || "No se pudo actualizar.", true);
          if (response.status === 401) window.location.href = "/login";
          return;
        }

        // Bloquear definitivamente el select
        select.innerHTML = '<option value="1" selected>Sí</option>';
        select.disabled = true;
        select.title = "Asistencia confirmada: no se puede modificar";

        const cell = select.closest("tr")?.querySelector(".cell-cert");
        if (cell) {
          cell.innerHTML = `<a class="btn btn-sm btn-outline-primary" href="/certificados/${id}" target="_blank" rel="noopener">Ver</a>`;
        }

        const asisEl = document.getElementById("stat-asistentes");
        if (asisEl) {
          const count = [...tbody.querySelectorAll(".select-asistencia")].filter((s) => s.value === "1").length;
          asisEl.textContent = String(count);
        }

        mostrarToast(data.mensaje);
      } catch (err) {
        select.value = prev;
        select.disabled = false;
        mostrarToast("Error de red al actualizar.", true);
      }
    });

    setInterval(refrescarLista, 15000);
  }
})();
