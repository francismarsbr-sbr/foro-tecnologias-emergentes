--
-- PostgreSQL database dump
--

\restrict 18a7kcVARtyqY0s6vS7abC6Fiehmd7zFgUEjc51BghwdVXXfSOSNA2YOeBhdcbN

-- Dumped from database version 18.1
-- Dumped by pg_dump version 18.1

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET transaction_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: estudiantes; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.estudiantes (
    id integer NOT NULL,
    cedula character varying(10) NOT NULL,
    nombre character varying(30) NOT NULL,
    apellido character varying(30) NOT NULL,
    fecha_nacimiento date NOT NULL,
    edad integer NOT NULL,
    correo character varying(150) NOT NULL,
    telefono character varying(15) NOT NULL,
    asistio boolean DEFAULT false NOT NULL,
    fecha_registro timestamp without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL,
    institution_name character varying(150) DEFAULT 'No indicó institución'::character varying NOT NULL,
    institution_type character varying(20) DEFAULT 'no_indico'::character varying NOT NULL,
    CONSTRAINT chk_estudiantes_apellido CHECK ((((char_length(TRIM(BOTH FROM apellido)) >= 2) AND (char_length(TRIM(BOTH FROM apellido)) <= 30)) AND ((apellido)::text ~ '^[[:alpha:]''\-]+$'::text))),
    CONSTRAINT chk_estudiantes_cedula CHECK (((cedula)::text ~ '^[0-9]{6,10}$'::text)),
    CONSTRAINT chk_estudiantes_correo CHECK (((correo)::text ~* '^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$'::text)),
    CONSTRAINT chk_estudiantes_nombre CHECK ((((char_length(TRIM(BOTH FROM nombre)) >= 2) AND (char_length(TRIM(BOTH FROM nombre)) <= 30)) AND ((nombre)::text ~ '^[[:alpha:]''\-]+$'::text))),
    CONSTRAINT chk_estudiantes_telefono CHECK (((telefono)::text ~ '^[0-9]{7,15}$'::text)),
    CONSTRAINT estudiantes_edad_check CHECK (((edad >= 0) AND (edad <= 120)))
);


--
-- Name: TABLE estudiantes; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE public.estudiantes IS 'Participantes registrados al II Foro de Tecnologías Emergentes';


--
-- Name: COLUMN estudiantes.asistio; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.estudiantes.asistio IS 'TRUE cuando logística confirma asistencia el 06/11/2026';


--
-- Name: estudiantes_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.estudiantes_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: estudiantes_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.estudiantes_id_seq OWNED BY public.estudiantes.id;


--
-- Name: estudiantes id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.estudiantes ALTER COLUMN id SET DEFAULT nextval('public.estudiantes_id_seq'::regclass);


--
-- Data for Name: estudiantes; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.estudiantes (id, cedula, nombre, apellido, fecha_nacimiento, edad, correo, telefono, asistio, fecha_registro, institution_name, institution_type) FROM stdin;
1	28259274	Francismar	Sanblas	2000-08-15	26	francismar.sbr@gmail.com	04126778846	t	2026-10-19 20:01:00.654458	No indicó institución	no_indico
3	29999111	Carla	Perez	1998-05-12	28	carla.perez.test@example.com	04145551234	f	2026-10-29 12:45:55.113864	No indicó institución	no_indico
4	17458428	Nerson	Gimenez	1985-03-19	41	nersongimenez@gmail.com	04121541653	f	2026-10-29 13:19:20.04589	No indicó institución	no_indico
\.


--
-- Name: estudiantes_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.estudiantes_id_seq', 4, true);


--
-- Name: estudiantes estudiantes_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.estudiantes
    ADD CONSTRAINT estudiantes_pkey PRIMARY KEY (id);


--
-- Name: estudiantes uq_estudiantes_cedula; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.estudiantes
    ADD CONSTRAINT uq_estudiantes_cedula UNIQUE (cedula);


--
-- Name: idx_estudiantes_apellido; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_estudiantes_apellido ON public.estudiantes USING btree (apellido);


--
-- Name: idx_estudiantes_asistio; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_estudiantes_asistio ON public.estudiantes USING btree (asistio);


--
-- Name: idx_estudiantes_cedula; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_estudiantes_cedula ON public.estudiantes USING btree (cedula);


--
-- Name: idx_estudiantes_institution_type; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_estudiantes_institution_type ON public.estudiantes USING btree (institution_type);


--
-- PostgreSQL database dump complete
--

\unrestrict 18a7kcVARtyqY0s6vS7abC6Fiehmd7zFgUEjc51BghwdVXXfSOSNA2YOeBhdcbN

