# II Foro de Tecnologías Emergentes: Inteligencia Artificial

Aplicación web MVC en PHP nativo + PostgreSQL para registro de participantes, control de asistencia y emisión de certificados digitales.

**Fecha del evento:** 06 de noviembre de 2026 · 8:00 AM

## Requisitos

- PHP 8.1+ con extensión `pdo_pgsql`
- PostgreSQL 13+
- Apache con `mod_rewrite` **o** servidor embebido de PHP

## Instalación rápida

### 1. Clonar el repositorio

```bash
git clone <URL_DEL_REPO>
cd foro-tecnologias-emergentes
```

### 2. Base de datos (copia incluida)

En la **raíz del proyecto** está el archivo `foro_backup.sql` (volcado completo de la BD).

```bash
# Crear la base (nombre en minúsculas en PostgreSQL)
psql -U postgres -c "CREATE DATABASE foro;"

# Restaurar esquema + datos
psql -U postgres -d foro -f foro_backup.sql
```

Si prefieres solo el esquema vacío (sin datos):

```bash
psql -U postgres -d foro -f database/schema.sql
```

### 3. Configuración

Edite `config/database.php` según su entorno:

| Variable / clave | Valor de ejemplo |
|------------------|------------------|
| host             | localhost        |
| port             | 5432             |
| dbname           | foro             |
| user             | postgres         |
| password         | *su contraseña*  |

Credenciales de logística (`config/auth.php`):

| Campo        | Valor por defecto |
|--------------|-------------------|
| Usuario      | `logistica`       |
| Contraseña   | `foro2026`        |

> En modo prueba, `forzar_asistencia` está en `true` para poder marcar asistencia y generar certificados sin esperar al 06/11/2026. Cámbielo a `null` en producción.

### 4. Ejecutar

```bash
cd public
php -S localhost:8080 router.php
```

Abra http://localhost:8080

| Quién        | URL                          |
|--------------|------------------------------|
| Estudiantes  | http://localhost:8080/registro |
| Logística    | http://localhost:8080/login    |

## Módulos

- **Registro público** — inscripción de participantes
- **Logística** (login) — listado en vivo + select ¿Vino? (bloqueado al confirmar)
- **Certificados** — vista imprimible y descarga PDF

## Estructura

```
foro_backup.sql      # Copia de la base de datos (raíz)
config/              # database.php, auth.php
app/controllers/
app/models/
app/views/
app/core/
public/              # Front controller, CSS, JS, imágenes
database/schema.sql  # Esquema vacío
```

## Seguridad

- PDO + prepared statements
- CSRF en formularios y AJAX
- Validación cliente/servidor
- Constraints en PostgreSQL
- Asistencia irreversible una vez marcada como "Sí"
