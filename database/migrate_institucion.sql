-- Agrega procedencia (institución / empresa) a asistentes existentes
BEGIN;

ALTER TABLE estudiantes
    ADD COLUMN IF NOT EXISTS institution_name VARCHAR(150);

ALTER TABLE estudiantes
    ADD COLUMN IF NOT EXISTS institution_type VARCHAR(20);

UPDATE estudiantes
SET institution_name = 'No indicó institución'
WHERE institution_name IS NULL
   OR btrim(institution_name) = '';

UPDATE estudiantes
SET institution_type = 'no_indico'
WHERE institution_type IS NULL
   OR btrim(institution_type) = '';

ALTER TABLE estudiantes
    ALTER COLUMN institution_name SET DEFAULT 'No indicó institución';

ALTER TABLE estudiantes
    ALTER COLUMN institution_type SET DEFAULT 'no_indico';

ALTER TABLE estudiantes
    ALTER COLUMN institution_name SET NOT NULL;

ALTER TABLE estudiantes
    ALTER COLUMN institution_type SET NOT NULL;

CREATE INDEX IF NOT EXISTS idx_estudiantes_institution_type ON estudiantes (institution_type);

COMMIT;
