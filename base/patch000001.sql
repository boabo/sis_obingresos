/********************************************I-SCP-JRR-OBINGRESOS-0-10/12/2015********************************************/
CREATE TABLE obingresos.tcomprobante (
  id_comprobante SERIAL NOT NULL,
  pais VARCHAR(3) NOT NULL,
  comprobante VARCHAR(50),
  glosa TEXT,
  estado VARCHAR(1),
  fecha_reg DATE,
  retbsp VARCHAR(15),
  momento INTEGER,
  estacion CHAR(3),
  moneda_estacion CHAR(3),
  fecha_ini DATE,
  fecha_fin DATE,
  tipo_ingreso VARCHAR(15),
  cliente VARCHAR(100),
  id_int_comprobante INTEGER,
  CONSTRAINT tconta_encabezado_pkey PRIMARY KEY(id_comprobante)
);

CREATE TABLE obingresos.tcomprobante_det (
  id_comprobante_det SERIAL NOT NULL,
  id_comprobante INTEGER NOT NULL,
  pais VARCHAR(3),
  renglon INTEGER,
  id_presupuesto INTEGER,
  id_partida INTEGER,
  id_cuenta INTEGER,
  id_auxiliar INTEGER,
  tipo_movimiento VARCHAR(10),
  importe NUMERIC(18,2),
  importe_mb NUMERIC(18,2),
  importe_mt NUMERIC(18,2),
  tc_mb NUMERIC(18,2),
  tc_mt NUMERIC(18,2),
  moneda_transaccion VARCHAR(3),
  fecha_ven_ini DATE,
  fecha_ven_fin DATE,
  CONSTRAINT tcomprobante_det_pkey PRIMARY KEY(id_comprobante_det)
);

CREATE TABLE obingresos.tagencia (
  id_agencia SERIAL NOT NULL,
  codigo VARCHAR(20) NOT NULL,
  codigo_noiata VARCHAR(20),
  codigo_int VARCHAR(20),
  nombre VARCHAR(255) NOT NULL,
  tipo_agencia VARCHAR(25) NOT NULL,
  tipo_pago VARCHAR(25) NOT NULL,
  monto_maximo_deuda NUMERIC(18,2) NOT NULL,
  depositos_moneda_boleto VARCHAR(2) NOT NULL,
  tipo_cambio VARCHAR(10) NOT NULL,
  id_moneda_control INTEGER NOT NULL,
  CONSTRAINT tagencia_pkey PRIMARY KEY(id_agencia)
)
  INHERITS (pxp.tbase) WITHOUT OIDS;

CREATE SEQUENCE obingresos.tboleto_id_boleto_seq
INCREMENT 1 MINVALUE 1
MAXVALUE 9223372036854775807 START 1
CACHE 1;

CREATE TABLE obingresos.tboleto (
  id_boleto INTEGER DEFAULT nextval('obingresos.tboleto_id_boleto_seq'::regclass) NOT NULL,
  nro_boleto VARCHAR(50) NOT NULL,
  pasajero VARCHAR(100) NOT NULL,
  fecha_emision DATE NOT NULL,
  total NUMERIC(18,2) NOT NULL,
  comision NUMERIC(18,2),
  liquido NUMERIC(18,2) NOT NULL,
  id_moneda_boleto INTEGER NOT NULL,
  monto_pagado_moneda_boleto NUMERIC(18,2) NOT NULL,
  id_agencia INTEGER NOT NULL,
  moneda VARCHAR(5),
  agt VARCHAR(20),
  agtnoiata VARCHAR(20),
  neto NUMERIC(18,2) NOT NULL,
  CONSTRAINT tboleto_pkey PRIMARY KEY(id_boleto)
)
  INHERITS (pxp.tbase) WITHOUT OIDS;


CREATE TABLE obingresos.tdeposito (
  id_deposito SERIAL NOT NULL,
  nro_deposito VARCHAR(70) NOT NULL,
  monto_deposito NUMERIC(18,2) NOT NULL,
  id_moneda_deposito INTEGER,
  id_agencia INTEGER ,
  fecha DATE NOT NULL,
  saldo NUMERIC(18,2) NOT NULL,
  moneda VARCHAR(5),
  agt VARCHAR(20),
  agtnoiata VARCHAR(20),
  fecini DATE,
  fecfin DATE,
  observaciones TEXT,
  id_cuenta_bancaria INTEGER,
  CONSTRAINT tdeposito_pkey PRIMARY KEY(id_deposito)
)
  INHERITS (pxp.tbase) WITHOUT OIDS;




CREATE TABLE obingresos.tdeposito_boleto (
  id_deposito_boleto SERIAL NOT NULL,
  id_deposito INTEGER NOT NULL,
  id_boleto INTEGER NOT NULL,
  monto_moneda_boleto NUMERIC(18,2) NOT NULL,
  monto_moneda_deposito NUMERIC(18,2) NOT NULL,
  tc NUMERIC(12,6) NOT NULL,
  CONSTRAINT tdeposito_boleto_pkey PRIMARY KEY(id_deposito_boleto)
)
  INHERITS (pxp.tbase) WITHOUT OIDS;



/********************************************F-SCP-JRR-OBINGRESOS-0-10/12/2015********************************************/

/********************************************I-SCP-JRR-OBINGRESOS-0-02/02/2016********************************************/

ALTER TABLE obingresos.tagencia
ADD COLUMN agencia_ato CHAR(1) DEFAULT 'N' NOT NULL;

ALTER TABLE obingresos.tboleto
ADD COLUMN gds VARCHAR(5);

ALTER TABLE obingresos.tboleto
ADD COLUMN tipdoc VARCHAR(5) NOT NULL;

ALTER TABLE obingresos.tboleto
ADD COLUMN ruta VARCHAR(2);

ALTER TABLE obingresos.tboleto
ADD COLUMN cupones INTEGER NOT NULL;

ALTER TABLE obingresos.tboleto
ADD COLUMN origen VARCHAR(10);

ALTER TABLE obingresos.tboleto
ADD COLUMN destino varchar(10);

ALTER TABLE obingresos.tboleto
ADD COLUMN tipopax varchar(5);

ALTER TABLE obingresos.tboleto
ADD COLUMN retbsp VARCHAR(5) NOT NULL;

ALTER TABLE obingresos.tboleto
ADD COLUMN estado VARCHAR(20);

ALTER TABLE obingresos.tboleto
ADD COLUMN endoso VARCHAR(255);

CREATE TABLE obingresos.testacion (
  id_estacion SERIAL NOT NULL,
  codigo VARCHAR(20) NOT NULL,
  nombre VARCHAR(100) NOT NULL,
  id_lugar INTEGER NOT NULL,
  tipo_pais VARCHAR(5),
  CONSTRAINT testacion_pkey PRIMARY KEY(id_estacion)
)
  INHERITS (pxp.tbase) WITHOUT OIDS;


CREATE TABLE obingresos.tcomision (
  id_comision SERIAL NOT NULL,
  id_lugar INTEGER NOT NULL,
  porcentaje NUMERIC(5,2) NOT NULL,
  tipodoc varchar(20) NOT NULL,
  codigo VARCHAR(50),
  nombre VARCHAR(150),
  CONSTRAINT tcomision_pkey PRIMARY KEY(id_comision)
)
  INHERITS (pxp.tbase) WITHOUT OIDS;

CREATE TABLE obingresos.timpuesto (
  id_impuesto SERIAL NOT NULL,
  id_lugar INTEGER NOT NULL,
  porcentaje NUMERIC(5,2) NOT NULL,
  tipodoc varchar(20) NOT NULL,
  codigo VARCHAR(50),
  nombre VARCHAR(150),
  monto numeric(18,2),
  tipo varchar(5),
  CONSTRAINT timpuesto_pkey PRIMARY KEY(id_impuesto)
)
  INHERITS (pxp.tbase) WITHOUT OIDS;

CREATE TABLE obingresos.tforma_pago (
  id_forma_pago SERIAL NOT NULL,
  codigo VARCHAR(20) NOT NULL,
  nombre VARCHAR(100) NOT NULL,
  id_moneda INTEGER NOT NULL,
  id_lugar INTEGER NOT NULL,
  ctacte VARCHAR(2) NOT NULL,
  CONSTRAINT tforma_pago_pkey PRIMARY KEY(id_forma_pago)
)
  INHERITS (pxp.tbase) WITHOUT OIDS;

CREATE TABLE obingresos.tboleto_comision (
  id_boleto_comision SERIAL NOT NULL,
  importe NUMERIC(18,2) NOT NULL,
  id_comision INTEGER NOT NULL,
  id_boleto INTEGER NOT NULL,
  CONSTRAINT tboleto_comision_pkey PRIMARY KEY(id_boleto_comision)
)
  INHERITS (pxp.tbase) WITHOUT OIDS;

CREATE TABLE obingresos.tboleto_impuesto (
  id_boleto_impuesto SERIAL NOT NULL,
  importe NUMERIC(18,2) NOT NULL,
  id_impuesto INTEGER NOT NULL,
  id_boleto INTEGER NOT NULL,
  CONSTRAINT tboleto_impuesto_pkey PRIMARY KEY(id_boleto_impuesto)
)
  INHERITS (pxp.tbase) WITHOUT OIDS;

CREATE TABLE obingresos.tboleto_forma_pago (
  id_boleto_forma_pago SERIAL NOT NULL,
  importe NUMERIC(18,2) NOT NULL,
  id_forma_pago INTEGER NOT NULL,
  id_boleto INTEGER NOT NULL,
  tipo VARCHAR(20),
  tarjeta VARCHAR(6) ,
  numero_tarjeta VARCHAR(20),
  ctacte VARCHAR(20),
  CONSTRAINT tboleto_forma_pago_pkey PRIMARY KEY(id_boleto_forma_pago)
)
  INHERITS (pxp.tbase) WITHOUT OIDS;

/********************************************F-SCP-JRR-OBINGRESOS-0-02/02/2016********************************************/

/********************************************I-SCP-JRR-OBINGRESOS-0-08/04/2016********************************************/

CREATE TABLE obingresos.tperiodo_venta (
  id_periodo_venta SERIAL NOT NULL,
  mes VARCHAR(30) NOT NULL,
  nro_periodo_mes INTEGER NOT NULL,
  fecha_ini DATE NOT NULL,
  fecha_fin DATE NOT NULL,
  tipo VARCHAR(20) NOT NULL,
  estado VARCHAR (15) NOT NULL,
  id_pais	INTEGER NOT NULL,
  id_gestion INTEGER NOT NULL,
  CONSTRAINT tperiodo_venta_pkey PRIMARY KEY(id_periodo_venta)
)
  INHERITS (pxp.tbase) WITHOUT OIDS;

ALTER TABLE obingresos.tagencia
ADD COLUMN id_lugar INTEGER NOT NULL;

ALTER TABLE obingresos.tagencia
ADD COLUMN boaagt VARCHAR(2);

CREATE TABLE obingresos.taeropuerto (
  id_aeropuerto SERIAL NOT NULL,
  codigo VARCHAR(5) NOT NULL,
  id_lugar INTEGER NOT NULL,
  nombre VARCHAR(100) NOT NULL,
  tipo_nalint VARCHAR(1) NOT NULL,
  PRIMARY KEY(id_aeropuerto)
) INHERITS (pxp.tbase)
;

ALTER TABLE obingresos.tboleto
ADD COLUMN tc NUMERIC(18,7);

ALTER TABLE obingresos.tboleto
ADD COLUMN moneda_sucursal VARCHAR(3);

ALTER TABLE obingresos.tboleto
ADD COLUMN id_usuario_cajero INTEGER;

ALTER TABLE obingresos.tboleto
ADD COLUMN id_punto_venta INTEGER;

ALTER TABLE obingresos.tboleto
ADD COLUMN ruta_completa VARCHAR(255);
/********************************************F-SCP-JRR-OBINGRESOS-0-08/04/2016********************************************/

/********************************************I-SCP-JRR-OBINGRESOS-0-19/07/2016********************************************/
CREATE TABLE obingresos.tboleto_vuelo (
  id_boleto_vuelo SERIAL NOT NULL,
  id_boleto INTEGER NOT NULL,
  fecha DATE NOT NULL,
  hora_origen TIME(0) WITHOUT TIME ZONE NOT NULL,
  hora_destino TIME(0) WITHOUT TIME ZONE,
  vuelo VARCHAR(7) NOT NULL,
  id_aeropuerto_origen INTEGER NOT NULL,
  id_aeropuerto_destino INTEGER NOT NULL,
  tarifa VARCHAR(7) NOT NULL,
  equipaje VARCHAR(7) NOT NULL,
  status VARCHAR(20) NOT NULL,
  id_boleto_conjuncion INTEGER,
  CONSTRAINT tboleto_vuelo_pkey PRIMARY KEY(id_boleto_vuelo)
) INHERITS (pxp.tbase);

ALTER TABLE obingresos.tboleto
ADD COLUMN tiene_conjuncion VARCHAR(2);

ALTER TABLE obingresos.tboleto
ADD COLUMN id_boleto_conjuncion INTEGER;

ALTER TABLE obingresos.tboleto
ADD COLUMN localizador VARCHAR(10);

ALTER TABLE obingresos.tboleto
ADD COLUMN identificacion VARCHAR(30);

ALTER TABLE obingresos.tboleto
ADD COLUMN xt NUMERIC(18,2) DEFAULT 0 NOT NULL;

ALTER TABLE obingresos.tboleto
ADD COLUMN mensaje_error TEXT;

ALTER TABLE obingresos.tboleto_impuesto
ADD COLUMN calculo_tarifa VARCHAR(2) DEFAULT 'no' NOT NULL;


/********************************************F-SCP-JRR-OBINGRESOS-0-19/07/2016********************************************/

/********************************************I-SCP-JRR-OBINGRESOS-0-30/09/2016********************************************/

ALTER TABLE obingresos.tboleto_forma_pago
ADD COLUMN codigo_tarjeta VARCHAR(20);

/********************************************F-SCP-JRR-OBINGRESOS-0-30/09/2016********************************************/

/********************************************I-SCP-JRR-OBINGRESOS-0-09/11/2016********************************************/

CREATE INDEX tboleto_forma_pago__id_boleto_idx ON obingresos.tboleto_forma_pago
USING btree (id_boleto);

CREATE INDEX tboleto_impuesto__id_boleto_idx ON obingresos.tboleto_impuesto
USING btree (id_boleto);

CREATE INDEX tboleto_vuelo__id_boleto_idx ON obingresos.tboleto_vuelo
USING btree (id_boleto);

/********************************************F-SCP-JRR-OBINGRESOS-0-09/11/2016********************************************/

/********************************************I-SCP-JRR-OBINGRESOS-0-14/11/2016********************************************/

CREATE TABLE obingresos.tdetalle_boletos_web (
  billete VARCHAR(15) NOT NULL,
  conjuncion VARCHAR(255),
  medio_pago VARCHAR(50) NOT NULL,
  entidad_pago VARCHAR(50) NOT NULL,
  moneda VARCHAR(3) NOT NULL,
  importe NUMERIC(18,2) NOT NULL,
  endoso VARCHAR(500),
  procesado VARCHAR(2) DEFAULT 'no'::character varying NOT NULL,
  origen VARCHAR(20) DEFAULT 'servicio'::character varying,
  id_detalle_boletos_web SERIAL,
  fecha DATE NOT NULL,
  nit VARCHAR(30),
  razon_social VARCHAR(300),
  void VARCHAR(2) DEFAULT 'no'::character varying,
  CONSTRAINT tdetalle_boletos_web_billete_key UNIQUE(billete),
  CONSTRAINT tdetalle_boletos_web_pkey PRIMARY KEY(id_detalle_boletos_web)
) INHERITS (pxp.tbase)

WITH (oids = false);

ALTER TABLE obingresos.tdetalle_boletos_web
ALTER COLUMN origen SET STATISTICS 0;

ALTER TABLE obingresos.tdetalle_boletos_web
ALTER COLUMN id_detalle_boletos_web SET STATISTICS 0;

ALTER TABLE obingresos.tdetalle_boletos_web
ALTER COLUMN fecha SET STATISTICS 0;

CREATE TYPE obingresos.detalle_boletos AS (
  "Billete" VARCHAR(15),
  "CNJ" VARCHAR(255),
  "MedioDePago" VARCHAR(50),
  "Entidad" VARCHAR(50),
  "Moneda" VARCHAR(3),
  "ImportePasaje" NUMERIC(18,2),
  "ImporteTarifa" NUMERIC(18,2),
  "OrigenDestino" VARCHAR(255),
  endoso VARCHAR(500)
);

ALTER TABLE obingresos.tboleto
ADD COLUMN nit_ingresos VARCHAR(20);

ALTER TABLE obingresos.tboleto
ADD COLUMN razon_ingresos VARCHAR(300);


/********************************************F-SCP-JRR-OBINGRESOS-0-14/11/2016********************************************/

/********************************************I-SCP-JRR-OBINGRESOS-0-23/11/2016********************************************/

CREATE UNIQUE INDEX tforma_pago_idx ON obingresos.tforma_pago
USING btree (codigo COLLATE pg_catalog."default", id_lugar, id_moneda);

ALTER TABLE obingresos.tboleto
ADD COLUMN voided VARCHAR(2) DEFAULT 'no'::character varying NOT NULL;

ALTER TABLE obingresos.tboleto
ADD COLUMN nit BIGINT;

ALTER TABLE obingresos.tboleto
ADD COLUMN razon VARCHAR(200);

ALTER TABLE obingresos.tboleto
ADD COLUMN medio_pago VARCHAR(20);

ALTER TABLE obingresos.tboleto_vuelo
ADD COLUMN cupon SMALLINT NOT NULL;

ALTER TABLE obingresos.tboleto
ALTER COLUMN id_moneda_boleto DROP NOT NULL;

ALTER TABLE obingresos.tboleto
ALTER COLUMN pasajero DROP NOT NULL;

ALTER TABLE obingresos.tboleto_vuelo
ADD COLUMN linea VARCHAR(5);

ALTER TABLE obingresos.tboleto_vuelo
ADD COLUMN aeropuerto_origen VARCHAR(10);

ALTER TABLE obingresos.tboleto_vuelo
ADD COLUMN aeropuerto_destino VARCHAR(10);

ALTER TABLE obingresos.tboleto_vuelo
ALTER COLUMN tarifa TYPE VARCHAR(15) COLLATE pg_catalog."default";

ALTER TABLE obingresos.tboleto_vuelo
ALTER COLUMN hora_origen DROP NOT NULL;

ALTER TABLE obingresos.tboleto_vuelo
ALTER COLUMN fecha DROP NOT NULL;

ALTER TABLE obingresos.tboleto
ALTER COLUMN localizador TYPE VARCHAR(13) COLLATE pg_catalog."default";
/********************************************F-SCP-JRR-OBINGRESOS-0-23/11/2016********************************************/

/********************************************I-SCP-MAM-OBINGRESOS-0-25/11/2016********************************************/
ALTER TABLE obingresos.tdeposito
ADD COLUMN descripcion TEXT;

ALTER TABLE obingresos.tdeposito
ADD COLUMN pnr VARCHAR(20);

/********************************************F-SCP-MAM-OBINGRESOS-0-25/11/2016********************************************/

/********************************************I-SCP-JRR-OBINGRESOS-0-25/11/2016********************************************/
CREATE UNIQUE INDEX tdeposito_idx ON obingresos.tdeposito
USING btree (nro_deposito COLLATE pg_catalog."default", tipo COLLATE pg_catalog."default");

CREATE INDEX tdeposito_idx1 ON obingresos.tdeposito
USING btree (pnr);

ALTER TABLE obingresos.tdeposito
ADD COLUMN tipo VARCHAR(15) DEFAULT 'agencia' NOT NULL;

/********************************************F-SCP-JRR-OBINGRESOS-0-25/11/2016********************************************/

/********************************************I-SCP-JRR-OBINGRESOS-0-19/01/2017********************************************/
CREATE TABLE obingresos.tventa_web_modificaciones (
  nro_boleto VARCHAR(20) NOT NULL,
  tipo VARCHAR(15),
  nro_boleto_reemision VARCHAR(20),
  used VARCHAR(2),
  motivo TEXT NOT NULL,
  id_venta_web_modificaciones SERIAL,
  procesado VARCHAR(2) DEFAULT 'no'::character varying NOT NULL,
  CONSTRAINT tventa_web_modificaciones_nro_boleto_key UNIQUE(nro_boleto),
  CONSTRAINT tventa_web_modificaciones_pkey PRIMARY KEY(id_venta_web_modificaciones),
  CONSTRAINT tventa_web_modificaciones_chk CHECK (((tipo)::text = 'tsu_anulado'::text) OR ((tipo)::text = 'anulado'::text) OR ((tipo)::text = 'reemision'::text))
) INHERITS (pxp.tbase)

WITH (oids = false);

CREATE UNIQUE INDEX tventa_web_modificaciones_idx ON obingresos.tventa_web_modificaciones
USING btree (nro_boleto_reemision COLLATE pg_catalog."default")
  WHERE ((nro_boleto_reemision IS NOT NULL) AND ((nro_boleto_reemision)::text <> ''::text));

CREATE TABLE obingresos.tboleto_retweb (
  id_boleto_retweb SERIAL,
  nro_boleto VARCHAR(20) NOT NULL,
  pasajero VARCHAR(100),
  fecha_emision DATE NOT NULL,
  total NUMERIC(18,2),
  moneda VARCHAR(5),
  tarjeta VARCHAR(5) NOT NULL,
  numero_tarjeta VARCHAR(30),
  estado VARCHAR(2) NOT NULL,
  CONSTRAINT tboleto_retweb_pkey PRIMARY KEY(id_boleto_retweb)
) INHERITS (pxp.tbase)

WITH (oids = false);

ALTER TABLE obingresos.tboleto_retweb
ALTER COLUMN nro_boleto SET STATISTICS 0;

CREATE INDEX tboleto_retweb_idx ON obingresos.tboleto_retweb
USING btree (fecha_emision);

CREATE UNIQUE INDEX tboleto_retweb_idx1 ON obingresos.tboleto_retweb
USING btree (nro_boleto COLLATE pg_catalog."default");

/********************************************F-SCP-JRR-OBINGRESOS-0-19/01/2017********************************************/


/********************************************I-SCP-FFP-OBINGRESOS-0-15/02/2017********************************************/

CREATE TABLE obingresos.tskybiz_archivo (
  id_skybiz_archivo SERIAL,
  nombre_archivo VARCHAR(255),
  subido VARCHAR(255),
  comentario VARCHAR(255),
  fecha DATE,
  PRIMARY KEY(id_skybiz_archivo)
) INHERITS (pxp.tbase)
WITH (oids = false);




CREATE TYPE obingresos.json_ins_skybiz_archivo AS (
  nombre_archivo VARCHAR(255),
  subido VARCHAR(255),
  comentario VARCHAR(255),
  moneda VARCHAR(255)
);

ALTER TABLE obingresos.tskybiz_archivo ADD moneda VARCHAR(255) NULL;


CREATE TABLE obingresos.tskybiz_archivo_detalle (
  id_skybiz_archivo_detalle SERIAL,
  id_skybiz_archivo INTEGER,
  entity VARCHAR(255),
  ip VARCHAR(255),
  request_date_time VARCHAR(255),
  issue_date_time VARCHAR(255),
  pnr VARCHAR(255),
  identifier_pnr VARCHAR(255),
  authorization_ VARCHAR(255),
  total_amount NUMERIC(10,2),
  currency VARCHAR(255),
  status VARCHAR(255),
  PRIMARY KEY(id_skybiz_archivo_detalle)
) INHERITS (pxp.tbase)
WITH (oids = false);


CREATE TYPE obingresos.json_ins_skybiz_archivo_detalle AS (
  entity                    VARCHAR(255),
  ip                        VARCHAR(255),
  request_date_time         VARCHAR(255),
  issue_date_time           VARCHAR(255),
  pnr                       VARCHAR(255),
  identifier_pnr            VARCHAR(255),
  authorization_            VARCHAR(255),
  total_amount              VARCHAR(255),
  currency                  VARCHAR(255),
  status                    VARCHAR(255),
  nombre_archivo            VARCHAR(255)
);


/********************************************F-SCP-FFP-OBINGRESOS-0-15/02/2017********************************************/

/********************************************I-SCP-JRR-OBINGRESOS-0-01/03/2017********************************************/
DROP INDEX obingresos.tdeposito_idx;

CREATE INDEX tdeposito_idx ON obingresos.tdeposito
  USING btree (nro_deposito COLLATE pg_catalog."default", tipo COLLATE pg_catalog."default");

/********************************************F-SCP-JRR-OBINGRESOS-0-01/03/2017********************************************/

/********************************************I-SCP-FFP-OBINGRESOS-0-01/03/2017********************************************/

ALTER TABLE param.tcolumnas_archivo_excel ALTER COLUMN formato_fecha TYPE VARCHAR(20) USING formato_fecha::VARCHAR(20);

DROP TYPE obingresos.json_ins_skybiz_archivo_detalle;
CREATE TYPE obingresos.json_ins_skybiz_archivo_detalle AS (
  entity                    VARCHAR(255),
  ip                        VARCHAR(255),
  request_date_time         TIMESTAMP,
  issue_date_time           TIMESTAMP,
  pnr                       VARCHAR(255),
  identifier_pnr            VARCHAR(255),
  authorization_            VARCHAR(255),
  total_amount              VARCHAR(255),
  currency                  VARCHAR(255),
  status                    VARCHAR(255),
  nombre_archivo            VARCHAR(255)
);

ALTER TABLE obingresos.tskybiz_archivo ADD banco VARCHAR(255) NULL;

select pxp.f_insert_tgui ('Skybiz', 'Skybiz', 'SKYBIZ', 'si', 8, 'sis_obingresos/vista/skybiz_archivo/SkybizArchivo.php', 2, '', 'SkybizArchivo', 'OBINGRESOS');
select pxp.f_insert_testructura_gui ('SKYBIZ', 'OBINGRESOS');


/********************************************F-SCP-FFP-OBINGRESOS-0-01/03/2017********************************************/

/********************************************I-SCP-JRR-OBINGRESOS-0-02/03/2017********************************************/
ALTER TABLE obingresos.tdeposito
ADD COLUMN fecha_venta DATE;

ALTER TABLE obingresos.tdeposito
ADD COLUMN monto_total NUMERIC(18,2);

/********************************************F-SCP-JRR-OBINGRESOS-0-02/03/2017********************************************/


/********************************************I-SCP-JRR-OBINGRESOS-0-20/03/2017********************************************/
ALTER TABLE obingresos.tboleto
  ADD COLUMN fare_calc TEXT;

/********************************************F-SCP-JRR-OBINGRESOS-0-20/03/2017********************************************/

/********************************************I-SCP-JRR-OBINGRESOS-0-24/03/2017********************************************/
ALTER TABLE obingresos.tboleto_vuelo
  ADD COLUMN fecha_hora_origen TIMESTAMP(0) WITHOUT TIME ZONE;

ALTER TABLE obingresos.tboleto_vuelo
  ADD COLUMN fecha_hora_destino TIMESTAMP(0) WITHOUT TIME ZONE;

ALTER TABLE obingresos.tboleto_vuelo
  ADD COLUMN tiempo_conexion INTEGER;

ALTER TABLE obingresos.tboleto_vuelo
  ADD COLUMN retorno VARCHAR(10);

ALTER TABLE obingresos.tboleto_vuelo
  ADD COLUMN clase VARCHAR(5);

ALTER TABLE obingresos.tboleto_vuelo
  ADD COLUMN flight_status VARCHAR(5);



/********************************************F-SCP-JRR-OBINGRESOS-0-24/03/2017********************************************/

/********************************************I-SCP-JRR-OBINGRESOS-0-22/03/2017********************************************/
ALTER TABLE obingresos.tventa_web_modificaciones
ADD COLUMN fecha_reserva_antigua DATE;

ALTER TABLE obingresos.tventa_web_modificaciones
ADD COLUMN pnr_antiguo VARCHAR (20);

ALTER TABLE obingresos.tventa_web_modificaciones
  ADD COLUMN banco VARCHAR(5);

/********************************************F-SCP-JRR-OBINGRESOS-0-22/03/2017********************************************/

/********************************************I-SCP-JRR-OBINGRESOS-0-07/04/2017********************************************/
ALTER TABLE obingresos.tboleto_vuelo
  ADD COLUMN validez_tarifa INTEGER;

  CREATE TABLE obingresos.tclase_tarifaria (
  id_clase_tarifaria SERIAL,
  codigo VARCHAR(1),
  tipo_condicion VARCHAR(15) NOT NULL,
  ruta VARCHAR(5)[],
  aeropuerto VARCHAR(5)[],
  pais VARCHAR(2),
  duracion_meses INTEGER,
  CONSTRAINT tclase_tarifaria_pkey PRIMARY KEY(id_clase_tarifaria)
) INHERITS (pxp.tbase);

/********************************************F-SCP-JRR-OBINGRESOS-0-07/04/2017********************************************/

/********************************************I-SCP-JRR-OBINGRESOS-0-28/04/2017********************************************/
CREATE TABLE obingresos.tcomision_agencia (
  id_comision SERIAL,
  id_contrato INTEGER,
  id_agencia INTEGER,
  descripcion VARCHAR(255),
  tipo_comision VARCHAR(10),
  mercado VARCHAR(20),
  porcentaje NUMERIC(5,2),
  moneda VARCHAR(3),
  limite_superior NUMERIC(18,2),
  limite_inferior NUMERIC(18,2),
  PRIMARY KEY(id_comision)
) INHERITS (pxp.tbase)
WITH (oids = false);

/********************************************F-SCP-JRR-OBINGRESOS-0-28/04/2017********************************************/


/********************************************I-SCP-JRR-OBINGRESOS-0-02/05/2017********************************************/

CREATE TABLE obingresos.ttipo_periodo (
  id_tipo_periodo SERIAL,
  tipo VARCHAR(20) NOT NULL,
  tiempo VARCHAR(20) NOT NULL,
  medio_pago VARCHAR(30) NOT NULL,
  tipo_cc VARCHAR(20),
  pago_comision VARCHAR(2),
  estado VARCHAR(10) NOT NULL,
  fecha_ini_primer_periodo DATE,
  CONSTRAINT ttipo_periodo_pkey PRIMARY KEY(id_tipo_periodo)
) INHERITS (pxp.tbase)

WITH (oids = false);

COMMENT ON COLUMN obingresos.ttipo_periodo.tipo
IS 'portal,venta_propia';

COMMENT ON COLUMN obingresos.ttipo_periodo.tiempo
IS 'bsp,1d,2d,5d';

COMMENT ON COLUMN obingresos.ttipo_periodo.medio_pago
IS 'banca_electronica,cuenta_corriente';

COMMENT ON COLUMN obingresos.ttipo_periodo.tipo_cc
IS 'prepago,postpago';

COMMENT ON COLUMN obingresos.ttipo_periodo.pago_comision
IS 'si,no';

COMMENT ON COLUMN obingresos.ttipo_periodo.estado
IS 'activo,inactivo';

CREATE UNIQUE INDEX tskybiz_archivo_nombre_archivo_uindex ON obingresos.tskybiz_archivo (nombre_archivo);

/********************************************F-SCP-JRR-OBINGRESOS-0-02/05/2017********************************************/



/********************************************I-SCP-FFP-OBINGRESOS-0-04/05/2017********************************************/
ALTER TABLE obingresos.tagencia
  ADD COLUMN tipo_persona VARCHAR(15);

ALTER TABLE obingresos.tperiodo_venta
  ADD COLUMN id_tipo_periodo INTEGER;

ALTER TABLE obingresos.tperiodo_venta
  DROP COLUMN id_pais;

ALTER TABLE obingresos.tperiodo_venta
  DROP COLUMN tipo;

COMMENT ON COLUMN obingresos.tagencia.tipo_persona
IS 'juridica|natural';

CREATE TABLE obingresos.tmovimiento_entidad (
  id_movimiento_entidad SERIAL,
  tipo VARCHAR(8) NOT NULL,
  pnr VARCHAR(8),
  fecha DATE NOT NULL,
  apellido VARCHAR(200),
  monto NUMERIC(18,2) NOT NULL,
  id_moneda INTEGER NOT NULL,
  autorizacion__nro_deposito VARCHAR(200),
  garantia VARCHAR(2) NOT NULL,
  ajuste VARCHAR(2) NOT NULL,
  id_periodo_venta INTEGER,
  id_agencia INTEGER NOT NULL,
  monto_total NUMERIC(18,2) NOT NULL,
  CONSTRAINT tmovimiento_entidad_pkey PRIMARY KEY(id_movimiento_entidad)
) INHERITS (pxp.tbase)

WITH (oids = false);

CREATE TABLE obingresos.tperiodo_venta_agencia (
  id_periodo_venta_agencia SERIAL NOT NULL,
  id_agencia INTEGER NOT NULL,
  id_periodo_venta INTEGER NOT NULL,
  monto_usd NUMERIC(18,2) NOT NULL,
  monto_mb NUMERIC(18,2) NOT NULL,
  deposito_mb NUMERIC(18,2) NOT NULL,
  deposito_usd NUMERIC(18,2) NOT NULL,
  estado VARCHAR(15) NOT NULL,
  fecha_cierre DATE NOT NULL,
  total_mb_cierre NUMERIC(18,2) NOT NULL,
  total_mb_pagado NUMERIC(18,2) NOT NULL,
  PRIMARY KEY(id_periodo_venta_agencia)
) INHERITS (pxp.tbase)
;

ALTER TABLE obingresos.tboleto
  ADD COLUMN id_periodo_venta INTEGER;

ALTER TABLE obingresos.tdeposito
  ADD COLUMN id_periodo_venta INTEGER;



ALTER TABLE obingresos.tdeposito
  ADD COLUMN estado VARCHAR(10) COLLATE pg_catalog."default";

ALTER TABLE obingresos.tdeposito
  ALTER COLUMN estado SET DEFAULT 'borrador'::character varying;

ALTER TABLE obingresos.tperiodo_venta_agencia
  ADD COLUMN moneda_restrictiva VARCHAR(2) DEFAULT 'si' NOT NULL;

/********************************************F-SCP-FFP-OBINGRESOS-0-04/05/2017********************************************/






/********************************************I-SCP-FFP-OBINGRESOS-0-31/05/2017********************************************/
CREATE TABLE obingresos.tobservaciones_conciliacion (
  id_observaciones_conciliacion SERIAL NOT NULL,
  tipo_observacion VARCHAR(20) NOT NULL,
  observacion TEXT NOT NULL,
  fecha_observacion DATE NOT NULL,
  banco VARCHAR(30),
  PRIMARY KEY(id_observaciones_conciliacion)
) INHERITS (pxp.tbase)
;

COMMENT ON COLUMN obingresos.tobservaciones_conciliacion.tipo_observacion
IS 'skybiz,portal';

/********************************************F-SCP-FFP-OBINGRESOS-0-31/05/2017********************************************/



/********************************************I-SCP-GSS-OBINGRESOS-0-13/07/2017********************************************/

ALTER TABLE obingresos.tboleto
  ADD COLUMN pnr VARCHAR(6);

/********************************************F-SCP-GSS-OBINGRESOS-0-13/07/2017********************************************/

/********************************************I-SCP-FFP-OBINGRESOS-0-16/06/2017********************************************/
ALTER TABLE obingresos.tdetalle_boletos_web
  ADD COLUMN fecha_pago DATE;

ALTER TABLE obingresos.tdetalle_boletos_web
  ADD COLUMN id_agencia INTEGER;

ALTER TABLE obingresos.tdetalle_boletos_web
  ADD COLUMN comision NUMERIC(18,2);

ALTER TABLE obingresos.tdetalle_boletos_web
  ADD COLUMN numero_tarjeta VARCHAR(20);

ALTER TABLE obingresos.tdetalle_boletos_web
  ADD COLUMN numero_autorizacion VARCHAR(8);

CREATE TYPE obingresos.detalle_boletos_portal AS (
  "Billete" VARCHAR(15),
  "CNJ" VARCHAR(255),
  "MedioDePago" VARCHAR(50),
  "Entidad" VARCHAR(50),
  "Moneda" VARCHAR(3),
  "ImportePasaje" NUMERIC(18,2),
  "ImporteTarifa" NUMERIC(18,2),
  "OrigenDestino" VARCHAR(255),
  "Nit" VARCHAR(30),
  "RazonSocial" VARCHAR(255),
  "FechaPago" VARCHAR(20),
  "idEntidad" INTEGER,
  "Comision" NUMERIC(18,2),
  "NumeroTarjeta" VARCHAR(20),
  "NumeroAutorizacion" VARCHAR(8),
  "FechaEmision" VARCHAR(20)
);

/********************************************F-SCP-FFP-OBINGRESOS-0-16/06/2017********************************************/

/********************************************I-SCP-JRR-OBINGRESOS-0-24/07/2017********************************************/


ALTER TABLE obingresos.tdetalle_boletos_web
  ADD COLUMN id_periodo_venta INTEGER;

ALTER TABLE obingresos.tboleto_retweb
  ADD COLUMN id_moneda INTEGER;

ALTER TABLE obingresos.tboleto_retweb
  ADD COLUMN comision NUMERIC(18,2);

ALTER TABLE obingresos.tboleto_retweb
  ADD COLUMN pnr VARCHAR(20);

ALTER TABLE obingresos.tmovimiento_entidad
  ADD COLUMN billete VARCHAR(20);

 ALTER TABLE obingresos.tboleto_retweb
  ADD COLUMN id_agencia INTEGER;

ALTER TABLE obingresos.tdetalle_boletos_web
  ADD COLUMN id_moneda INTEGER;

ALTER TABLE obingresos.tperiodo_venta_agencia
  ALTER COLUMN deposito_mb SET DEFAULT 0;

ALTER TABLE obingresos.tperiodo_venta_agencia
  ALTER COLUMN deposito_usd SET DEFAULT 0;

ALTER TABLE obingresos.tperiodo_venta_agencia
  DROP COLUMN total_mb_cierre;

ALTER TABLE obingresos.tperiodo_venta_agencia
  DROP COLUMN total_mb_pagado;
/********************************************F-SCP-JRR-OBINGRESOS-0-24/07/2017********************************************/


/********************************************I-SCP-JRR-OBINGRESOS-0-04/08/2017********************************************/
ALTER TABLE obingresos.tagencia
  ADD COLUMN nit VARCHAR(30);

ALTER TABLE obingresos.tdetalle_boletos_web
  ADD COLUMN neto NUMERIC(18,2);

ALTER TABLE obingresos.tboleto_retweb
  ALTER COLUMN tarjeta DROP NOT NULL;

ALTER TABLE obingresos.tboleto_retweb
  ADD COLUMN forma_pago VARCHAR(100);

ALTER TABLE obingresos.tboleto_retweb
  ADD COLUMN neto NUMERIC(18,2);

CREATE TABLE obingresos.tobservaciones_portal (
  id_observaciones_portal SERIAL NOT NULL,
  billete VARCHAR(15) ,
  pnr VARCHAR(15) ,
  total NUMERIC(18,2) NOT NULL,
  moneda VARCHAR(3),
  tipo_observacion VARCHAR(20) NOT NULL,
  observacion TEXT NOT NULL,
  PRIMARY KEY(id_observaciones_portal)
) INHERITS (pxp.tbase)
;

ALTER TABLE obingresos.tdetalle_boletos_web
  ALTER COLUMN numero_autorizacion TYPE VARCHAR(200) COLLATE pg_catalog."default";

ALTER TABLE obingresos.tperiodo_venta
  ADD COLUMN codigo_periodo VARCHAR(15) NOT NULL;

ALTER TABLE obingresos.tmovimiento_entidad
  ADD COLUMN neto NUMERIC(18,2);



ALTER TABLE obingresos.tperiodo_venta_agencia
  ADD COLUMN monto_credito_mb NUMERIC(18,2);

ALTER TABLE obingresos.tperiodo_venta_agencia
  ADD COLUMN monto_credito_usd NUMERIC(18,2);

ALTER TABLE obingresos.tperiodo_venta_agencia
  ADD COLUMN monto_debito_mb NUMERIC(18,2);

ALTER TABLE obingresos.tperiodo_venta_agencia
  ADD COLUMN monto_debito_usd NUMERIC(18,2);

ALTER TABLE obingresos.tperiodo_venta_agencia
  ADD COLUMN monto_boletos_mb NUMERIC(18,2);

ALTER TABLE obingresos.tperiodo_venta_agencia
  ADD COLUMN monto_boletos_usd NUMERIC(18,2);

ALTER TABLE obingresos.tperiodo_venta_agencia
  ADD COLUMN monto_neto_mb NUMERIC(18,2);

ALTER TABLE obingresos.tperiodo_venta_agencia
  ADD COLUMN monto_neto_usd NUMERIC(18,2);

ALTER TABLE obingresos.tperiodo_venta_agencia
  ADD COLUMN monto_comision_mb NUMERIC(18,2);

ALTER TABLE obingresos.tperiodo_venta_agencia
  ADD COLUMN monto_comision_usd NUMERIC(18,2);


ALTER TABLE obingresos.tperiodo_venta_agencia
  ADD COLUMN total_comision_mb NUMERIC(18,2);

ALTER TABLE obingresos.tobservaciones_portal
  ADD COLUMN fecha_emision DATE;
/********************************************F-SCP-JRR-OBINGRESOS-0-04/08/2017********************************************/

/********************************************I-SCP-JRR-OBINGRESOS-0-17/08/2017********************************************/
CREATE TABLE obingresos.ttotal_comision_mes (
  gestion NUMERIC(4,0) NOT NULL,
  periodo NUMERIC(2,0) NOT NULL,
  total_comision NUMERIC(18,2) NOT NULL,
  id_tipo_periodo INTEGER NOT NULL,
  id_periodos INTEGER[] NOT NULL
) INHERITS (pxp.tbase)
;

ALTER TABLE obingresos.ttotal_comision_mes
  ADD COLUMN estado VARCHAR(15);

COMMENT ON COLUMN obingresos.ttotal_comision_mes.estado
IS 'pendiente o verificado';

ALTER TABLE obingresos.ttotal_comision_mes
  ADD COLUMN id_total_comision_mes SERIAL NOT NULL PRIMARY KEY;

ALTER TABLE obingresos.ttotal_comision_mes
  ADD COLUMN id_agencia INTEGER;

CREATE RULE ttotal_comision_mes_rl AS ON INSERT TO obingresos.ttotal_comision_mes
WHERE NEW.total_comision = 0
DO INSTEAD NOTHING;

ALTER TABLE obingresos.tagencia
  ADD COLUMN terciariza VARCHAR(2);

ALTER TABLE obingresos.tagencia
  ALTER COLUMN terciariza SET DEFAULT 'no';

ALTER TABLE obingresos.tagencia
  ADD COLUMN id_agencia_terciarizada INTEGER;

ALTER TABLE obingresos.tmovimiento_entidad
  ADD COLUMN comision_terciarizada NUMERIC(18,2);

ALTER TABLE obingresos.tperiodo_venta
  ADD COLUMN fecha_pago DATE;

/********************************************F-SCP-JRR-OBINGRESOS-0-17/08/2017********************************************/

/********************************************I-SCP-JRR-OBINGRESOS-0-10/09/2017********************************************/

ALTER TABLE obingresos.tagencia
  ADD COLUMN email VARCHAR(255);

/********************************************F-SCP-JRR-OBINGRESOS-0-10/09/2017********************************************/

/********************************************I-SCP-JRR-OBINGRESOS-0-12/09/2017********************************************/
DROP TRIGGER tdeposito_tr ON obingresos.tdeposito;

CREATE TRIGGER tdeposito_tr
  AFTER UPDATE
  ON obingresos.tdeposito FOR EACH ROW
  EXECUTE PROCEDURE obingresos.f_tr_deposito();

/********************************************F-SCP-JRR-OBINGRESOS-0-12/09/2017********************************************/

/********************************************I-SCP-GSS-OBINGRESOS-0-22/10/2017********************************************/

ALTER TABLE obingresos.tboleto
  ADD COLUMN tipo_comision VARCHAR(13);

ALTER TABLE obingresos.tboleto
  ALTER COLUMN tipo_comision SET DEFAULT 'ninguno';

/********************************************F-SCP-GSS-OBINGRESOS-0-22/10/2017********************************************/

/********************************************I-SCP-JRR-OBINGRESOS-0-30/10/2017********************************************/

CREATE UNIQUE INDEX tdeposito_nro_deposito_agencia ON obingresos.tdeposito
  USING btree (nro_deposito COLLATE pg_catalog."default", id_agencia)
  WHERE tipo='agencia';

CREATE INDEX tdetalle_boletos_web_idx ON obingresos.tdetalle_boletos_web
  USING btree (fecha);

CREATE INDEX tdetalle_boletos_web_idx1 ON obingresos.tdetalle_boletos_web
  USING btree (numero_autorizacion COLLATE pg_catalog."default")
  WHERE origen = 'portal';

/********************************************F-SCP-JRR-OBINGRESOS-0-30/10/2017********************************************/


/********************************************I-SCP-JRR-OBINGRESOS-0-11/12/2017********************************************/
ALTER TABLE obingresos.tagencia
  ADD COLUMN controlar_periodos_pago VARCHAR(2) NOT NULL DEFAULT 'si';

ALTER TABLE obingresos.tagencia
  ADD COLUMN validar_boleta VARCHAR(2) NOT NULL DEFAULT 'si';

ALTER TABLE obingresos.tagencia
  ADD COLUMN bloquear_emision VARCHAR(2) NOT NULL DEFAULT 'no';


/********************************************F-SCP-JRR-OBINGRESOS-0-11/12/2017********************************************/


/********************************************I-SCP-RZM-OBINGRESOS-0-28/09/2018********************************************/
CREATE TABLE obingresos.tarchivo_acm (
  id_archivo_acm SERIAL,
  fecha_ini DATE NOT NULL,
  fecha_fin DATE NOT NULL,
  nombre VARCHAR(500) NOT NULL,
  estado VARCHAR(50),
  ultimo_numero INTEGER,
  CONSTRAINT tarchivo_acm_pkey PRIMARY KEY(id_archivo_acm)
) INHERITS (pxp.tbase)

WITH (oids = false);
/********************************************F-SCP-RZM-OBINGRESOS-0-28/09/2018********************************************/
/********************************************I-SCP-RZM-OBINGRESOS-0-28/09/2018********************************************/
CREATE TABLE obingresos.tarchivo_acm_det (
  id_archivo_acm_det SERIAL,
  officce_id VARCHAR(50) NOT NULL,
  porcentaje INTEGER NOT NULL,
  id_agencia INTEGER,
  importe_total_mb NUMERIC(18,2),
  importe_total_mt NUMERIC(18,2),
  id_archivo_acm INTEGER NOT NULL,
  neto_total_mb NUMERIC(18,2),
  neto_total_mt NUMERIC(18,2),
  cant_bol_mb INTEGER,
  cant_bol_mt INTEGER,
  CONSTRAINT tarchivo_acm_det_pkey PRIMARY KEY(id_archivo_acm_det),
  CONSTRAINT tarchivo_acm_det_fk FOREIGN KEY (id_archivo_acm)
    REFERENCES obingresos.tarchivo_acm(id_archivo_acm)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
    NOT DEFERRABLE
) INHERITS (pxp.tbase)

WITH (oids = false);
/********************************************I-SCP-RZM-OBINGRESOS-0-28/09/2018********************************************/
/********************************************I-SCP-IRV-OBINGRESOS-0-28/09/2018********************************************/
CREATE TABLE obingresos.tacm (
  id_acm SERIAL,
  id_archivo_acm_det INTEGER NOT NULL,
  numero VARCHAR(20) NOT NULL,
  fecha DATE NOT NULL,
  ruta VARCHAR(10) NOT NULL,
  id_moneda INTEGER,
  importe NUMERIC(18,2),
  id_movimiento_entidad INTEGER,
  total_bsp NUMERIC(18,2),
  CONSTRAINT tacm_pkey PRIMARY KEY(id_acm),
  CONSTRAINT tacm_fk FOREIGN KEY (id_moneda)
    REFERENCES param.tmoneda(id_moneda)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
    NOT DEFERRABLE,
  CONSTRAINT tacm_fk1 FOREIGN KEY (id_archivo_acm_det)
    REFERENCES obingresos.tarchivo_acm_det(id_archivo_acm_det)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
    NOT DEFERRABLE,
  CONSTRAINT tacm_fk2 FOREIGN KEY (id_movimiento_entidad)
    REFERENCES obingresos.tmovimiento_entidad(id_movimiento_entidad)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
    NOT DEFERRABLE
) INHERITS (pxp.tbase)

WITH (oids = false);

ALTER TABLE obingresos.tacm
  ALTER COLUMN numero SET STATISTICS 0;
/********************************************F-SCP-IRV-OBINGRESOS-0-28/09/2018********************************************/
/********************************************I-SCP-IRV-OBINGRESOS-0-28/09/2018********************************************/
CREATE TABLE obingresos.tacm_det (
  id_acm_det SERIAL,
  id_detalle_boletos_web INTEGER NOT NULL,
  neto NUMERIC(18,2) NOT NULL,
  over_comision NUMERIC(18,2) NOT NULL,
  id_acm INTEGER NOT NULL,
  com_bsp NUMERIC(18,2),
  porcentaje_over INTEGER,
  moneda VARCHAR(10),
  td VARCHAR(10),
  CONSTRAINT tacm_det_pkey PRIMARY KEY(id_acm_det),
  CONSTRAINT tacm_det_fk FOREIGN KEY (id_acm)
    REFERENCES obingresos.tacm(id_acm)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
    NOT DEFERRABLE,
  CONSTRAINT tacm_det_fk1 FOREIGN KEY (id_detalle_boletos_web)
    REFERENCES obingresos.tdetalle_boletos_web(id_detalle_boletos_web)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
    NOT DEFERRABLE
) INHERITS (pxp.tbase)

WITH (oids = false);
/********************************************F-SCP-IRV-OBINGRESOS-0-28/09/2018********************************************/
/********************************************I-SCP-RZM-OBINGRESOS-1-24/10/2018********************************************/
ALTER TABLE obingresos.tarchivo_acm_det
ADD COLUMN abonado VARCHAR(2);
/********************************************F-SCP-RZM-OBINGRESOS-1-24/10/2018********************************************/

/********************************************I-SCP-FEA-OBINGRESOS-1-07/11/2018********************************************/

CREATE TABLE obingresos.tboleto_amadeus (
  id_boleto_amadeus SERIAL,
  nro_boleto VARCHAR(50),
  pasajero VARCHAR(100),
  fecha_emision DATE,
  total NUMERIC(18,2),
  liquido NUMERIC(18,2),
  id_moneda_boleto INTEGER,
  neto NUMERIC(18,2),
  estado VARCHAR(20),
  id_punto_venta INTEGER,
  localizador VARCHAR(13),
  voided VARCHAR(10),
  forma_pago VARCHAR(3),
  officeid VARCHAR(10),
  codigo_iata VARCHAR(10),
  comision NUMERIC(18,2),
  moneda VARCHAR(5),
  tc NUMERIC(18,2),
  xt NUMERIC(18,2),
  monto_pagado_moneda_boleto NUMERIC(18,2),
  agente_venta VARCHAR(7),
  id_agencia INTEGER,
  tipo_comision VARCHAR(13) DEFAULT 'ninguno'::character varying,
  id_usuario_cajero INTEGER,
  ruta_completa VARCHAR(255),
  mensaje_error TEXT,
  estado_informix VARCHAR(20) DEFAULT 'migrado'::character varying,
  CONSTRAINT tboleto_amadeus_nro_boleto_key UNIQUE(nro_boleto),
  CONSTRAINT tboleto_amadeus_pkey PRIMARY KEY(id_boleto_amadeus),
  CONSTRAINT fk_tboleto_amadeus__id_agencia FOREIGN KEY (id_agencia)
    REFERENCES obingresos.tagencia(id_agencia)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
    NOT DEFERRABLE,
  CONSTRAINT fk_tboleto_amadeus__id_moneda FOREIGN KEY (id_moneda_boleto)
    REFERENCES param.tmoneda(id_moneda)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
    NOT DEFERRABLE,
  CONSTRAINT fk_tboleto_amadeus__id_punto_venta FOREIGN KEY (id_punto_venta)
    REFERENCES vef.tpunto_venta(id_punto_venta)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
    NOT DEFERRABLE,
  CONSTRAINT fk_tboleto_amadeus__id_usuario FOREIGN KEY (id_usuario_cajero)
    REFERENCES segu.tusuario(id_usuario)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
    NOT DEFERRABLE
) INHERITS (pxp.tbase)

WITH (oids = false);

CREATE TABLE obingresos.tboleto_amadeus_forma_pago (
  id_boleto_amadeus_forma_pago SERIAL,
  importe NUMERIC(18,2) NOT NULL,
  id_forma_pago INTEGER NOT NULL,
  id_boleto_amadeus INTEGER NOT NULL,
  tipo VARCHAR(20),
  tarjeta VARCHAR(6),
  numero_tarjeta VARCHAR(20),
  ctacte VARCHAR(20),
  codigo_tarjeta VARCHAR(20),
  forma_pago_amadeus VARCHAR(3),
  id_auxiliar INTEGER,
  id_usuario_fp_corregido INTEGER,
  registro_mod INTEGER DEFAULT 0,
  mco VARCHAR(15),
  CONSTRAINT tboleto_amadeus_forma_pago_pkey PRIMARY KEY(id_boleto_amadeus_forma_pago),
  CONSTRAINT fk_tboleto_amadeus_forma_pago__id_auxiliar FOREIGN KEY (id_auxiliar)
    REFERENCES conta.tauxiliar(id_auxiliar)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
    NOT DEFERRABLE,
  CONSTRAINT fk_tboleto_amadeus_forma_pago__id_boleto_amadeus FOREIGN KEY (id_boleto_amadeus)
    REFERENCES obingresos.tboleto_amadeus(id_boleto_amadeus)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
    NOT DEFERRABLE,
  CONSTRAINT fk_tboleto_amadeus_forma_pago__id_forma_pago FOREIGN KEY (id_forma_pago)
    REFERENCES obingresos.tforma_pago(id_forma_pago)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
    NOT DEFERRABLE,
  CONSTRAINT fk_tboleto_amadeus_forma_pago__id_usuario FOREIGN KEY (id_usuario_fp_corregido)
    REFERENCES segu.tusuario(id_usuario)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
    NOT DEFERRABLE
) INHERITS (pxp.tbase)

WITH (oids = false);

CREATE INDEX tboleto_amadeus_forma_pago__id_boleto_idx ON obingresos.tboleto_amadeus_forma_pago
  USING btree (id_boleto_amadeus);


CREATE TABLE obingresos.tconsulta_viajero_frecuente (
  id_consulta_viajero_frecuente SERIAL,
  ffid VARCHAR(50),
  voucher_code VARCHAR(60),
  message VARCHAR(200),
  status VARCHAR(20),
  nro_boleto VARCHAR(50),
  CONSTRAINT tconsulta_vieajero_frecuente_pkey PRIMARY KEY(id_consulta_viajero_frecuente)
) INHERITS (pxp.tbase)

WITH (oids = false);

ALTER TABLE obingresos.tconsulta_viajero_frecuente
  ALTER COLUMN id_consulta_viajero_frecuente SET STATISTICS 0;

ALTER TABLE obingresos.tconsulta_viajero_frecuente
  ALTER COLUMN ffid SET STATISTICS 0;

ALTER TABLE obingresos.tconsulta_viajero_frecuente
  ALTER COLUMN voucher_code SET STATISTICS 0;

ALTER TABLE obingresos.tconsulta_viajero_frecuente
  ALTER COLUMN message SET STATISTICS 0;

ALTER TABLE obingresos.tconsulta_viajero_frecuente
  ALTER COLUMN status SET STATISTICS 0;

CREATE TABLE obingresos.tdetalle_credito (
  id_agencia SERIAL,
  autorizacion__nro_deposito TEXT,
  fecha DATE,
  monto_total NUMERIC(18,2),
  CONSTRAINT tdetalle_credito_pkey PRIMARY KEY(id_agencia)
)
WITH (oids = false);

CREATE TABLE obingresos.tdetalle_debito (
  id_agencia SERIAL,
  billeta_pnr TEXT,
  fecha DATE,
  comision NUMERIC(18,2),
  importe NUMERIC(18,2),
  neto NUMERIC(18,2),
  saldo NUMERIC,
  CONSTRAINT tdetalle_debito_pkey PRIMARY KEY(id_agencia)
)
WITH (oids = false);

CREATE TABLE obingresos.tforma_pago_ant (
  id_forma_pago_ant SERIAL,
  id_boleto_amadeus INTEGER,
  id_forma_pago INTEGER,
  importe NUMERIC(20,0),
  CONSTRAINT tforma_pago_ant_pkey PRIMARY KEY(id_forma_pago_ant)
) INHERITS (pxp.tbase)

WITH (oids = false);

ALTER TABLE obingresos.tforma_pago_ant
  ALTER COLUMN id_forma_pago_ant SET STATISTICS 0;

ALTER TABLE obingresos.tforma_pago_ant
  ALTER COLUMN id_boleto_amadeus SET STATISTICS 0;

ALTER TABLE obingresos.tforma_pago_ant
  ALTER COLUMN id_forma_pago SET STATISTICS 0;

ALTER TABLE obingresos.tforma_pago_ant
  ALTER COLUMN importe SET STATISTICS 0;


CREATE TABLE obingresos.tlog_vajero_frecuente (
  id_log_viajero_frecuente SERIAL,
  tickert_number VARCHAR(50),
  pnr VARCHAR(50),
  importe NUMERIC(20,0),
  moneda VARCHAR(5),
  id_boleto_amadeus INTEGER,
  CONSTRAINT tlog_vajero_frecuente_pkey PRIMARY KEY(id_log_viajero_frecuente)
) INHERITS (pxp.tbase)

WITH (oids = false);

ALTER TABLE obingresos.tlog_vajero_frecuente
  ALTER COLUMN id_log_viajero_frecuente SET STATISTICS 0;

ALTER TABLE obingresos.tlog_vajero_frecuente
  ALTER COLUMN tickert_number SET STATISTICS 0;

ALTER TABLE obingresos.tlog_vajero_frecuente
  ALTER COLUMN pnr SET STATISTICS 0;

ALTER TABLE obingresos.tlog_vajero_frecuente
  ALTER COLUMN importe SET STATISTICS 0;

ALTER TABLE obingresos.tlog_vajero_frecuente
  ALTER COLUMN moneda SET STATISTICS 0;


CREATE TABLE obingresos.tpnr_forma_pago (
  id_pnr_forma_pago SERIAL,
  pnr VARCHAR,
  id_forma_pago INTEGER,
  tarjeta VARCHAR(6),
  numero_tarjeta VARCHAR(20),
  codigo_tarjeta VARCHAR(20),
  ctacte VARCHAR(20),
  importe NUMERIC(18,2),
  forma_pago_amadeus VARCHAR(3),
  CONSTRAINT tpnr_forma_pago_pkey PRIMARY KEY(id_pnr_forma_pago),
  CONSTRAINT fk_tpnr_forma_pago__id_forma_pago FOREIGN KEY (id_forma_pago)
    REFERENCES obingresos.tforma_pago(id_forma_pago)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
    NOT DEFERRABLE
) INHERITS (pxp.tbase)

WITH (oids = false);


CREATE TABLE obingresos.tviajero_frecuente (
  id_viajero_frecuente SERIAL,
  id_boleto_amadeus INTEGER,
  ffid VARCHAR(50),
  pnr VARCHAR(30),
  ticket_number VARCHAR(50),
  voucher_code VARCHAR(50),
  id_pasajero_frecuente INTEGER,
  nombre_completo VARCHAR(100),
  mensaje VARCHAR(200),
  status VARCHAR(50),
  CONSTRAINT tviajero_frecuente_pkey PRIMARY KEY(id_viajero_frecuente)
) INHERITS (pxp.tbase)

WITH (oids = false);

ALTER TABLE obingresos.tviajero_frecuente
  ALTER COLUMN id_viajero_frecuente SET STATISTICS 0;

ALTER TABLE obingresos.tviajero_frecuente
  ALTER COLUMN id_boleto_amadeus SET STATISTICS 0;

ALTER TABLE obingresos.tviajero_frecuente
  ALTER COLUMN ffid SET STATISTICS 0;

ALTER TABLE obingresos.tviajero_frecuente
  ALTER COLUMN pnr SET STATISTICS 0;

ALTER TABLE obingresos.tviajero_frecuente
  ALTER COLUMN ticket_number SET STATISTICS 0;


CREATE TABLE obingresos.tventa_web_modificaciones (
  nro_boleto VARCHAR(20) NOT NULL,
  tipo VARCHAR(15),
  nro_boleto_reemision VARCHAR(20),
  used VARCHAR(2),
  motivo TEXT NOT NULL,
  id_venta_web_modificaciones SERIAL,
  procesado VARCHAR(2) DEFAULT 'no'::character varying NOT NULL,
  pnr_antiguo VARCHAR(20),
  fecha_reserva_antigua DATE,
  banco VARCHAR(5),
  CONSTRAINT tventa_web_modificaciones_nro_boleto_key UNIQUE(nro_boleto),
  CONSTRAINT tventa_web_modificaciones_pkey PRIMARY KEY(id_venta_web_modificaciones),
  CONSTRAINT tventa_web_modificaciones_chk CHECK (((tipo)::text = 'tsu_anulado'::text) OR ((tipo)::text = 'anulado'::text) OR ((tipo)::text = 'reemision'::text) OR ((tipo)::text = 'emision_manual'::text))
) INHERITS (pxp.tbase)

WITH (oids = false);

CREATE UNIQUE INDEX tventa_web_modificaciones_idx ON obingresos.tventa_web_modificaciones
  USING btree (nro_boleto_reemision COLLATE pg_catalog."default")
  WHERE ((nro_boleto_reemision IS NOT NULL) AND ((nro_boleto_reemision)::text <> ''::text));


CREATE TABLE obingresos.tvisa (
  id_visa SERIAL,
  nro_boleto VARCHAR(50),
  autoriazaion VARCHAR(20),
  CONSTRAINT tvisa_pkey PRIMARY KEY(id_visa)
)
WITH (oids = false);

ALTER TABLE obingresos.tvisa
  ALTER COLUMN id_visa SET STATISTICS 0;

ALTER TABLE obingresos.tvisa
  ALTER COLUMN nro_boleto SET STATISTICS 0;

ALTER TABLE obingresos.tvisa
  ALTER COLUMN autoriazaion SET STATISTICS 0;


-------------------------------------------

ALTER TABLE obingresos.tagencia
  ADD COLUMN movimiento_activo VARCHAR(2) DEFAULT 'si'::character varying;

ALTER TABLE obingresos.tboleto_forma_pago
  ADD COLUMN forma_pago_amadeus VARCHAR(3),
  ADD COLUMN  fp_amadeus_corregido VARCHAR(3),
  ADD COLUMN  id_usuario_fp_corregido INTEGER,
  ADD COLUMN  id_auxiliar INTEGER;


ALTER TABLE obingresos.tdeposito
  ADD COLUMN id_apertura_cierre_caja INTEGER,
  ADD COLUMN nro_deposito_aux VARCHAR(70),
  ADD COLUMN nro_deposito_boa VARCHAR(70);
/********************************************F-SCP-FEA-OBINGRESOS-1-07/11/2018********************************************/
/********************************************I-SCP-RZM-OBINGRESOS-1-02/01/2019********************************************/
CREATE TABLE obingresos.tviajero_interno (
  id_viajero_interno SERIAL,
  codigo_voucher VARCHAR(60),
  mensaje VARCHAR(200),
  estado VARCHAR(20),
  CONSTRAINT tviajero_interno_pkey PRIMARY KEY(id_viajero_interno)
) INHERITS (pxp.tbase)

WITH (oids = false);


CREATE TABLE obingresos.tviajero_interno_det (
  id_viajero_interno_det SERIAL,
  nombre VARCHAR(100),
  pnr VARCHAR(50),
  num_boleto VARCHAR(50),
  id_viajero_interno INTEGER,
  solicitud VARCHAR(5),
  num_documento VARCHAR(15),
  estado_voucher VARCHAR(50),
  tarifa VARCHAR(20),
  CONSTRAINT tviajero_interno_det_pkey PRIMARY KEY(id_viajero_interno_det),
  CONSTRAINT tviajero_interno_det_fk FOREIGN KEY (id_viajero_interno)
    REFERENCES obingresos.tviajero_interno(id_viajero_interno)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
    NOT DEFERRABLE
) INHERITS (pxp.tbase)

WITH (oids = false);

/********************************************F-SCP-RZM-OBINGRESOS-1-02/01/2019********************************************/
/********************************************I-SCP-IRVA-OBINGRESOS-1-02/01/2019********************************************/
ALTER TABLE obingresos.tagencia
  ALTER COLUMN boaagt SET NOT NULL;
/********************************************F-SCP-RZM-OBINGRESOS-1-02/01/2019********************************************/

/********************************************I-SCP-IRVA-OBINGRESOS-1-05/08/2019********************************************/
ALTER TABLE obingresos.tboleto_amadeus_forma_pago
  ADD COLUMN nro_cupon VARCHAR(50);

ALTER TABLE obingresos.tboleto_amadeus_forma_pago
  ADD COLUMN nro_cuota VARCHAR(50);
/********************************************F-SCP-RZM-OBINGRESOS-1-05/08/2019********************************************/

/********************************************I-SCP-IRVA-OBINGRESOS-0-18/09/2019********************************************/
ALTER TABLE obingresos.tmovimiento_entidad
  ADD COLUMN fk_id_movimiento_entidad INTEGER;
/********************************************F-SCP-IRVA-OBINGRESOS-0-18/09/2019********************************************/

/********************************************I-SCP-IRVA-OBINGRESOS-0-26/09/2019********************************************/
ALTER TABLE obingresos.tmovimiento_entidad
  ADD COLUMN id_void INTEGER;

ALTER TABLE obingresos.tmovimiento_entidad
  ADD COLUMN tipo_void VARCHAR(50);
/********************************************F-SCP-IRVA-OBINGRESOS-0-26/09/2019********************************************/

/********************************************I-SCP-IRVA-OBINGRESOS-0-06/11/2019********************************************/
CREATE TABLE obingresos.tforma_pago_pw (
  id_forma_pago_pw SERIAL,
  name VARCHAR(100),
  country_code VARCHAR(5),
  erp_code VARCHAR(50),
  fop_code VARCHAR(15),
  manage_account NUMERIC(18,2),
  forma_pago_id INTEGER,
  CONSTRAINT tforma_pago_pw_pkey PRIMARY KEY(id_forma_pago_pw)
) INHERITS (pxp.tbase)
WITH (oids = false);

ALTER TABLE obingresos.tforma_pago_pw
  ALTER COLUMN id_forma_pago_pw SET STATISTICS 0;

ALTER TABLE obingresos.tforma_pago_pw
  ALTER COLUMN name SET STATISTICS 0;

ALTER TABLE obingresos.tforma_pago_pw
  ALTER COLUMN country_code SET STATISTICS 0;

ALTER TABLE obingresos.tforma_pago_pw
  ALTER COLUMN erp_code SET STATISTICS 0;

ALTER TABLE obingresos.tforma_pago_pw
  ALTER COLUMN fop_code SET STATISTICS 0;

ALTER TABLE obingresos.tforma_pago_pw
  ALTER COLUMN manage_account SET STATISTICS 0;

ALTER TABLE obingresos.tforma_pago_pw
  OWNER TO postgres;

  CREATE TABLE obingresos.tboletos_observados (
    id_boletos_observados SERIAL,
    pnr VARCHAR(10) NOT NULL,
    nro_autorizacion VARCHAR(256) NOT NULL,
    moneda VARCHAR(4),
    importe_total NUMERIC(18,2) NOT NULL,
    fecha_emision DATE,
    estado_p VARCHAR(10),
    forma_pago VARCHAR(10),
    medio_pago VARCHAR(10),
    instancia_pago VARCHAR(10),
    office_id_emisor VARCHAR(10) NOT NULL,
    pnr_prov VARCHAR(10) NOT NULL,
    nro_autorizacion_prov VARCHAR(256) NOT NULL,
    office_id_emisor_prov VARCHAR(10) NOT NULL,
    importe_prov NUMERIC(18,2),
    moneda_prov VARCHAR(4),
    estado_prov VARCHAR(10),
    fecha_autorizacion_prov DATE,
    estado_validacion CHAR(1) NOT NULL,
    tipo_error VARCHAR(100) NOT NULL,
    tipo_validacion VARCHAR(50),
    prov_informacion VARCHAR(256),
    CONSTRAINT tpnr_na_observado_pkey PRIMARY KEY(id_boletos_observados)
  ) INHERITS (pxp.tbase)
  WITH (oids = false);

  ALTER TABLE obingresos.tboletos_observados
    ALTER COLUMN id_boletos_observados SET STATISTICS 0;

  ALTER TABLE obingresos.tboletos_observados
    ALTER COLUMN pnr SET STATISTICS 0;

  ALTER TABLE obingresos.tboletos_observados
    ALTER COLUMN nro_autorizacion SET STATISTICS 0;

  ALTER TABLE obingresos.tboletos_observados
    ALTER COLUMN moneda SET STATISTICS 0;

  ALTER TABLE obingresos.tboletos_observados
    ALTER COLUMN importe_total SET STATISTICS 0;

  ALTER TABLE obingresos.tboletos_observados
    ALTER COLUMN fecha_emision SET STATISTICS 0;

  ALTER TABLE obingresos.tboletos_observados
    ALTER COLUMN estado_p SET STATISTICS 0;

  ALTER TABLE obingresos.tboletos_observados
    ALTER COLUMN forma_pago SET STATISTICS 0;

  ALTER TABLE obingresos.tboletos_observados
    ALTER COLUMN medio_pago SET STATISTICS 0;

  ALTER TABLE obingresos.tboletos_observados
    ALTER COLUMN instancia_pago SET STATISTICS 0;

  ALTER TABLE obingresos.tboletos_observados
    ALTER COLUMN office_id_emisor SET STATISTICS 0;

  ALTER TABLE obingresos.tboletos_observados
    ALTER COLUMN pnr_prov SET STATISTICS 0;

  ALTER TABLE obingresos.tboletos_observados
    ALTER COLUMN nro_autorizacion_prov SET STATISTICS 0;

  ALTER TABLE obingresos.tboletos_observados
    ALTER COLUMN office_id_emisor_prov SET STATISTICS 0;

  ALTER TABLE obingresos.tboletos_observados
    ALTER COLUMN importe_prov SET STATISTICS 0;

  ALTER TABLE obingresos.tboletos_observados
    ALTER COLUMN moneda_prov SET STATISTICS 0;

  ALTER TABLE obingresos.tboletos_observados
    ALTER COLUMN estado_prov SET STATISTICS 0;

  ALTER TABLE obingresos.tboletos_observados
    ALTER COLUMN fecha_autorizacion_prov SET STATISTICS 0;

  ALTER TABLE obingresos.tboletos_observados
    ALTER COLUMN estado_validacion SET STATISTICS 0;

  ALTER TABLE obingresos.tboletos_observados
    ALTER COLUMN tipo_error SET STATISTICS 0;

  ALTER TABLE obingresos.tboletos_observados
    ALTER COLUMN tipo_validacion SET STATISTICS 0;

  ALTER TABLE obingresos.tboletos_observados
    OWNER TO postgres;


    CREATE TABLE obingresos.tinstancia_pago (
    id_instancia_pago SERIAL,
    id_medio_pago INTEGER,
    nombre VARCHAR(40) NOT NULL,
    codigo VARCHAR(10) NOT NULL,
    codigo_forma_pago VARCHAR(10),
    codigo_medio_pago VARCHAR(20),
    instancia_pago_id INTEGER,
    fp_code VARCHAR(50),
    ins_code VARCHAR(20),
    CONSTRAINT tintancia_pago_pkey PRIMARY KEY(id_instancia_pago)
  ) INHERITS (pxp.tbase)
  WITH (oids = false);

  ALTER TABLE obingresos.tinstancia_pago
    ALTER COLUMN id_instancia_pago SET STATISTICS 0;

  ALTER TABLE obingresos.tinstancia_pago
    ALTER COLUMN id_medio_pago SET STATISTICS 0;

  ALTER TABLE obingresos.tinstancia_pago
    ALTER COLUMN nombre SET STATISTICS 0;

  ALTER TABLE obingresos.tinstancia_pago
    ALTER COLUMN codigo SET STATISTICS 0;

  ALTER TABLE obingresos.tinstancia_pago
    ALTER COLUMN codigo_medio_pago SET STATISTICS 0;

  ALTER TABLE obingresos.tinstancia_pago
    OWNER TO postgres;


    CREATE TABLE obingresos.tmedio_pago_pw (
    id_medio_pago_pw SERIAL,
    medio_pago_id INTEGER,
    forma_pago_id INTEGER,
    name VARCHAR(100),
    mop_code VARCHAR(20),
    code VARCHAR(15),
    CONSTRAINT tmedio_pago_pw_pkey PRIMARY KEY(id_medio_pago_pw)
  ) INHERITS (pxp.tbase)
  WITH (oids = false);

  ALTER TABLE obingresos.tmedio_pago_pw
    ALTER COLUMN id_medio_pago_pw SET STATISTICS 0;

  ALTER TABLE obingresos.tmedio_pago_pw
    ALTER COLUMN medio_pago_id SET STATISTICS 0;

  ALTER TABLE obingresos.tmedio_pago_pw
    ALTER COLUMN forma_pago_id SET STATISTICS 0;

  ALTER TABLE obingresos.tmedio_pago_pw
    OWNER TO postgres;
/********************************************F-SCP-IRVA-OBINGRESOS-0-06/11/2019********************************************/

/********************************************I-SCP-IRVA-OBINGRESOS-0-25/11/2019********************************************/
ALTER TABLE obingresos.tmovimiento_entidad
  ADD COLUMN observaciones TEXT;
/********************************************F-SCP-IRVA-OBINGRESOS-0-25/11/2019********************************************/

/********************************************I-SCP-IRVA-OBINGRESOS-0-02/12/2019********************************************/
ALTER TABLE obingresos.tconsulta_viajero_frecuente
ADD COLUMN pnr VARCHAR(6);

ALTER TABLE obingresos.tconsulta_viajero_frecuente
ADD COLUMN estado VARCHAR(100);

ALTER TABLE obingresos.tconsulta_viajero_frecuente
ADD COLUMN status_canjeado VARCHAR(20);

ALTER TABLE obingresos.tconsulta_viajero_frecuente
ADD COLUMN message_canjeado VARCHAR(200);
/********************************************F-SCP-IRVA-OBINGRESOS-0-02/12/2019********************************************/

/********************************************I-SCP-IRVA-OBINGRESOS-0-24/12/2019********************************************/
ALTER TABLE obingresos.tacm_det
DROP CONSTRAINT tacm_det_fk1 RESTRICT;

ALTER TABLE obingresos.tacm_det
DROP CONSTRAINT tacm_det_idx RESTRICT;
/********************************************F-SCP-IRVA-OBINGRESOS-0-24/12/2019********************************************/

/********************************************I-SCP-IRVA-OBINGRESOS-0-03/02/2020********************************************/
CREATE TABLE obingresos.tboletos_banca (
  id_boleto_banca SERIAL,
  agencia_id INTEGER NOT NULL,
  emision_id INTEGER NOT NULL,
  transaccion_id INTEGER NOT NULL,
  pnr VARCHAR(10) NOT NULL,
  tkt VARCHAR(15),
  neto NUMERIC(20,2) NOT NULL,
  tasas NUMERIC(20,2) NOT NULL,
  monto_total NUMERIC(20,2) NOT NULL,
  comision NUMERIC(20,2) NOT NULL,
  moneda VARCHAR(3) NOT NULL,
  fecha_emision DATE,
  fecha_transaccion TIMESTAMP WITHOUT TIME ZONE,
  fecha_pago_banco DATE NOT NULL,
  forma_pago VARCHAR(50) NOT NULL,
  entidad_pago VARCHAR(20) NOT NULL,
  estado VARCHAR(20) NOT NULL,
  pasajero TEXT,
  CONSTRAINT tboletos_banca_pkey PRIMARY KEY(id_boleto_banca)
) INHERITS (pxp.tbase)
WITH (oids = false);

CREATE INDEX tboletos_banca_idx ON obingresos.tboletos_banca
  USING btree (fecha_emision, fecha_transaccion, fecha_pago_banco, moneda COLLATE pg_catalog."default", forma_pago COLLATE pg_catalog."default", agencia_id, emision_id);

ALTER TABLE obingresos.tboletos_banca
  OWNER TO postgres;

/********************************************F-SCP-IRVA-OBINGRESOS-0-03/02/2020********************************************/

/********************************************I-SCP-BVP-OBINGRESOS-0-10/07/2020********************************************/
CREATE TABLE obingresos.tmco_s (
  id_mco SERIAL,
  fecha_emision DATE NOT NULL,
  id_moneda INTEGER NOT NULL,
  motivo TEXT NOT NULL,
  valor_total NUMERIC(18,2),
  id_gestion INTEGER NOT NULL,
  id_punto_venta INTEGER,
  id_sucursal_usuario INTEGER,
  estado INTEGER NOT NULL,
  id_concepto_ingas INTEGER NOT NULL,
  id_boleto INTEGER,
  tipo_cambio NUMERIC(18,6),
  nro_mco VARCHAR,
  pax VARCHAR,
  id_funcionario_emisor INTEGER,
  CONSTRAINT tmco_s_pkey PRIMARY KEY(id_mco),
  CONSTRAINT tmco_s_fk1 FOREIGN KEY (id_moneda)
    REFERENCES param.tmoneda(id_moneda)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
    NOT DEFERRABLE,
  CONSTRAINT tmco_s_fk2 FOREIGN KEY (id_gestion)
    REFERENCES param.tgestion(id_gestion)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
    NOT DEFERRABLE
) INHERITS (pxp.tbase)
WITH (oids = false);

COMMENT ON COLUMN obingresos.tmco_s.motivo
IS 'Razon de emision de MCO.';

COMMENT ON COLUMN obingresos.tmco_s.valor_total
IS 'Importe del MCO.';

COMMENT ON COLUMN obingresos.tmco_s.id_concepto_ingas
IS 'Tipo de concepto global.';

ALTER TABLE obingresos.tmco_s
  OWNER TO postgres;
/********************************************F-SCP-BVP-OBINGRESOS-0-10/07/2020********************************************/

/********************************************I-SCP-BVP-OBINGRESOS-0-16/11/2020********************************************/

ALTER TABLE obingresos.tmedio_pago_pw
  ADD COLUMN sw_autorizacion VARCHAR(200) [];

ALTER TABLE obingresos.tmedio_pago_pw
  ADD COLUMN regionales VARCHAR(200) [];

ALTER TABLE obingresos.tmedio_pago_pw
 ADD CONSTRAINT tmedio_pago_pw_fk FOREIGN KEY (forma_pago_id)
   REFERENCES obingresos.tforma_pago_pw(id_forma_pago_pw)
   ON DELETE NO ACTION
   ON UPDATE NO ACTION
   NOT DEFERRABLE;


 ALTER TABLE obingresos.tinstancia_pago
   ADD CONSTRAINT tinstancia_pago_fk FOREIGN KEY (id_medio_pago)
     REFERENCES obingresos.tmedio_pago_pw(id_medio_pago_pw)
     ON DELETE NO ACTION
     ON UPDATE NO ACTION
     NOT DEFERRABLE;

/********************************************F-SCP-BVP-OBINGRESOS-0-16/11/2020********************************************/
/********************************************I-SCP-IRVA-OBINGRESOS-0-26/11/2020********************************************/
ALTER TABLE obingresos.tboleto_amadeus_forma_pago
  ADD COLUMN id_medio_pago INTEGER;

COMMENT ON COLUMN obingresos.tboleto_amadeus_forma_pago.id_medio_pago
IS 'Usar los nuevos medios de pago';

ALTER TABLE obingresos.tboleto_amadeus_forma_pago
  ADD COLUMN id_moneda INTEGER;

COMMENT ON COLUMN obingresos.tboleto_amadeus_forma_pago.id_moneda
IS 'Id moneda con la que se realiza el pago';

ALTER TABLE obingresos.tboleto_amadeus_forma_pago
  ALTER COLUMN id_forma_pago DROP NOT NULL;

ALTER TABLE obingresos.tforma_pago_ant
  ADD COLUMN id_medio_pago INTEGER;

COMMENT ON COLUMN obingresos.tforma_pago_ant.id_medio_pago
IS 'Campo para registrar el medio de pago';
/********************************************F-SCP-IRVA-OBINGRESOS-0-26/11/2020********************************************/
=======
/********************************************I-SCP-JRR-OBINGRESOS-0-10/12/2015********************************************/
CREATE TABLE obingresos.tcomprobante (
  id_comprobante SERIAL NOT NULL,
  pais VARCHAR(3) NOT NULL,
  comprobante VARCHAR(50),
  glosa TEXT,
  estado VARCHAR(1),
  fecha_reg DATE,
  retbsp VARCHAR(15),
  momento INTEGER,
  estacion CHAR(3),
  moneda_estacion CHAR(3),
  fecha_ini DATE,
  fecha_fin DATE,
  tipo_ingreso VARCHAR(15),
  cliente VARCHAR(100),
  id_int_comprobante INTEGER,
  CONSTRAINT tconta_encabezado_pkey PRIMARY KEY(id_comprobante)
);

CREATE TABLE obingresos.tcomprobante_det (
  id_comprobante_det SERIAL NOT NULL,
  id_comprobante INTEGER NOT NULL,
  pais VARCHAR(3),
  renglon INTEGER,
  id_presupuesto INTEGER,
  id_partida INTEGER,
  id_cuenta INTEGER,
  id_auxiliar INTEGER,
  tipo_movimiento VARCHAR(10),
  importe NUMERIC(18,2),
  importe_mb NUMERIC(18,2),
  importe_mt NUMERIC(18,2),
  tc_mb NUMERIC(18,2),
  tc_mt NUMERIC(18,2),
  moneda_transaccion VARCHAR(3),
  fecha_ven_ini DATE,
  fecha_ven_fin DATE,
  CONSTRAINT tcomprobante_det_pkey PRIMARY KEY(id_comprobante_det)
);

CREATE TABLE obingresos.tagencia (
  id_agencia SERIAL NOT NULL,
  codigo VARCHAR(20) NOT NULL,
  codigo_noiata VARCHAR(20),
  codigo_int VARCHAR(20),
  nombre VARCHAR(255) NOT NULL,
  tipo_agencia VARCHAR(25) NOT NULL,
  tipo_pago VARCHAR(25) NOT NULL,
  monto_maximo_deuda NUMERIC(18,2) NOT NULL,
  depositos_moneda_boleto VARCHAR(2) NOT NULL,
  tipo_cambio VARCHAR(10) NOT NULL,
  id_moneda_control INTEGER NOT NULL,
  CONSTRAINT tagencia_pkey PRIMARY KEY(id_agencia)
)
  INHERITS (pxp.tbase) WITHOUT OIDS;

CREATE SEQUENCE obingresos.tboleto_id_boleto_seq
INCREMENT 1 MINVALUE 1
MAXVALUE 9223372036854775807 START 1
CACHE 1;

CREATE TABLE obingresos.tboleto (
  id_boleto INTEGER DEFAULT nextval('obingresos.tboleto_id_boleto_seq'::regclass) NOT NULL,
  nro_boleto VARCHAR(50) NOT NULL,
  pasajero VARCHAR(100) NOT NULL,
  fecha_emision DATE NOT NULL,
  total NUMERIC(18,2) NOT NULL,
  comision NUMERIC(18,2),
  liquido NUMERIC(18,2) NOT NULL,
  id_moneda_boleto INTEGER NOT NULL,
  monto_pagado_moneda_boleto NUMERIC(18,2) NOT NULL,
  id_agencia INTEGER NOT NULL,
  moneda VARCHAR(5),
  agt VARCHAR(20),
  agtnoiata VARCHAR(20),
  neto NUMERIC(18,2) NOT NULL,
  CONSTRAINT tboleto_pkey PRIMARY KEY(id_boleto)
)
  INHERITS (pxp.tbase) WITHOUT OIDS;


CREATE TABLE obingresos.tdeposito (
  id_deposito SERIAL NOT NULL,
  nro_deposito VARCHAR(70) NOT NULL,
  monto_deposito NUMERIC(18,2) NOT NULL,
  id_moneda_deposito INTEGER,
  id_agencia INTEGER ,
  fecha DATE NOT NULL,
  saldo NUMERIC(18,2) NOT NULL,
  moneda VARCHAR(5),
  agt VARCHAR(20),
  agtnoiata VARCHAR(20),
  fecini DATE,
  fecfin DATE,
  observaciones TEXT,
  id_cuenta_bancaria INTEGER,
  CONSTRAINT tdeposito_pkey PRIMARY KEY(id_deposito)
)
  INHERITS (pxp.tbase) WITHOUT OIDS;




CREATE TABLE obingresos.tdeposito_boleto (
  id_deposito_boleto SERIAL NOT NULL,
  id_deposito INTEGER NOT NULL,
  id_boleto INTEGER NOT NULL,
  monto_moneda_boleto NUMERIC(18,2) NOT NULL,
  monto_moneda_deposito NUMERIC(18,2) NOT NULL,
  tc NUMERIC(12,6) NOT NULL,
  CONSTRAINT tdeposito_boleto_pkey PRIMARY KEY(id_deposito_boleto)
)
  INHERITS (pxp.tbase) WITHOUT OIDS;



/********************************************F-SCP-JRR-OBINGRESOS-0-10/12/2015********************************************/

/********************************************I-SCP-JRR-OBINGRESOS-0-02/02/2016********************************************/

ALTER TABLE obingresos.tagencia
ADD COLUMN agencia_ato CHAR(1) DEFAULT 'N' NOT NULL;

ALTER TABLE obingresos.tboleto
ADD COLUMN gds VARCHAR(5);

ALTER TABLE obingresos.tboleto
ADD COLUMN tipdoc VARCHAR(5) NOT NULL;

ALTER TABLE obingresos.tboleto
ADD COLUMN ruta VARCHAR(2);

ALTER TABLE obingresos.tboleto
ADD COLUMN cupones INTEGER NOT NULL;

ALTER TABLE obingresos.tboleto
ADD COLUMN origen VARCHAR(10);

ALTER TABLE obingresos.tboleto
ADD COLUMN destino varchar(10);

ALTER TABLE obingresos.tboleto
ADD COLUMN tipopax varchar(5);

ALTER TABLE obingresos.tboleto
ADD COLUMN retbsp VARCHAR(5) NOT NULL;

ALTER TABLE obingresos.tboleto
ADD COLUMN estado VARCHAR(20);

ALTER TABLE obingresos.tboleto
ADD COLUMN endoso VARCHAR(255);

CREATE TABLE obingresos.testacion (
  id_estacion SERIAL NOT NULL,
  codigo VARCHAR(20) NOT NULL,
  nombre VARCHAR(100) NOT NULL,
  id_lugar INTEGER NOT NULL,
  tipo_pais VARCHAR(5),
  CONSTRAINT testacion_pkey PRIMARY KEY(id_estacion)
)
  INHERITS (pxp.tbase) WITHOUT OIDS;


CREATE TABLE obingresos.tcomision (
  id_comision SERIAL NOT NULL,
  id_lugar INTEGER NOT NULL,
  porcentaje NUMERIC(5,2) NOT NULL,
  tipodoc varchar(20) NOT NULL,
  codigo VARCHAR(50),
  nombre VARCHAR(150),
  CONSTRAINT tcomision_pkey PRIMARY KEY(id_comision)
)
  INHERITS (pxp.tbase) WITHOUT OIDS;

CREATE TABLE obingresos.timpuesto (
  id_impuesto SERIAL NOT NULL,
  id_lugar INTEGER NOT NULL,
  porcentaje NUMERIC(5,2) NOT NULL,
  tipodoc varchar(20) NOT NULL,
  codigo VARCHAR(50),
  nombre VARCHAR(150),
  monto numeric(18,2),
  tipo varchar(5),
  CONSTRAINT timpuesto_pkey PRIMARY KEY(id_impuesto)
)
  INHERITS (pxp.tbase) WITHOUT OIDS;

CREATE TABLE obingresos.tforma_pago (
  id_forma_pago SERIAL NOT NULL,
  codigo VARCHAR(20) NOT NULL,
  nombre VARCHAR(100) NOT NULL,
  id_moneda INTEGER NOT NULL,
  id_lugar INTEGER NOT NULL,
  ctacte VARCHAR(2) NOT NULL,
  CONSTRAINT tforma_pago_pkey PRIMARY KEY(id_forma_pago)
)
  INHERITS (pxp.tbase) WITHOUT OIDS;

CREATE TABLE obingresos.tboleto_comision (
  id_boleto_comision SERIAL NOT NULL,
  importe NUMERIC(18,2) NOT NULL,
  id_comision INTEGER NOT NULL,
  id_boleto INTEGER NOT NULL,
  CONSTRAINT tboleto_comision_pkey PRIMARY KEY(id_boleto_comision)
)
  INHERITS (pxp.tbase) WITHOUT OIDS;

CREATE TABLE obingresos.tboleto_impuesto (
  id_boleto_impuesto SERIAL NOT NULL,
  importe NUMERIC(18,2) NOT NULL,
  id_impuesto INTEGER NOT NULL,
  id_boleto INTEGER NOT NULL,
  CONSTRAINT tboleto_impuesto_pkey PRIMARY KEY(id_boleto_impuesto)
)
  INHERITS (pxp.tbase) WITHOUT OIDS;

CREATE TABLE obingresos.tboleto_forma_pago (
  id_boleto_forma_pago SERIAL NOT NULL,
  importe NUMERIC(18,2) NOT NULL,
  id_forma_pago INTEGER NOT NULL,
  id_boleto INTEGER NOT NULL,
  tipo VARCHAR(20),
  tarjeta VARCHAR(6) ,
  numero_tarjeta VARCHAR(20),
  ctacte VARCHAR(20),
  CONSTRAINT tboleto_forma_pago_pkey PRIMARY KEY(id_boleto_forma_pago)
)
  INHERITS (pxp.tbase) WITHOUT OIDS;

/********************************************F-SCP-JRR-OBINGRESOS-0-02/02/2016********************************************/

/********************************************I-SCP-JRR-OBINGRESOS-0-08/04/2016********************************************/

CREATE TABLE obingresos.tperiodo_venta (
  id_periodo_venta SERIAL NOT NULL,
  mes VARCHAR(30) NOT NULL,
  nro_periodo_mes INTEGER NOT NULL,
  fecha_ini DATE NOT NULL,
  fecha_fin DATE NOT NULL,
  tipo VARCHAR(20) NOT NULL,
  estado VARCHAR (15) NOT NULL,
  id_pais	INTEGER NOT NULL,
  id_gestion INTEGER NOT NULL,
  CONSTRAINT tperiodo_venta_pkey PRIMARY KEY(id_periodo_venta)
)
  INHERITS (pxp.tbase) WITHOUT OIDS;

ALTER TABLE obingresos.tagencia
ADD COLUMN id_lugar INTEGER NOT NULL;

ALTER TABLE obingresos.tagencia
ADD COLUMN boaagt VARCHAR(2);

CREATE TABLE obingresos.taeropuerto (
  id_aeropuerto SERIAL NOT NULL,
  codigo VARCHAR(5) NOT NULL,
  id_lugar INTEGER NOT NULL,
  nombre VARCHAR(100) NOT NULL,
  tipo_nalint VARCHAR(1) NOT NULL,
  PRIMARY KEY(id_aeropuerto)
) INHERITS (pxp.tbase)
;

ALTER TABLE obingresos.tboleto
ADD COLUMN tc NUMERIC(18,7);

ALTER TABLE obingresos.tboleto
ADD COLUMN moneda_sucursal VARCHAR(3);

ALTER TABLE obingresos.tboleto
ADD COLUMN id_usuario_cajero INTEGER;

ALTER TABLE obingresos.tboleto
ADD COLUMN id_punto_venta INTEGER;

ALTER TABLE obingresos.tboleto
ADD COLUMN ruta_completa VARCHAR(255);
/********************************************F-SCP-JRR-OBINGRESOS-0-08/04/2016********************************************/

/********************************************I-SCP-JRR-OBINGRESOS-0-19/07/2016********************************************/
CREATE TABLE obingresos.tboleto_vuelo (
  id_boleto_vuelo SERIAL NOT NULL,
  id_boleto INTEGER NOT NULL,
  fecha DATE NOT NULL,
  hora_origen TIME(0) WITHOUT TIME ZONE NOT NULL,
  hora_destino TIME(0) WITHOUT TIME ZONE,
  vuelo VARCHAR(7) NOT NULL,
  id_aeropuerto_origen INTEGER NOT NULL,
  id_aeropuerto_destino INTEGER NOT NULL,
  tarifa VARCHAR(7) NOT NULL,
  equipaje VARCHAR(7) NOT NULL,
  status VARCHAR(20) NOT NULL,
  id_boleto_conjuncion INTEGER,
  CONSTRAINT tboleto_vuelo_pkey PRIMARY KEY(id_boleto_vuelo)
) INHERITS (pxp.tbase);

ALTER TABLE obingresos.tboleto
ADD COLUMN tiene_conjuncion VARCHAR(2);

ALTER TABLE obingresos.tboleto
ADD COLUMN id_boleto_conjuncion INTEGER;

ALTER TABLE obingresos.tboleto
ADD COLUMN localizador VARCHAR(10);

ALTER TABLE obingresos.tboleto
ADD COLUMN identificacion VARCHAR(30);

ALTER TABLE obingresos.tboleto
ADD COLUMN xt NUMERIC(18,2) DEFAULT 0 NOT NULL;

ALTER TABLE obingresos.tboleto
ADD COLUMN mensaje_error TEXT;

ALTER TABLE obingresos.tboleto_impuesto
ADD COLUMN calculo_tarifa VARCHAR(2) DEFAULT 'no' NOT NULL;


/********************************************F-SCP-JRR-OBINGRESOS-0-19/07/2016********************************************/

/********************************************I-SCP-JRR-OBINGRESOS-0-30/09/2016********************************************/

ALTER TABLE obingresos.tboleto_forma_pago
ADD COLUMN codigo_tarjeta VARCHAR(20);

/********************************************F-SCP-JRR-OBINGRESOS-0-30/09/2016********************************************/

/********************************************I-SCP-JRR-OBINGRESOS-0-09/11/2016********************************************/

CREATE INDEX tboleto_forma_pago__id_boleto_idx ON obingresos.tboleto_forma_pago
USING btree (id_boleto);

CREATE INDEX tboleto_impuesto__id_boleto_idx ON obingresos.tboleto_impuesto
USING btree (id_boleto);

CREATE INDEX tboleto_vuelo__id_boleto_idx ON obingresos.tboleto_vuelo
USING btree (id_boleto);

/********************************************F-SCP-JRR-OBINGRESOS-0-09/11/2016********************************************/

/********************************************I-SCP-JRR-OBINGRESOS-0-14/11/2016********************************************/

CREATE TABLE obingresos.tdetalle_boletos_web (
  billete VARCHAR(15) NOT NULL,
  conjuncion VARCHAR(255),
  medio_pago VARCHAR(50) NOT NULL,
  entidad_pago VARCHAR(50) NOT NULL,
  moneda VARCHAR(3) NOT NULL,
  importe NUMERIC(18,2) NOT NULL,
  endoso VARCHAR(500),
  procesado VARCHAR(2) DEFAULT 'no'::character varying NOT NULL,
  origen VARCHAR(20) DEFAULT 'servicio'::character varying,
  id_detalle_boletos_web SERIAL,
  fecha DATE NOT NULL,
  nit VARCHAR(30),
  razon_social VARCHAR(300),
  void VARCHAR(2) DEFAULT 'no'::character varying,
  CONSTRAINT tdetalle_boletos_web_billete_key UNIQUE(billete),
  CONSTRAINT tdetalle_boletos_web_pkey PRIMARY KEY(id_detalle_boletos_web)
) INHERITS (pxp.tbase)

WITH (oids = false);

ALTER TABLE obingresos.tdetalle_boletos_web
ALTER COLUMN origen SET STATISTICS 0;

ALTER TABLE obingresos.tdetalle_boletos_web
ALTER COLUMN id_detalle_boletos_web SET STATISTICS 0;

ALTER TABLE obingresos.tdetalle_boletos_web
ALTER COLUMN fecha SET STATISTICS 0;

CREATE TYPE obingresos.detalle_boletos AS (
  "Billete" VARCHAR(15),
  "CNJ" VARCHAR(255),
  "MedioDePago" VARCHAR(50),
  "Entidad" VARCHAR(50),
  "Moneda" VARCHAR(3),
  "ImportePasaje" NUMERIC(18,2),
  "ImporteTarifa" NUMERIC(18,2),
  "OrigenDestino" VARCHAR(255),
  endoso VARCHAR(500)
);

ALTER TABLE obingresos.tboleto
ADD COLUMN nit_ingresos VARCHAR(20);

ALTER TABLE obingresos.tboleto
ADD COLUMN razon_ingresos VARCHAR(300);


/********************************************F-SCP-JRR-OBINGRESOS-0-14/11/2016********************************************/

/********************************************I-SCP-JRR-OBINGRESOS-0-23/11/2016********************************************/

CREATE UNIQUE INDEX tforma_pago_idx ON obingresos.tforma_pago
USING btree (codigo COLLATE pg_catalog."default", id_lugar, id_moneda);

ALTER TABLE obingresos.tboleto
ADD COLUMN voided VARCHAR(2) DEFAULT 'no'::character varying NOT NULL;

ALTER TABLE obingresos.tboleto
ADD COLUMN nit BIGINT;

ALTER TABLE obingresos.tboleto
ADD COLUMN razon VARCHAR(200);

ALTER TABLE obingresos.tboleto
ADD COLUMN medio_pago VARCHAR(20);

ALTER TABLE obingresos.tboleto_vuelo
ADD COLUMN cupon SMALLINT NOT NULL;

ALTER TABLE obingresos.tboleto
ALTER COLUMN id_moneda_boleto DROP NOT NULL;

ALTER TABLE obingresos.tboleto
ALTER COLUMN pasajero DROP NOT NULL;

ALTER TABLE obingresos.tboleto_vuelo
ADD COLUMN linea VARCHAR(5);

ALTER TABLE obingresos.tboleto_vuelo
ADD COLUMN aeropuerto_origen VARCHAR(10);

ALTER TABLE obingresos.tboleto_vuelo
ADD COLUMN aeropuerto_destino VARCHAR(10);

ALTER TABLE obingresos.tboleto_vuelo
ALTER COLUMN tarifa TYPE VARCHAR(15) COLLATE pg_catalog."default";

ALTER TABLE obingresos.tboleto_vuelo
ALTER COLUMN hora_origen DROP NOT NULL;

ALTER TABLE obingresos.tboleto_vuelo
ALTER COLUMN fecha DROP NOT NULL;

ALTER TABLE obingresos.tboleto
ALTER COLUMN localizador TYPE VARCHAR(13) COLLATE pg_catalog."default";
/********************************************F-SCP-JRR-OBINGRESOS-0-23/11/2016********************************************/

/********************************************I-SCP-MAM-OBINGRESOS-0-25/11/2016********************************************/
ALTER TABLE obingresos.tdeposito
ADD COLUMN descripcion TEXT;

ALTER TABLE obingresos.tdeposito
ADD COLUMN pnr VARCHAR(20);

/********************************************F-SCP-MAM-OBINGRESOS-0-25/11/2016********************************************/

/********************************************I-SCP-JRR-OBINGRESOS-0-25/11/2016********************************************/
CREATE UNIQUE INDEX tdeposito_idx ON obingresos.tdeposito
USING btree (nro_deposito COLLATE pg_catalog."default", tipo COLLATE pg_catalog."default");

CREATE INDEX tdeposito_idx1 ON obingresos.tdeposito
USING btree (pnr);

ALTER TABLE obingresos.tdeposito
ADD COLUMN tipo VARCHAR(15) DEFAULT 'agencia' NOT NULL;

/********************************************F-SCP-JRR-OBINGRESOS-0-25/11/2016********************************************/

/********************************************I-SCP-JRR-OBINGRESOS-0-19/01/2017********************************************/
CREATE TABLE obingresos.tventa_web_modificaciones (
  nro_boleto VARCHAR(20) NOT NULL,
  tipo VARCHAR(15),
  nro_boleto_reemision VARCHAR(20),
  used VARCHAR(2),
  motivo TEXT NOT NULL,
  id_venta_web_modificaciones SERIAL,
  procesado VARCHAR(2) DEFAULT 'no'::character varying NOT NULL,
  CONSTRAINT tventa_web_modificaciones_nro_boleto_key UNIQUE(nro_boleto),
  CONSTRAINT tventa_web_modificaciones_pkey PRIMARY KEY(id_venta_web_modificaciones),
  CONSTRAINT tventa_web_modificaciones_chk CHECK (((tipo)::text = 'tsu_anulado'::text) OR ((tipo)::text = 'anulado'::text) OR ((tipo)::text = 'reemision'::text))
) INHERITS (pxp.tbase)

WITH (oids = false);

CREATE UNIQUE INDEX tventa_web_modificaciones_idx ON obingresos.tventa_web_modificaciones
USING btree (nro_boleto_reemision COLLATE pg_catalog."default")
  WHERE ((nro_boleto_reemision IS NOT NULL) AND ((nro_boleto_reemision)::text <> ''::text));

CREATE TABLE obingresos.tboleto_retweb (
  id_boleto_retweb SERIAL,
  nro_boleto VARCHAR(20) NOT NULL,
  pasajero VARCHAR(100),
  fecha_emision DATE NOT NULL,
  total NUMERIC(18,2),
  moneda VARCHAR(5),
  tarjeta VARCHAR(5) NOT NULL,
  numero_tarjeta VARCHAR(30),
  estado VARCHAR(2) NOT NULL,
  CONSTRAINT tboleto_retweb_pkey PRIMARY KEY(id_boleto_retweb)
) INHERITS (pxp.tbase)

WITH (oids = false);

ALTER TABLE obingresos.tboleto_retweb
ALTER COLUMN nro_boleto SET STATISTICS 0;

CREATE INDEX tboleto_retweb_idx ON obingresos.tboleto_retweb
USING btree (fecha_emision);

CREATE UNIQUE INDEX tboleto_retweb_idx1 ON obingresos.tboleto_retweb
USING btree (nro_boleto COLLATE pg_catalog."default");

/********************************************F-SCP-JRR-OBINGRESOS-0-19/01/2017********************************************/


/********************************************I-SCP-FFP-OBINGRESOS-0-15/02/2017********************************************/

CREATE TABLE obingresos.tskybiz_archivo (
  id_skybiz_archivo SERIAL,
  nombre_archivo VARCHAR(255),
  subido VARCHAR(255),
  comentario VARCHAR(255),
  fecha DATE,
  PRIMARY KEY(id_skybiz_archivo)
) INHERITS (pxp.tbase)
WITH (oids = false);




CREATE TYPE obingresos.json_ins_skybiz_archivo AS (
  nombre_archivo VARCHAR(255),
  subido VARCHAR(255),
  comentario VARCHAR(255),
  moneda VARCHAR(255)
);

ALTER TABLE obingresos.tskybiz_archivo ADD moneda VARCHAR(255) NULL;


CREATE TABLE obingresos.tskybiz_archivo_detalle (
  id_skybiz_archivo_detalle SERIAL,
  id_skybiz_archivo INTEGER,
  entity VARCHAR(255),
  ip VARCHAR(255),
  request_date_time VARCHAR(255),
  issue_date_time VARCHAR(255),
  pnr VARCHAR(255),
  identifier_pnr VARCHAR(255),
  authorization_ VARCHAR(255),
  total_amount NUMERIC(10,2),
  currency VARCHAR(255),
  status VARCHAR(255),
  PRIMARY KEY(id_skybiz_archivo_detalle)
) INHERITS (pxp.tbase)
WITH (oids = false);


CREATE TYPE obingresos.json_ins_skybiz_archivo_detalle AS (
  entity                    VARCHAR(255),
  ip                        VARCHAR(255),
  request_date_time         VARCHAR(255),
  issue_date_time           VARCHAR(255),
  pnr                       VARCHAR(255),
  identifier_pnr            VARCHAR(255),
  authorization_            VARCHAR(255),
  total_amount              VARCHAR(255),
  currency                  VARCHAR(255),
  status                    VARCHAR(255),
  nombre_archivo            VARCHAR(255)
);


/********************************************F-SCP-FFP-OBINGRESOS-0-15/02/2017********************************************/

/********************************************I-SCP-JRR-OBINGRESOS-0-01/03/2017********************************************/
DROP INDEX obingresos.tdeposito_idx;

CREATE INDEX tdeposito_idx ON obingresos.tdeposito
  USING btree (nro_deposito COLLATE pg_catalog."default", tipo COLLATE pg_catalog."default");

/********************************************F-SCP-JRR-OBINGRESOS-0-01/03/2017********************************************/

/********************************************I-SCP-FFP-OBINGRESOS-0-01/03/2017********************************************/

ALTER TABLE param.tcolumnas_archivo_excel ALTER COLUMN formato_fecha TYPE VARCHAR(20) USING formato_fecha::VARCHAR(20);

DROP TYPE obingresos.json_ins_skybiz_archivo_detalle;
CREATE TYPE obingresos.json_ins_skybiz_archivo_detalle AS (
  entity                    VARCHAR(255),
  ip                        VARCHAR(255),
  request_date_time         TIMESTAMP,
  issue_date_time           TIMESTAMP,
  pnr                       VARCHAR(255),
  identifier_pnr            VARCHAR(255),
  authorization_            VARCHAR(255),
  total_amount              VARCHAR(255),
  currency                  VARCHAR(255),
  status                    VARCHAR(255),
  nombre_archivo            VARCHAR(255)
);

ALTER TABLE obingresos.tskybiz_archivo ADD banco VARCHAR(255) NULL;

select pxp.f_insert_tgui ('Skybiz', 'Skybiz', 'SKYBIZ', 'si', 8, 'sis_obingresos/vista/skybiz_archivo/SkybizArchivo.php', 2, '', 'SkybizArchivo', 'OBINGRESOS');
select pxp.f_insert_testructura_gui ('SKYBIZ', 'OBINGRESOS');


/********************************************F-SCP-FFP-OBINGRESOS-0-01/03/2017********************************************/

/********************************************I-SCP-JRR-OBINGRESOS-0-02/03/2017********************************************/
ALTER TABLE obingresos.tdeposito
ADD COLUMN fecha_venta DATE;

ALTER TABLE obingresos.tdeposito
ADD COLUMN monto_total NUMERIC(18,2);

/********************************************F-SCP-JRR-OBINGRESOS-0-02/03/2017********************************************/


/********************************************I-SCP-JRR-OBINGRESOS-0-20/03/2017********************************************/
ALTER TABLE obingresos.tboleto
  ADD COLUMN fare_calc TEXT;

/********************************************F-SCP-JRR-OBINGRESOS-0-20/03/2017********************************************/

/********************************************I-SCP-JRR-OBINGRESOS-0-24/03/2017********************************************/
ALTER TABLE obingresos.tboleto_vuelo
  ADD COLUMN fecha_hora_origen TIMESTAMP(0) WITHOUT TIME ZONE;

ALTER TABLE obingresos.tboleto_vuelo
  ADD COLUMN fecha_hora_destino TIMESTAMP(0) WITHOUT TIME ZONE;

ALTER TABLE obingresos.tboleto_vuelo
  ADD COLUMN tiempo_conexion INTEGER;

ALTER TABLE obingresos.tboleto_vuelo
  ADD COLUMN retorno VARCHAR(10);

ALTER TABLE obingresos.tboleto_vuelo
  ADD COLUMN clase VARCHAR(5);

ALTER TABLE obingresos.tboleto_vuelo
  ADD COLUMN flight_status VARCHAR(5);



/********************************************F-SCP-JRR-OBINGRESOS-0-24/03/2017********************************************/

/********************************************I-SCP-JRR-OBINGRESOS-0-22/03/2017********************************************/
ALTER TABLE obingresos.tventa_web_modificaciones
ADD COLUMN fecha_reserva_antigua DATE;

ALTER TABLE obingresos.tventa_web_modificaciones
ADD COLUMN pnr_antiguo VARCHAR (20);

ALTER TABLE obingresos.tventa_web_modificaciones
  ADD COLUMN banco VARCHAR(5);

/********************************************F-SCP-JRR-OBINGRESOS-0-22/03/2017********************************************/

/********************************************I-SCP-JRR-OBINGRESOS-0-07/04/2017********************************************/
ALTER TABLE obingresos.tboleto_vuelo
  ADD COLUMN validez_tarifa INTEGER;

  CREATE TABLE obingresos.tclase_tarifaria (
  id_clase_tarifaria SERIAL,
  codigo VARCHAR(1),
  tipo_condicion VARCHAR(15) NOT NULL,
  ruta VARCHAR(5)[],
  aeropuerto VARCHAR(5)[],
  pais VARCHAR(2),
  duracion_meses INTEGER,
  CONSTRAINT tclase_tarifaria_pkey PRIMARY KEY(id_clase_tarifaria)
) INHERITS (pxp.tbase);

/********************************************F-SCP-JRR-OBINGRESOS-0-07/04/2017********************************************/

/********************************************I-SCP-JRR-OBINGRESOS-0-28/04/2017********************************************/
CREATE TABLE obingresos.tcomision_agencia (
  id_comision SERIAL,
  id_contrato INTEGER,
  id_agencia INTEGER,
  descripcion VARCHAR(255),
  tipo_comision VARCHAR(10),
  mercado VARCHAR(20),
  porcentaje NUMERIC(5,2),
  moneda VARCHAR(3),
  limite_superior NUMERIC(18,2),
  limite_inferior NUMERIC(18,2),
  PRIMARY KEY(id_comision)
) INHERITS (pxp.tbase)
WITH (oids = false);

/********************************************F-SCP-JRR-OBINGRESOS-0-28/04/2017********************************************/


/********************************************I-SCP-JRR-OBINGRESOS-0-02/05/2017********************************************/

CREATE TABLE obingresos.ttipo_periodo (
  id_tipo_periodo SERIAL,
  tipo VARCHAR(20) NOT NULL,
  tiempo VARCHAR(20) NOT NULL,
  medio_pago VARCHAR(30) NOT NULL,
  tipo_cc VARCHAR(20),
  pago_comision VARCHAR(2),
  estado VARCHAR(10) NOT NULL,
  fecha_ini_primer_periodo DATE,
  CONSTRAINT ttipo_periodo_pkey PRIMARY KEY(id_tipo_periodo)
) INHERITS (pxp.tbase)

WITH (oids = false);

COMMENT ON COLUMN obingresos.ttipo_periodo.tipo
IS 'portal,venta_propia';

COMMENT ON COLUMN obingresos.ttipo_periodo.tiempo
IS 'bsp,1d,2d,5d';

COMMENT ON COLUMN obingresos.ttipo_periodo.medio_pago
IS 'banca_electronica,cuenta_corriente';

COMMENT ON COLUMN obingresos.ttipo_periodo.tipo_cc
IS 'prepago,postpago';

COMMENT ON COLUMN obingresos.ttipo_periodo.pago_comision
IS 'si,no';

COMMENT ON COLUMN obingresos.ttipo_periodo.estado
IS 'activo,inactivo';

CREATE UNIQUE INDEX tskybiz_archivo_nombre_archivo_uindex ON obingresos.tskybiz_archivo (nombre_archivo);

/********************************************F-SCP-JRR-OBINGRESOS-0-02/05/2017********************************************/



/********************************************I-SCP-FFP-OBINGRESOS-0-04/05/2017********************************************/
ALTER TABLE obingresos.tagencia
  ADD COLUMN tipo_persona VARCHAR(15);

ALTER TABLE obingresos.tperiodo_venta
  ADD COLUMN id_tipo_periodo INTEGER;

ALTER TABLE obingresos.tperiodo_venta
  DROP COLUMN id_pais;

ALTER TABLE obingresos.tperiodo_venta
  DROP COLUMN tipo;

COMMENT ON COLUMN obingresos.tagencia.tipo_persona
IS 'juridica|natural';

CREATE TABLE obingresos.tmovimiento_entidad (
  id_movimiento_entidad SERIAL,
  tipo VARCHAR(8) NOT NULL,
  pnr VARCHAR(8),
  fecha DATE NOT NULL,
  apellido VARCHAR(200),
  monto NUMERIC(18,2) NOT NULL,
  id_moneda INTEGER NOT NULL,
  autorizacion__nro_deposito VARCHAR(200),
  garantia VARCHAR(2) NOT NULL,
  ajuste VARCHAR(2) NOT NULL,
  id_periodo_venta INTEGER,
  id_agencia INTEGER NOT NULL,
  monto_total NUMERIC(18,2) NOT NULL,
  CONSTRAINT tmovimiento_entidad_pkey PRIMARY KEY(id_movimiento_entidad)
) INHERITS (pxp.tbase)

WITH (oids = false);

CREATE TABLE obingresos.tperiodo_venta_agencia (
  id_periodo_venta_agencia SERIAL NOT NULL,
  id_agencia INTEGER NOT NULL,
  id_periodo_venta INTEGER NOT NULL,
  monto_usd NUMERIC(18,2) NOT NULL,
  monto_mb NUMERIC(18,2) NOT NULL,
  deposito_mb NUMERIC(18,2) NOT NULL,
  deposito_usd NUMERIC(18,2) NOT NULL,
  estado VARCHAR(15) NOT NULL,
  fecha_cierre DATE NOT NULL,
  total_mb_cierre NUMERIC(18,2) NOT NULL,
  total_mb_pagado NUMERIC(18,2) NOT NULL,
  PRIMARY KEY(id_periodo_venta_agencia)
) INHERITS (pxp.tbase)
;

ALTER TABLE obingresos.tboleto
  ADD COLUMN id_periodo_venta INTEGER;

ALTER TABLE obingresos.tdeposito
  ADD COLUMN id_periodo_venta INTEGER;



ALTER TABLE obingresos.tdeposito
  ADD COLUMN estado VARCHAR(10) COLLATE pg_catalog."default";

ALTER TABLE obingresos.tdeposito
  ALTER COLUMN estado SET DEFAULT 'borrador'::character varying;

ALTER TABLE obingresos.tperiodo_venta_agencia
  ADD COLUMN moneda_restrictiva VARCHAR(2) DEFAULT 'si' NOT NULL;

/********************************************F-SCP-FFP-OBINGRESOS-0-04/05/2017********************************************/






/********************************************I-SCP-FFP-OBINGRESOS-0-31/05/2017********************************************/
CREATE TABLE obingresos.tobservaciones_conciliacion (
  id_observaciones_conciliacion SERIAL NOT NULL,
  tipo_observacion VARCHAR(20) NOT NULL,
  observacion TEXT NOT NULL,
  fecha_observacion DATE NOT NULL,
  banco VARCHAR(30),
  PRIMARY KEY(id_observaciones_conciliacion)
) INHERITS (pxp.tbase)
;

COMMENT ON COLUMN obingresos.tobservaciones_conciliacion.tipo_observacion
IS 'skybiz,portal';

/********************************************F-SCP-FFP-OBINGRESOS-0-31/05/2017********************************************/



/********************************************I-SCP-GSS-OBINGRESOS-0-13/07/2017********************************************/

ALTER TABLE obingresos.tboleto
  ADD COLUMN pnr VARCHAR(6);

/********************************************F-SCP-GSS-OBINGRESOS-0-13/07/2017********************************************/

/********************************************I-SCP-FFP-OBINGRESOS-0-16/06/2017********************************************/
ALTER TABLE obingresos.tdetalle_boletos_web
  ADD COLUMN fecha_pago DATE;

ALTER TABLE obingresos.tdetalle_boletos_web
  ADD COLUMN id_agencia INTEGER;

ALTER TABLE obingresos.tdetalle_boletos_web
  ADD COLUMN comision NUMERIC(18,2);

ALTER TABLE obingresos.tdetalle_boletos_web
  ADD COLUMN numero_tarjeta VARCHAR(20);

ALTER TABLE obingresos.tdetalle_boletos_web
  ADD COLUMN numero_autorizacion VARCHAR(8);

CREATE TYPE obingresos.detalle_boletos_portal AS (
  "Billete" VARCHAR(15),
  "CNJ" VARCHAR(255),
  "MedioDePago" VARCHAR(50),
  "Entidad" VARCHAR(50),
  "Moneda" VARCHAR(3),
  "ImportePasaje" NUMERIC(18,2),
  "ImporteTarifa" NUMERIC(18,2),
  "OrigenDestino" VARCHAR(255),
  "Nit" VARCHAR(30),
  "RazonSocial" VARCHAR(255),
  "FechaPago" VARCHAR(20),
  "idEntidad" INTEGER,
  "Comision" NUMERIC(18,2),
  "NumeroTarjeta" VARCHAR(20),
  "NumeroAutorizacion" VARCHAR(8),
  "FechaEmision" VARCHAR(20)
);

/********************************************F-SCP-FFP-OBINGRESOS-0-16/06/2017********************************************/

/********************************************I-SCP-JRR-OBINGRESOS-0-24/07/2017********************************************/


ALTER TABLE obingresos.tdetalle_boletos_web
  ADD COLUMN id_periodo_venta INTEGER;

ALTER TABLE obingresos.tboleto_retweb
  ADD COLUMN id_moneda INTEGER;

ALTER TABLE obingresos.tboleto_retweb
  ADD COLUMN comision NUMERIC(18,2);

ALTER TABLE obingresos.tboleto_retweb
  ADD COLUMN pnr VARCHAR(20);

ALTER TABLE obingresos.tmovimiento_entidad
  ADD COLUMN billete VARCHAR(20);

 ALTER TABLE obingresos.tboleto_retweb
  ADD COLUMN id_agencia INTEGER;

ALTER TABLE obingresos.tdetalle_boletos_web
  ADD COLUMN id_moneda INTEGER;

ALTER TABLE obingresos.tperiodo_venta_agencia
  ALTER COLUMN deposito_mb SET DEFAULT 0;

ALTER TABLE obingresos.tperiodo_venta_agencia
  ALTER COLUMN deposito_usd SET DEFAULT 0;

ALTER TABLE obingresos.tperiodo_venta_agencia
  DROP COLUMN total_mb_cierre;

ALTER TABLE obingresos.tperiodo_venta_agencia
  DROP COLUMN total_mb_pagado;
/********************************************F-SCP-JRR-OBINGRESOS-0-24/07/2017********************************************/


/********************************************I-SCP-JRR-OBINGRESOS-0-04/08/2017********************************************/
ALTER TABLE obingresos.tagencia
  ADD COLUMN nit VARCHAR(30);

ALTER TABLE obingresos.tdetalle_boletos_web
  ADD COLUMN neto NUMERIC(18,2);

ALTER TABLE obingresos.tboleto_retweb
  ALTER COLUMN tarjeta DROP NOT NULL;

ALTER TABLE obingresos.tboleto_retweb
  ADD COLUMN forma_pago VARCHAR(100);

ALTER TABLE obingresos.tboleto_retweb
  ADD COLUMN neto NUMERIC(18,2);

CREATE TABLE obingresos.tobservaciones_portal (
  id_observaciones_portal SERIAL NOT NULL,
  billete VARCHAR(15) ,
  pnr VARCHAR(15) ,
  total NUMERIC(18,2) NOT NULL,
  moneda VARCHAR(3),
  tipo_observacion VARCHAR(20) NOT NULL,
  observacion TEXT NOT NULL,
  PRIMARY KEY(id_observaciones_portal)
) INHERITS (pxp.tbase)
;

ALTER TABLE obingresos.tdetalle_boletos_web
  ALTER COLUMN numero_autorizacion TYPE VARCHAR(200) COLLATE pg_catalog."default";

ALTER TABLE obingresos.tperiodo_venta
  ADD COLUMN codigo_periodo VARCHAR(15) NOT NULL;

ALTER TABLE obingresos.tmovimiento_entidad
  ADD COLUMN neto NUMERIC(18,2);



ALTER TABLE obingresos.tperiodo_venta_agencia
  ADD COLUMN monto_credito_mb NUMERIC(18,2);

ALTER TABLE obingresos.tperiodo_venta_agencia
  ADD COLUMN monto_credito_usd NUMERIC(18,2);

ALTER TABLE obingresos.tperiodo_venta_agencia
  ADD COLUMN monto_debito_mb NUMERIC(18,2);

ALTER TABLE obingresos.tperiodo_venta_agencia
  ADD COLUMN monto_debito_usd NUMERIC(18,2);

ALTER TABLE obingresos.tperiodo_venta_agencia
  ADD COLUMN monto_boletos_mb NUMERIC(18,2);

ALTER TABLE obingresos.tperiodo_venta_agencia
  ADD COLUMN monto_boletos_usd NUMERIC(18,2);

ALTER TABLE obingresos.tperiodo_venta_agencia
  ADD COLUMN monto_neto_mb NUMERIC(18,2);

ALTER TABLE obingresos.tperiodo_venta_agencia
  ADD COLUMN monto_neto_usd NUMERIC(18,2);

ALTER TABLE obingresos.tperiodo_venta_agencia
  ADD COLUMN monto_comision_mb NUMERIC(18,2);

ALTER TABLE obingresos.tperiodo_venta_agencia
  ADD COLUMN monto_comision_usd NUMERIC(18,2);


ALTER TABLE obingresos.tperiodo_venta_agencia
  ADD COLUMN total_comision_mb NUMERIC(18,2);

ALTER TABLE obingresos.tobservaciones_portal
  ADD COLUMN fecha_emision DATE;
/********************************************F-SCP-JRR-OBINGRESOS-0-04/08/2017********************************************/

/********************************************I-SCP-JRR-OBINGRESOS-0-17/08/2017********************************************/
CREATE TABLE obingresos.ttotal_comision_mes (
  gestion NUMERIC(4,0) NOT NULL,
  periodo NUMERIC(2,0) NOT NULL,
  total_comision NUMERIC(18,2) NOT NULL,
  id_tipo_periodo INTEGER NOT NULL,
  id_periodos INTEGER[] NOT NULL
) INHERITS (pxp.tbase)
;

ALTER TABLE obingresos.ttotal_comision_mes
  ADD COLUMN estado VARCHAR(15);

COMMENT ON COLUMN obingresos.ttotal_comision_mes.estado
IS 'pendiente o verificado';

ALTER TABLE obingresos.ttotal_comision_mes
  ADD COLUMN id_total_comision_mes SERIAL NOT NULL PRIMARY KEY;

ALTER TABLE obingresos.ttotal_comision_mes
  ADD COLUMN id_agencia INTEGER;

CREATE RULE ttotal_comision_mes_rl AS ON INSERT TO obingresos.ttotal_comision_mes
WHERE NEW.total_comision = 0
DO INSTEAD NOTHING;

ALTER TABLE obingresos.tagencia
  ADD COLUMN terciariza VARCHAR(2);

ALTER TABLE obingresos.tagencia
  ALTER COLUMN terciariza SET DEFAULT 'no';

ALTER TABLE obingresos.tagencia
  ADD COLUMN id_agencia_terciarizada INTEGER;

ALTER TABLE obingresos.tmovimiento_entidad
  ADD COLUMN comision_terciarizada NUMERIC(18,2);

ALTER TABLE obingresos.tperiodo_venta
  ADD COLUMN fecha_pago DATE;

/********************************************F-SCP-JRR-OBINGRESOS-0-17/08/2017********************************************/

/********************************************I-SCP-JRR-OBINGRESOS-0-10/09/2017********************************************/

ALTER TABLE obingresos.tagencia
  ADD COLUMN email VARCHAR(255);

/********************************************F-SCP-JRR-OBINGRESOS-0-10/09/2017********************************************/

/********************************************I-SCP-JRR-OBINGRESOS-0-12/09/2017********************************************/
DROP TRIGGER tdeposito_tr ON obingresos.tdeposito;

CREATE TRIGGER tdeposito_tr
  AFTER UPDATE
  ON obingresos.tdeposito FOR EACH ROW
  EXECUTE PROCEDURE obingresos.f_tr_deposito();

/********************************************F-SCP-JRR-OBINGRESOS-0-12/09/2017********************************************/

/********************************************I-SCP-GSS-OBINGRESOS-0-22/10/2017********************************************/

ALTER TABLE obingresos.tboleto
  ADD COLUMN tipo_comision VARCHAR(13);

ALTER TABLE obingresos.tboleto
  ALTER COLUMN tipo_comision SET DEFAULT 'ninguno';

/********************************************F-SCP-GSS-OBINGRESOS-0-22/10/2017********************************************/

/********************************************I-SCP-JRR-OBINGRESOS-0-30/10/2017********************************************/

CREATE UNIQUE INDEX tdeposito_nro_deposito_agencia ON obingresos.tdeposito
  USING btree (nro_deposito COLLATE pg_catalog."default", id_agencia)
  WHERE tipo='agencia';

CREATE INDEX tdetalle_boletos_web_idx ON obingresos.tdetalle_boletos_web
  USING btree (fecha);

CREATE INDEX tdetalle_boletos_web_idx1 ON obingresos.tdetalle_boletos_web
  USING btree (numero_autorizacion COLLATE pg_catalog."default")
  WHERE origen = 'portal';

/********************************************F-SCP-JRR-OBINGRESOS-0-30/10/2017********************************************/


/********************************************I-SCP-JRR-OBINGRESOS-0-11/12/2017********************************************/
ALTER TABLE obingresos.tagencia
  ADD COLUMN controlar_periodos_pago VARCHAR(2) NOT NULL DEFAULT 'si';

ALTER TABLE obingresos.tagencia
  ADD COLUMN validar_boleta VARCHAR(2) NOT NULL DEFAULT 'si';

ALTER TABLE obingresos.tagencia
  ADD COLUMN bloquear_emision VARCHAR(2) NOT NULL DEFAULT 'no';


/********************************************F-SCP-JRR-OBINGRESOS-0-11/12/2017********************************************/


/********************************************I-SCP-RZM-OBINGRESOS-0-28/09/2018********************************************/
CREATE TABLE obingresos.tarchivo_acm (
  id_archivo_acm SERIAL,
  fecha_ini DATE NOT NULL,
  fecha_fin DATE NOT NULL,
  nombre VARCHAR(500) NOT NULL,
  estado VARCHAR(50),
  ultimo_numero INTEGER,
  CONSTRAINT tarchivo_acm_pkey PRIMARY KEY(id_archivo_acm)
) INHERITS (pxp.tbase)

WITH (oids = false);
/********************************************F-SCP-RZM-OBINGRESOS-0-28/09/2018********************************************/
/********************************************I-SCP-RZM-OBINGRESOS-0-28/09/2018********************************************/
CREATE TABLE obingresos.tarchivo_acm_det (
  id_archivo_acm_det SERIAL,
  officce_id VARCHAR(50) NOT NULL,
  porcentaje INTEGER NOT NULL,
  id_agencia INTEGER,
  importe_total_mb NUMERIC(18,2),
  importe_total_mt NUMERIC(18,2),
  id_archivo_acm INTEGER NOT NULL,
  neto_total_mb NUMERIC(18,2),
  neto_total_mt NUMERIC(18,2),
  cant_bol_mb INTEGER,
  cant_bol_mt INTEGER,
  CONSTRAINT tarchivo_acm_det_pkey PRIMARY KEY(id_archivo_acm_det),
  CONSTRAINT tarchivo_acm_det_fk FOREIGN KEY (id_archivo_acm)
    REFERENCES obingresos.tarchivo_acm(id_archivo_acm)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
    NOT DEFERRABLE
) INHERITS (pxp.tbase)

WITH (oids = false);
/********************************************I-SCP-RZM-OBINGRESOS-0-28/09/2018********************************************/
/********************************************I-SCP-IRV-OBINGRESOS-0-28/09/2018********************************************/
CREATE TABLE obingresos.tacm (
  id_acm SERIAL,
  id_archivo_acm_det INTEGER NOT NULL,
  numero VARCHAR(20) NOT NULL,
  fecha DATE NOT NULL,
  ruta VARCHAR(10) NOT NULL,
  id_moneda INTEGER,
  importe NUMERIC(18,2),
  id_movimiento_entidad INTEGER,
  total_bsp NUMERIC(18,2),
  CONSTRAINT tacm_pkey PRIMARY KEY(id_acm),
  CONSTRAINT tacm_fk FOREIGN KEY (id_moneda)
    REFERENCES param.tmoneda(id_moneda)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
    NOT DEFERRABLE,
  CONSTRAINT tacm_fk1 FOREIGN KEY (id_archivo_acm_det)
    REFERENCES obingresos.tarchivo_acm_det(id_archivo_acm_det)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
    NOT DEFERRABLE,
  CONSTRAINT tacm_fk2 FOREIGN KEY (id_movimiento_entidad)
    REFERENCES obingresos.tmovimiento_entidad(id_movimiento_entidad)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
    NOT DEFERRABLE
) INHERITS (pxp.tbase)

WITH (oids = false);

ALTER TABLE obingresos.tacm
  ALTER COLUMN numero SET STATISTICS 0;
/********************************************F-SCP-IRV-OBINGRESOS-0-28/09/2018********************************************/
/********************************************I-SCP-IRV-OBINGRESOS-0-28/09/2018********************************************/
CREATE TABLE obingresos.tacm_det (
  id_acm_det SERIAL,
  id_detalle_boletos_web INTEGER NOT NULL,
  neto NUMERIC(18,2) NOT NULL,
  over_comision NUMERIC(18,2) NOT NULL,
  id_acm INTEGER NOT NULL,
  com_bsp NUMERIC(18,2),
  porcentaje_over INTEGER,
  moneda VARCHAR(10),
  td VARCHAR(10),
  CONSTRAINT tacm_det_pkey PRIMARY KEY(id_acm_det),
  CONSTRAINT tacm_det_fk FOREIGN KEY (id_acm)
    REFERENCES obingresos.tacm(id_acm)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
    NOT DEFERRABLE,
  CONSTRAINT tacm_det_fk1 FOREIGN KEY (id_detalle_boletos_web)
    REFERENCES obingresos.tdetalle_boletos_web(id_detalle_boletos_web)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
    NOT DEFERRABLE
) INHERITS (pxp.tbase)

WITH (oids = false);
/********************************************F-SCP-IRV-OBINGRESOS-0-28/09/2018********************************************/
/********************************************I-SCP-RZM-OBINGRESOS-1-24/10/2018********************************************/
ALTER TABLE obingresos.tarchivo_acm_det
ADD COLUMN abonado VARCHAR(2);
/********************************************F-SCP-RZM-OBINGRESOS-1-24/10/2018********************************************/

/********************************************I-SCP-FEA-OBINGRESOS-1-07/11/2018********************************************/

CREATE TABLE obingresos.tboleto_amadeus (
  id_boleto_amadeus SERIAL,
  nro_boleto VARCHAR(50),
  pasajero VARCHAR(100),
  fecha_emision DATE,
  total NUMERIC(18,2),
  liquido NUMERIC(18,2),
  id_moneda_boleto INTEGER,
  neto NUMERIC(18,2),
  estado VARCHAR(20),
  id_punto_venta INTEGER,
  localizador VARCHAR(13),
  voided VARCHAR(10),
  forma_pago VARCHAR(3),
  officeid VARCHAR(10),
  codigo_iata VARCHAR(10),
  comision NUMERIC(18,2),
  moneda VARCHAR(5),
  tc NUMERIC(18,2),
  xt NUMERIC(18,2),
  monto_pagado_moneda_boleto NUMERIC(18,2),
  agente_venta VARCHAR(7),
  id_agencia INTEGER,
  tipo_comision VARCHAR(13) DEFAULT 'ninguno'::character varying,
  id_usuario_cajero INTEGER,
  ruta_completa VARCHAR(255),
  mensaje_error TEXT,
  estado_informix VARCHAR(20) DEFAULT 'migrado'::character varying,
  CONSTRAINT tboleto_amadeus_nro_boleto_key UNIQUE(nro_boleto),
  CONSTRAINT tboleto_amadeus_pkey PRIMARY KEY(id_boleto_amadeus),
  CONSTRAINT fk_tboleto_amadeus__id_agencia FOREIGN KEY (id_agencia)
    REFERENCES obingresos.tagencia(id_agencia)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
    NOT DEFERRABLE,
  CONSTRAINT fk_tboleto_amadeus__id_moneda FOREIGN KEY (id_moneda_boleto)
    REFERENCES param.tmoneda(id_moneda)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
    NOT DEFERRABLE,
  CONSTRAINT fk_tboleto_amadeus__id_punto_venta FOREIGN KEY (id_punto_venta)
    REFERENCES vef.tpunto_venta(id_punto_venta)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
    NOT DEFERRABLE,
  CONSTRAINT fk_tboleto_amadeus__id_usuario FOREIGN KEY (id_usuario_cajero)
    REFERENCES segu.tusuario(id_usuario)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
    NOT DEFERRABLE
) INHERITS (pxp.tbase)

WITH (oids = false);

CREATE TABLE obingresos.tboleto_amadeus_forma_pago (
  id_boleto_amadeus_forma_pago SERIAL,
  importe NUMERIC(18,2) NOT NULL,
  id_forma_pago INTEGER NOT NULL,
  id_boleto_amadeus INTEGER NOT NULL,
  tipo VARCHAR(20),
  tarjeta VARCHAR(6),
  numero_tarjeta VARCHAR(20),
  ctacte VARCHAR(20),
  codigo_tarjeta VARCHAR(20),
  forma_pago_amadeus VARCHAR(3),
  id_auxiliar INTEGER,
  id_usuario_fp_corregido INTEGER,
  registro_mod INTEGER DEFAULT 0,
  mco VARCHAR(15),
  CONSTRAINT tboleto_amadeus_forma_pago_pkey PRIMARY KEY(id_boleto_amadeus_forma_pago),
  CONSTRAINT fk_tboleto_amadeus_forma_pago__id_auxiliar FOREIGN KEY (id_auxiliar)
    REFERENCES conta.tauxiliar(id_auxiliar)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
    NOT DEFERRABLE,
  CONSTRAINT fk_tboleto_amadeus_forma_pago__id_boleto_amadeus FOREIGN KEY (id_boleto_amadeus)
    REFERENCES obingresos.tboleto_amadeus(id_boleto_amadeus)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
    NOT DEFERRABLE,
  CONSTRAINT fk_tboleto_amadeus_forma_pago__id_forma_pago FOREIGN KEY (id_forma_pago)
    REFERENCES obingresos.tforma_pago(id_forma_pago)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
    NOT DEFERRABLE,
  CONSTRAINT fk_tboleto_amadeus_forma_pago__id_usuario FOREIGN KEY (id_usuario_fp_corregido)
    REFERENCES segu.tusuario(id_usuario)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
    NOT DEFERRABLE
) INHERITS (pxp.tbase)

WITH (oids = false);

CREATE INDEX tboleto_amadeus_forma_pago__id_boleto_idx ON obingresos.tboleto_amadeus_forma_pago
  USING btree (id_boleto_amadeus);


CREATE TABLE obingresos.tconsulta_viajero_frecuente (
  id_consulta_viajero_frecuente SERIAL,
  ffid VARCHAR(50),
  voucher_code VARCHAR(60),
  message VARCHAR(200),
  status VARCHAR(20),
  nro_boleto VARCHAR(50),
  CONSTRAINT tconsulta_vieajero_frecuente_pkey PRIMARY KEY(id_consulta_viajero_frecuente)
) INHERITS (pxp.tbase)

WITH (oids = false);

ALTER TABLE obingresos.tconsulta_viajero_frecuente
  ALTER COLUMN id_consulta_viajero_frecuente SET STATISTICS 0;

ALTER TABLE obingresos.tconsulta_viajero_frecuente
  ALTER COLUMN ffid SET STATISTICS 0;

ALTER TABLE obingresos.tconsulta_viajero_frecuente
  ALTER COLUMN voucher_code SET STATISTICS 0;

ALTER TABLE obingresos.tconsulta_viajero_frecuente
  ALTER COLUMN message SET STATISTICS 0;

ALTER TABLE obingresos.tconsulta_viajero_frecuente
  ALTER COLUMN status SET STATISTICS 0;

CREATE TABLE obingresos.tdetalle_credito (
  id_agencia SERIAL,
  autorizacion__nro_deposito TEXT,
  fecha DATE,
  monto_total NUMERIC(18,2),
  CONSTRAINT tdetalle_credito_pkey PRIMARY KEY(id_agencia)
)
WITH (oids = false);

CREATE TABLE obingresos.tdetalle_debito (
  id_agencia SERIAL,
  billeta_pnr TEXT,
  fecha DATE,
  comision NUMERIC(18,2),
  importe NUMERIC(18,2),
  neto NUMERIC(18,2),
  saldo NUMERIC,
  CONSTRAINT tdetalle_debito_pkey PRIMARY KEY(id_agencia)
)
WITH (oids = false);

CREATE TABLE obingresos.tforma_pago_ant (
  id_forma_pago_ant SERIAL,
  id_boleto_amadeus INTEGER,
  id_forma_pago INTEGER,
  importe NUMERIC(20,0),
  CONSTRAINT tforma_pago_ant_pkey PRIMARY KEY(id_forma_pago_ant)
) INHERITS (pxp.tbase)

WITH (oids = false);

ALTER TABLE obingresos.tforma_pago_ant
  ALTER COLUMN id_forma_pago_ant SET STATISTICS 0;

ALTER TABLE obingresos.tforma_pago_ant
  ALTER COLUMN id_boleto_amadeus SET STATISTICS 0;

ALTER TABLE obingresos.tforma_pago_ant
  ALTER COLUMN id_forma_pago SET STATISTICS 0;

ALTER TABLE obingresos.tforma_pago_ant
  ALTER COLUMN importe SET STATISTICS 0;


CREATE TABLE obingresos.tlog_vajero_frecuente (
  id_log_viajero_frecuente SERIAL,
  tickert_number VARCHAR(50),
  pnr VARCHAR(50),
  importe NUMERIC(20,0),
  moneda VARCHAR(5),
  id_boleto_amadeus INTEGER,
  CONSTRAINT tlog_vajero_frecuente_pkey PRIMARY KEY(id_log_viajero_frecuente)
) INHERITS (pxp.tbase)

WITH (oids = false);

ALTER TABLE obingresos.tlog_vajero_frecuente
  ALTER COLUMN id_log_viajero_frecuente SET STATISTICS 0;

ALTER TABLE obingresos.tlog_vajero_frecuente
  ALTER COLUMN tickert_number SET STATISTICS 0;

ALTER TABLE obingresos.tlog_vajero_frecuente
  ALTER COLUMN pnr SET STATISTICS 0;

ALTER TABLE obingresos.tlog_vajero_frecuente
  ALTER COLUMN importe SET STATISTICS 0;

ALTER TABLE obingresos.tlog_vajero_frecuente
  ALTER COLUMN moneda SET STATISTICS 0;


CREATE TABLE obingresos.tpnr_forma_pago (
  id_pnr_forma_pago SERIAL,
  pnr VARCHAR,
  id_forma_pago INTEGER,
  tarjeta VARCHAR(6),
  numero_tarjeta VARCHAR(20),
  codigo_tarjeta VARCHAR(20),
  ctacte VARCHAR(20),
  importe NUMERIC(18,2),
  forma_pago_amadeus VARCHAR(3),
  CONSTRAINT tpnr_forma_pago_pkey PRIMARY KEY(id_pnr_forma_pago),
  CONSTRAINT fk_tpnr_forma_pago__id_forma_pago FOREIGN KEY (id_forma_pago)
    REFERENCES obingresos.tforma_pago(id_forma_pago)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
    NOT DEFERRABLE
) INHERITS (pxp.tbase)

WITH (oids = false);


CREATE TABLE obingresos.tviajero_frecuente (
  id_viajero_frecuente SERIAL,
  id_boleto_amadeus INTEGER,
  ffid VARCHAR(50),
  pnr VARCHAR(30),
  ticket_number VARCHAR(50),
  voucher_code VARCHAR(50),
  id_pasajero_frecuente INTEGER,
  nombre_completo VARCHAR(100),
  mensaje VARCHAR(200),
  status VARCHAR(50),
  CONSTRAINT tviajero_frecuente_pkey PRIMARY KEY(id_viajero_frecuente)
) INHERITS (pxp.tbase)

WITH (oids = false);

ALTER TABLE obingresos.tviajero_frecuente
  ALTER COLUMN id_viajero_frecuente SET STATISTICS 0;

ALTER TABLE obingresos.tviajero_frecuente
  ALTER COLUMN id_boleto_amadeus SET STATISTICS 0;

ALTER TABLE obingresos.tviajero_frecuente
  ALTER COLUMN ffid SET STATISTICS 0;

ALTER TABLE obingresos.tviajero_frecuente
  ALTER COLUMN pnr SET STATISTICS 0;

ALTER TABLE obingresos.tviajero_frecuente
  ALTER COLUMN ticket_number SET STATISTICS 0;


CREATE TABLE obingresos.tventa_web_modificaciones (
  nro_boleto VARCHAR(20) NOT NULL,
  tipo VARCHAR(15),
  nro_boleto_reemision VARCHAR(20),
  used VARCHAR(2),
  motivo TEXT NOT NULL,
  id_venta_web_modificaciones SERIAL,
  procesado VARCHAR(2) DEFAULT 'no'::character varying NOT NULL,
  pnr_antiguo VARCHAR(20),
  fecha_reserva_antigua DATE,
  banco VARCHAR(5),
  CONSTRAINT tventa_web_modificaciones_nro_boleto_key UNIQUE(nro_boleto),
  CONSTRAINT tventa_web_modificaciones_pkey PRIMARY KEY(id_venta_web_modificaciones),
  CONSTRAINT tventa_web_modificaciones_chk CHECK (((tipo)::text = 'tsu_anulado'::text) OR ((tipo)::text = 'anulado'::text) OR ((tipo)::text = 'reemision'::text) OR ((tipo)::text = 'emision_manual'::text))
) INHERITS (pxp.tbase)

WITH (oids = false);

CREATE UNIQUE INDEX tventa_web_modificaciones_idx ON obingresos.tventa_web_modificaciones
  USING btree (nro_boleto_reemision COLLATE pg_catalog."default")
  WHERE ((nro_boleto_reemision IS NOT NULL) AND ((nro_boleto_reemision)::text <> ''::text));


CREATE TABLE obingresos.tvisa (
  id_visa SERIAL,
  nro_boleto VARCHAR(50),
  autoriazaion VARCHAR(20),
  CONSTRAINT tvisa_pkey PRIMARY KEY(id_visa)
)
WITH (oids = false);

ALTER TABLE obingresos.tvisa
  ALTER COLUMN id_visa SET STATISTICS 0;

ALTER TABLE obingresos.tvisa
  ALTER COLUMN nro_boleto SET STATISTICS 0;

ALTER TABLE obingresos.tvisa
  ALTER COLUMN autoriazaion SET STATISTICS 0;


-------------------------------------------

ALTER TABLE obingresos.tagencia
  ADD COLUMN movimiento_activo VARCHAR(2) DEFAULT 'si'::character varying;

ALTER TABLE obingresos.tboleto_forma_pago
  ADD COLUMN forma_pago_amadeus VARCHAR(3),
  ADD COLUMN  fp_amadeus_corregido VARCHAR(3),
  ADD COLUMN  id_usuario_fp_corregido INTEGER,
  ADD COLUMN  id_auxiliar INTEGER;


ALTER TABLE obingresos.tdeposito
  ADD COLUMN id_apertura_cierre_caja INTEGER,
  ADD COLUMN nro_deposito_aux VARCHAR(70),
  ADD COLUMN nro_deposito_boa VARCHAR(70);
/********************************************F-SCP-FEA-OBINGRESOS-1-07/11/2018********************************************/
/********************************************I-SCP-RZM-OBINGRESOS-1-02/01/2019********************************************/
CREATE TABLE obingresos.tviajero_interno (
  id_viajero_interno SERIAL,
  codigo_voucher VARCHAR(60),
  mensaje VARCHAR(200),
  estado VARCHAR(20),
  CONSTRAINT tviajero_interno_pkey PRIMARY KEY(id_viajero_interno)
) INHERITS (pxp.tbase)

WITH (oids = false);


CREATE TABLE obingresos.tviajero_interno_det (
  id_viajero_interno_det SERIAL,
  nombre VARCHAR(100),
  pnr VARCHAR(50),
  num_boleto VARCHAR(50),
  id_viajero_interno INTEGER,
  solicitud VARCHAR(5),
  num_documento VARCHAR(15),
  estado_voucher VARCHAR(50),
  tarifa VARCHAR(20),
  CONSTRAINT tviajero_interno_det_pkey PRIMARY KEY(id_viajero_interno_det),
  CONSTRAINT tviajero_interno_det_fk FOREIGN KEY (id_viajero_interno)
    REFERENCES obingresos.tviajero_interno(id_viajero_interno)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
    NOT DEFERRABLE
) INHERITS (pxp.tbase)

WITH (oids = false);

/********************************************F-SCP-RZM-OBINGRESOS-1-02/01/2019********************************************/
/********************************************I-SCP-IRVA-OBINGRESOS-1-02/01/2019********************************************/
ALTER TABLE obingresos.tagencia
  ALTER COLUMN boaagt SET NOT NULL;
/********************************************F-SCP-RZM-OBINGRESOS-1-02/01/2019********************************************/

/********************************************I-SCP-IRVA-OBINGRESOS-1-05/08/2019********************************************/
ALTER TABLE obingresos.tboleto_amadeus_forma_pago
  ADD COLUMN nro_cupon VARCHAR(50);

ALTER TABLE obingresos.tboleto_amadeus_forma_pago
  ADD COLUMN nro_cuota VARCHAR(50);
/********************************************F-SCP-RZM-OBINGRESOS-1-05/08/2019********************************************/

/********************************************I-SCP-IRVA-OBINGRESOS-0-18/09/2019********************************************/
ALTER TABLE obingresos.tmovimiento_entidad
  ADD COLUMN fk_id_movimiento_entidad INTEGER;
/********************************************F-SCP-IRVA-OBINGRESOS-0-18/09/2019********************************************/

/********************************************I-SCP-IRVA-OBINGRESOS-0-26/09/2019********************************************/
ALTER TABLE obingresos.tmovimiento_entidad
  ADD COLUMN id_void INTEGER;

ALTER TABLE obingresos.tmovimiento_entidad
  ADD COLUMN tipo_void VARCHAR(50);
/********************************************F-SCP-IRVA-OBINGRESOS-0-26/09/2019********************************************/

/********************************************I-SCP-IRVA-OBINGRESOS-0-06/11/2019********************************************/
CREATE TABLE obingresos.tforma_pago_pw (
  id_forma_pago_pw SERIAL,
  name VARCHAR(100),
  country_code VARCHAR(5),
  erp_code VARCHAR(50),
  fop_code VARCHAR(15),
  manage_account NUMERIC(18,2),
  forma_pago_id INTEGER,
  CONSTRAINT tforma_pago_pw_pkey PRIMARY KEY(id_forma_pago_pw)
) INHERITS (pxp.tbase)
WITH (oids = false);

ALTER TABLE obingresos.tforma_pago_pw
  ALTER COLUMN id_forma_pago_pw SET STATISTICS 0;

ALTER TABLE obingresos.tforma_pago_pw
  ALTER COLUMN name SET STATISTICS 0;

ALTER TABLE obingresos.tforma_pago_pw
  ALTER COLUMN country_code SET STATISTICS 0;

ALTER TABLE obingresos.tforma_pago_pw
  ALTER COLUMN erp_code SET STATISTICS 0;

ALTER TABLE obingresos.tforma_pago_pw
  ALTER COLUMN fop_code SET STATISTICS 0;

ALTER TABLE obingresos.tforma_pago_pw
  ALTER COLUMN manage_account SET STATISTICS 0;

ALTER TABLE obingresos.tforma_pago_pw
  OWNER TO postgres;

  CREATE TABLE obingresos.tboletos_observados (
    id_boletos_observados SERIAL,
    pnr VARCHAR(10) NOT NULL,
    nro_autorizacion VARCHAR(256) NOT NULL,
    moneda VARCHAR(4),
    importe_total NUMERIC(18,2) NOT NULL,
    fecha_emision DATE,
    estado_p VARCHAR(10),
    forma_pago VARCHAR(10),
    medio_pago VARCHAR(10),
    instancia_pago VARCHAR(10),
    office_id_emisor VARCHAR(10) NOT NULL,
    pnr_prov VARCHAR(10) NOT NULL,
    nro_autorizacion_prov VARCHAR(256) NOT NULL,
    office_id_emisor_prov VARCHAR(10) NOT NULL,
    importe_prov NUMERIC(18,2),
    moneda_prov VARCHAR(4),
    estado_prov VARCHAR(10),
    fecha_autorizacion_prov DATE,
    estado_validacion CHAR(1) NOT NULL,
    tipo_error VARCHAR(100) NOT NULL,
    tipo_validacion VARCHAR(50),
    prov_informacion VARCHAR(256),
    CONSTRAINT tpnr_na_observado_pkey PRIMARY KEY(id_boletos_observados)
  ) INHERITS (pxp.tbase)
  WITH (oids = false);

  ALTER TABLE obingresos.tboletos_observados
    ALTER COLUMN id_boletos_observados SET STATISTICS 0;

  ALTER TABLE obingresos.tboletos_observados
    ALTER COLUMN pnr SET STATISTICS 0;

  ALTER TABLE obingresos.tboletos_observados
    ALTER COLUMN nro_autorizacion SET STATISTICS 0;

  ALTER TABLE obingresos.tboletos_observados
    ALTER COLUMN moneda SET STATISTICS 0;

  ALTER TABLE obingresos.tboletos_observados
    ALTER COLUMN importe_total SET STATISTICS 0;

  ALTER TABLE obingresos.tboletos_observados
    ALTER COLUMN fecha_emision SET STATISTICS 0;

  ALTER TABLE obingresos.tboletos_observados
    ALTER COLUMN estado_p SET STATISTICS 0;

  ALTER TABLE obingresos.tboletos_observados
    ALTER COLUMN forma_pago SET STATISTICS 0;

  ALTER TABLE obingresos.tboletos_observados
    ALTER COLUMN medio_pago SET STATISTICS 0;

  ALTER TABLE obingresos.tboletos_observados
    ALTER COLUMN instancia_pago SET STATISTICS 0;

  ALTER TABLE obingresos.tboletos_observados
    ALTER COLUMN office_id_emisor SET STATISTICS 0;

  ALTER TABLE obingresos.tboletos_observados
    ALTER COLUMN pnr_prov SET STATISTICS 0;

  ALTER TABLE obingresos.tboletos_observados
    ALTER COLUMN nro_autorizacion_prov SET STATISTICS 0;

  ALTER TABLE obingresos.tboletos_observados
    ALTER COLUMN office_id_emisor_prov SET STATISTICS 0;

  ALTER TABLE obingresos.tboletos_observados
    ALTER COLUMN importe_prov SET STATISTICS 0;

  ALTER TABLE obingresos.tboletos_observados
    ALTER COLUMN moneda_prov SET STATISTICS 0;

  ALTER TABLE obingresos.tboletos_observados
    ALTER COLUMN estado_prov SET STATISTICS 0;

  ALTER TABLE obingresos.tboletos_observados
    ALTER COLUMN fecha_autorizacion_prov SET STATISTICS 0;

  ALTER TABLE obingresos.tboletos_observados
    ALTER COLUMN estado_validacion SET STATISTICS 0;

  ALTER TABLE obingresos.tboletos_observados
    ALTER COLUMN tipo_error SET STATISTICS 0;

  ALTER TABLE obingresos.tboletos_observados
    ALTER COLUMN tipo_validacion SET STATISTICS 0;

  ALTER TABLE obingresos.tboletos_observados
    OWNER TO postgres;


    CREATE TABLE obingresos.tinstancia_pago (
    id_instancia_pago SERIAL,
    id_medio_pago INTEGER,
    nombre VARCHAR(40) NOT NULL,
    codigo VARCHAR(10) NOT NULL,
    codigo_forma_pago VARCHAR(10),
    codigo_medio_pago VARCHAR(20),
    instancia_pago_id INTEGER,
    fp_code VARCHAR(50),
    ins_code VARCHAR(20),
    CONSTRAINT tintancia_pago_pkey PRIMARY KEY(id_instancia_pago)
  ) INHERITS (pxp.tbase)
  WITH (oids = false);

  ALTER TABLE obingresos.tinstancia_pago
    ALTER COLUMN id_instancia_pago SET STATISTICS 0;

  ALTER TABLE obingresos.tinstancia_pago
    ALTER COLUMN id_medio_pago SET STATISTICS 0;

  ALTER TABLE obingresos.tinstancia_pago
    ALTER COLUMN nombre SET STATISTICS 0;

  ALTER TABLE obingresos.tinstancia_pago
    ALTER COLUMN codigo SET STATISTICS 0;

  ALTER TABLE obingresos.tinstancia_pago
    ALTER COLUMN codigo_medio_pago SET STATISTICS 0;

  ALTER TABLE obingresos.tinstancia_pago
    OWNER TO postgres;


    CREATE TABLE obingresos.tmedio_pago_pw (
    id_medio_pago_pw SERIAL,
    medio_pago_id INTEGER,
    forma_pago_id INTEGER,
    name VARCHAR(100),
    mop_code VARCHAR(20),
    code VARCHAR(15),
    CONSTRAINT tmedio_pago_pw_pkey PRIMARY KEY(id_medio_pago_pw)
  ) INHERITS (pxp.tbase)
  WITH (oids = false);

  ALTER TABLE obingresos.tmedio_pago_pw
    ALTER COLUMN id_medio_pago_pw SET STATISTICS 0;

  ALTER TABLE obingresos.tmedio_pago_pw
    ALTER COLUMN medio_pago_id SET STATISTICS 0;

  ALTER TABLE obingresos.tmedio_pago_pw
    ALTER COLUMN forma_pago_id SET STATISTICS 0;

  ALTER TABLE obingresos.tmedio_pago_pw
    OWNER TO postgres;
/********************************************F-SCP-IRVA-OBINGRESOS-0-06/11/2019********************************************/

/********************************************I-SCP-IRVA-OBINGRESOS-0-25/11/2019********************************************/
ALTER TABLE obingresos.tmovimiento_entidad
  ADD COLUMN observaciones TEXT;
/********************************************F-SCP-IRVA-OBINGRESOS-0-25/11/2019********************************************/

/********************************************I-SCP-IRVA-OBINGRESOS-0-02/12/2019********************************************/
ALTER TABLE obingresos.tconsulta_viajero_frecuente
ADD COLUMN pnr VARCHAR(6);

ALTER TABLE obingresos.tconsulta_viajero_frecuente
ADD COLUMN estado VARCHAR(100);

ALTER TABLE obingresos.tconsulta_viajero_frecuente
ADD COLUMN status_canjeado VARCHAR(20);

ALTER TABLE obingresos.tconsulta_viajero_frecuente
ADD COLUMN message_canjeado VARCHAR(200);
/********************************************F-SCP-IRVA-OBINGRESOS-0-02/12/2019********************************************/

/********************************************I-SCP-IRVA-OBINGRESOS-0-24/12/2019********************************************/
ALTER TABLE obingresos.tacm_det
DROP CONSTRAINT tacm_det_fk1 RESTRICT;

ALTER TABLE obingresos.tacm_det
DROP CONSTRAINT tacm_det_idx RESTRICT;
/********************************************F-SCP-IRVA-OBINGRESOS-0-24/12/2019********************************************/

/********************************************I-SCP-IRVA-OBINGRESOS-0-03/02/2020********************************************/
CREATE TABLE obingresos.tboletos_banca (
  id_boleto_banca SERIAL,
  agencia_id INTEGER NOT NULL,
  emision_id INTEGER NOT NULL,
  transaccion_id INTEGER NOT NULL,
  pnr VARCHAR(10) NOT NULL,
  tkt VARCHAR(15),
  neto NUMERIC(20,2) NOT NULL,
  tasas NUMERIC(20,2) NOT NULL,
  monto_total NUMERIC(20,2) NOT NULL,
  comision NUMERIC(20,2) NOT NULL,
  moneda VARCHAR(3) NOT NULL,
  fecha_emision DATE,
  fecha_transaccion TIMESTAMP WITHOUT TIME ZONE,
  fecha_pago_banco DATE NOT NULL,
  forma_pago VARCHAR(50) NOT NULL,
  entidad_pago VARCHAR(20) NOT NULL,
  estado VARCHAR(20) NOT NULL,
  pasajero TEXT,
  CONSTRAINT tboletos_banca_pkey PRIMARY KEY(id_boleto_banca)
) INHERITS (pxp.tbase)
WITH (oids = false);

CREATE INDEX tboletos_banca_idx ON obingresos.tboletos_banca
  USING btree (fecha_emision, fecha_transaccion, fecha_pago_banco, moneda COLLATE pg_catalog."default", forma_pago COLLATE pg_catalog."default", agencia_id, emision_id);

ALTER TABLE obingresos.tboletos_banca
  OWNER TO postgres;

/********************************************F-SCP-IRVA-OBINGRESOS-0-03/02/2020********************************************/

/********************************************I-SCP-BVP-OBINGRESOS-0-10/07/2020********************************************/
CREATE TABLE obingresos.tmco_s (
  id_mco SERIAL,
  fecha_emision DATE NOT NULL,
  id_moneda INTEGER NOT NULL,
  motivo TEXT NOT NULL,
  valor_total NUMERIC(18,2),
  id_gestion INTEGER NOT NULL,
  id_punto_venta INTEGER,
  id_sucursal_usuario INTEGER,
  estado INTEGER NOT NULL,
  id_concepto_ingas INTEGER NOT NULL,
  id_boleto INTEGER,
  tipo_cambio NUMERIC(18,6),
  nro_mco VARCHAR,
  pax VARCHAR,
  id_funcionario_emisor INTEGER,
  CONSTRAINT tmco_s_pkey PRIMARY KEY(id_mco),
  CONSTRAINT tmco_s_fk1 FOREIGN KEY (id_moneda)
    REFERENCES param.tmoneda(id_moneda)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
    NOT DEFERRABLE,
  CONSTRAINT tmco_s_fk2 FOREIGN KEY (id_gestion)
    REFERENCES param.tgestion(id_gestion)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
    NOT DEFERRABLE
) INHERITS (pxp.tbase)
WITH (oids = false);

COMMENT ON COLUMN obingresos.tmco_s.motivo
IS 'Razon de emision de MCO.';

COMMENT ON COLUMN obingresos.tmco_s.valor_total
IS 'Importe del MCO.';

COMMENT ON COLUMN obingresos.tmco_s.id_concepto_ingas
IS 'Tipo de concepto global.';

ALTER TABLE obingresos.tmco_s
  OWNER TO postgres;
/********************************************F-SCP-BVP-OBINGRESOS-0-10/07/2020********************************************/

/********************************************I-SCP-BVP-OBINGRESOS-0-16/11/2020********************************************/

ALTER TABLE obingresos.tmedio_pago_pw
  ADD COLUMN sw_autorizacion VARCHAR(200) [];

ALTER TABLE obingresos.tmedio_pago_pw
  ADD COLUMN regionales VARCHAR(200) [];

ALTER TABLE obingresos.tmedio_pago_pw
 ADD CONSTRAINT tmedio_pago_pw_fk FOREIGN KEY (forma_pago_id)
   REFERENCES obingresos.tforma_pago_pw(id_forma_pago_pw)
   ON DELETE NO ACTION
   ON UPDATE NO ACTION
   NOT DEFERRABLE;


 ALTER TABLE obingresos.tinstancia_pago
   ADD CONSTRAINT tinstancia_pago_fk FOREIGN KEY (id_medio_pago)
     REFERENCES obingresos.tmedio_pago_pw(id_medio_pago_pw)
     ON DELETE NO ACTION
     ON UPDATE NO ACTION
     NOT DEFERRABLE;

/********************************************F-SCP-BVP-OBINGRESOS-0-16/11/2020********************************************/

/********************************************I-SCP-MAY-OBINGRESOS-0-17/11/2020********************************************/
CREATE TABLE obingresos.tfactura_no_utilizada (
  id_factura_no_utilizada SERIAL,
  id_lugar_pais INTEGER,
  id_lugar_depto INTEGER,
  id_sucursal INTEGER,
  id_punto_venta INTEGER,
  id_estado_factura INTEGER,
  fecha DATE,
  tipo_cambio NUMERIC(18,2),
  id_moneda INTEGER,
  nro_autorizacion VARCHAR(200),
  nro_inicial INTEGER,
  nro_final INTEGER,
  nombre VARCHAR(300),
  nit VARCHAR(200),
  observaciones VARCHAR(500),
  id_concepto_ingas INTEGER,
  id_dosificacion INTEGER,
  CONSTRAINT tfactura_manual_pkey PRIMARY KEY(id_factura_no_utilizada)
) INHERITS (pxp.tbase)
WITH (oids = false);

COMMENT ON COLUMN obingresos.tfactura_no_utilizada.nro_inicial
IS 'numero factura inicial';

COMMENT ON COLUMN obingresos.tfactura_no_utilizada.nro_final
IS 'numero factura final';

COMMENT ON COLUMN obingresos.tfactura_no_utilizada.id_concepto_ingas
IS 'concepto de gasto';

ALTER TABLE obingresos.tfactura_no_utilizada
  OWNER TO postgres;
/********************************************F-SCP-MAY-OBINGRESOS-0-17/11/2020********************************************/
/********************************************I-SCP-IRVA-OBINGRESOS-0-27/11/2020********************************************/
ALTER TABLE obingresos.tboleto_amadeus_forma_pago
  ADD COLUMN id_medio_pago INTEGER;

COMMENT ON COLUMN obingresos.tboleto_amadeus_forma_pago.id_medio_pago
IS 'Usar los nuevos medios de pago';

ALTER TABLE obingresos.tboleto_amadeus_forma_pago
  ADD COLUMN id_moneda INTEGER;

COMMENT ON COLUMN obingresos.tboleto_amadeus_forma_pago.id_moneda
IS 'Id moneda con la que se realiza el pago';

ALTER TABLE obingresos.tboleto_amadeus_forma_pago
  ALTER COLUMN id_forma_pago DROP NOT NULL;

ALTER TABLE obingresos.tforma_pago_ant
  ADD COLUMN id_medio_pago INTEGER;

COMMENT ON COLUMN obingresos.tforma_pago_ant.id_medio_pago
IS 'Campo para registrar el medio de pago';
/********************************************F-SCP-IRVA-OBINGRESOS-0-27/11/2020********************************************/

/********************************************I-SCP-BVP-OBINGRESOS-0-19/01/2021********************************************/
ALTER TABLE obingresos.tmco_s
  ADD COLUMN nro_tkt_mco VARCHAR(50);

COMMENT ON COLUMN obingresos.tmco_s.nro_tkt_mco
IS 'Numero de mco o boleto de documentos originales.';


ALTER TABLE obingresos.tmco_s
  ADD COLUMN pais_doc_orig VARCHAR(25);

COMMENT ON COLUMN obingresos.tmco_s.pais_doc_orig
IS 'Pais de documentos originales mco o boleto.';


ALTER TABLE obingresos.tmco_s
  ADD COLUMN estacion_doc_orig VARCHAR(50);

COMMENT ON COLUMN obingresos.tmco_s.estacion_doc_orig
IS 'Estacion de documentos originales mco o boleto.';


ALTER TABLE obingresos.tmco_s
  ADD COLUMN fecha_doc_orig DATE;

COMMENT ON COLUMN obingresos.tmco_s.fecha_doc_orig
IS 'Fecha de documentos orig mco o boleto.';


ALTER TABLE obingresos.tmco_s
  ADD COLUMN t_c_doc_orig NUMERIC(18,6);

COMMENT ON COLUMN obingresos.tmco_s.t_c_doc_orig
IS 'Tipo de cambmio de documentos originales mco o boleto.';



ALTER TABLE obingresos.tmco_s
  ADD COLUMN moneda_doc_orig VARCHAR(25);

COMMENT ON COLUMN obingresos.tmco_s.moneda_doc_orig
IS 'Moneda de documentos originales mco o boleto.';

ALTER TABLE obingresos.tmco_s
  ADD COLUMN valor_total_doc_orig NUMERIC(18,2);

COMMENT ON COLUMN obingresos.tmco_s.valor_total_doc_orig
IS 'Valor total de documentos originales.';


ALTER TABLE obingresos.tmco_s
  ADD COLUMN valor_conv_doc_orig NUMERIC(18,2);

COMMENT ON COLUMN obingresos.tmco_s.valor_conv_doc_orig
IS 'Valor total convertido de documentos originales.';

/********************************************F-SCP-BVP-OBINGRESOS-0-19/01/2021********************************************/
/********************************************I-SCP-IRVA-OBINGRESOS-0-03/02/2021********************************************/
ALTER TABLE obingresos.tboleto_amadeus_forma_pago
  ADD COLUMN modificado VARCHAR(5);

COMMENT ON COLUMN obingresos.tboleto_amadeus_forma_pago.modificado
IS 'Campo que ayudara a saber las formas de pago modificadas para los boletos el valor es de (si,no)';

ALTER TABLE obingresos.tboleto_amadeus_forma_pago
  ALTER COLUMN modificado SET DEFAULT 'no';

  ALTER TABLE obingresos.tboleto_amadeus_forma_pago
  DROP CONSTRAINT fk_tboleto_amadeus_forma_pago__id_forma_pago RESTRICT;
/********************************************F-SCP-IRVA-OBINGRESOS-0-03/02/2021********************************************/

/********************************************I-SCP-FEA-OBINGRESOS-0-16/03/2021********************************************/
CREATE TABLE obingresos.testablecimiento_punto_venta (
  id_establecimiento_punto_venta SERIAL NOT NULL,
  codigo_estable VARCHAR(20) NOT NULL,
  nombre_estable VARCHAR(256) NOT NULL,
  office_id INTEGER,
  PRIMARY KEY(id_establecimiento_punto_venta)
) INHERITS (pxp.tbase)
WITH (oids = false);

COMMENT ON COLUMN obingresos.testablecimiento_punto_venta.codigo_estable
IS 'Valor Administradora(ATC,LINKSER) que identifica a un punto de venta BOA.';

COMMENT ON COLUMN obingresos.testablecimiento_punto_venta.nombre_estable
IS 'Nombre Descriptivo Adminstradora(ATC, LINKSER).';

COMMENT ON COLUMN obingresos.testablecimiento_punto_venta.office_id
IS 'Identifador oficina BOA relacionado con establecimiento Administradora.';

ALTER TABLE obingresos.testablecimiento_punto_venta
  ALTER COLUMN codigo_estable SET STATISTICS 0;
/********************************************F-SCP-FEA-OBINGRESOS-0-16/03/2021********************************************/
/********************************************I-SCP-BVP-OBINGRESOS-0-18/03/2021********************************************/
ALTER TABLE obingresos.tdeposito
  ADD COLUMN id_auxiliar INTEGER;

ALTER TABLE obingresos.tdeposito
  ADD CONSTRAINT tdeposito_fk1 FOREIGN KEY (id_auxiliar)
    REFERENCES conta.tauxiliar(id_auxiliar)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
    NOT DEFERRABLE;
/********************************************F-SCP-BVP-OBINGRESOS-0-18/03/2021********************************************/

/********************************************I-SCP-IRVA-OBINGRESOS-0-31/03/2021********************************************/
CREATE TABLE obingresos.terror_amadeus (
  nro_errores NUMERIC(18,2) DEFAULT 0
)
WITH (oids = false);

COMMENT ON TABLE obingresos.terror_amadeus
IS 'Tabla donde se almacenara los errores de Amadeus';

COMMENT ON COLUMN obingresos.terror_amadeus.nro_errores
IS 'Campo donde se ira almacenando si el servicio amadeus cae';

ALTER TABLE obingresos.terror_amadeus
  OWNER TO postgres;
/********************************************F-SCP-IRVA-OBINGRESOS-0-31/03/2021********************************************/
/********************************************I-SCP-IRVA-OBINGRESOS-0-05/04/2021********************************************/
ALTER TABLE obingresos.terror_amadeus
  ADD COLUMN id_punto_venta INTEGER;

ALTER TABLE obingresos.terror_amadeus
  ADD COLUMN datos_enviados TEXT;

ALTER TABLE obingresos.terror_amadeus
  ADD COLUMN datos_recibidos TEXT;
/********************************************F-SCP-IRVA-OBINGRESOS-0-05/04/2021********************************************/
/********************************************I-SCP-BVP-OBINGRESOS-0-13/04/2021********************************************/
ALTER TABLE obingresos.tboleto_amadeus_forma_pago
ADD COLUMN nro_documento INTEGER;
/********************************************F-SCP-BVP-OBINGRESOS-0-13/04/2021********************************************/
/********************************************I-SCP-BVP-OBINGRESOS-0-22/04/2021********************************************/
ALTER TABLE obingresos.tboleto_amadeus_forma_pago
RENAME COLUMN nro_documento TO id_venta;

COMMENT ON COLUMN obingresos.tboleto_amadeus_forma_pago.id_venta
IS 'se registra el id_venta relacionado a la tabla vef.tventa.';
/********************************************F-SCP-BVP-OBINGRESOS-0-22/04/2021********************************************/


/***********************************I-SCP-IRVA-OBINGRESOS-0-24/05/2021****************************************/

ALTER TABLE obingresos.tboleto_forma_pago_stage
  ADD COLUMN id_medio_pago INTEGER;

COMMENT ON COLUMN obingresos.tboleto_forma_pago_stage.id_medio_pago
IS 'Campo donde se almacenara el id_medio_pago porque el id_forma_pago era para lo del sistema de ingresos';

ALTER TABLE obingresos.tboleto_forma_pago_stage
  ADD COLUMN id_moneda INTEGER;

COMMENT ON COLUMN obingresos.tboleto_forma_pago_stage.id_moneda
IS 'Campo donde se almacena la moneda del medio de pago puede ser en dolar o moneda local';

ALTER TABLE obingresos.tboleto_forma_pago_stage
  ALTER COLUMN id_forma_pago DROP NOT NULL;

/***********************************F-SCP-IRVA-OBINGRESOS-0-24/05/2021****************************************/

/***********************************I-SCP-IRVA-OBINGRESOS-0-22/06/2021****************************************/
CREATE TABLE obingresos.tlog_modificaciones_medios_pago (
  id_log_mp SERIAL,
  nro_boleto VARCHAR(100),
  numero_tarjeta_antiguo VARCHAR(50),
  cod_autorizacion_tarjeta_antiguo VARCHAR(20),
  numero_tarjeta_modificado VARCHAR(50),
  cod_autorizacion_tarjeta_modificado VARCHAR(20),
  observaciones TEXT,
  numero_tarjeta_antiguo_2 VARCHAR(50),
  cod_autorizacion_tarjeta_antiguo_2 VARCHAR(20),
  numero_tarjeta_modificado_2 VARCHAR(50),
  cod_autorizacion_tarjeta_modificado_2 VARCHAR(20),
  CONSTRAINT tlog_modificaciones_medios_pago_pkey PRIMARY KEY(id_log_mp)
) INHERITS (pxp.tbase)
WITH (oids = false);

ALTER TABLE obingresos.tlog_modificaciones_medios_pago
  OWNER TO postgres;
/***********************************F-SCP-IRVA-OBINGRESOS-0-22/06/2021****************************************/

/***********************************I-SCP-FEA-OBINGRESOS-0-21/07/2021****************************************/
CREATE TABLE obingresos.tcalculo_over_comison (
  id_calculo_over_comison SERIAL,
  tipo VARCHAR(16),
  calculo_generado VARCHAR(16),
  fecha_ini_calculo DATE,
  fecha_fin_calculo DATE,
  id_calculo_over_comison_fk INTEGER,
  documento VARCHAR(8),
  lista_acm TEXT,
  CONSTRAINT tcalculo_over_comison_pkey PRIMARY KEY(id_calculo_over_comison)
) INHERITS (pxp.tbase)
WITH (oids = false);

ALTER TABLE obingresos.tcalculo_over_comison
  ALTER COLUMN id_calculo_over_comison SET STATISTICS 0;

ALTER TABLE obingresos.tcalculo_over_comison
  ALTER COLUMN tipo SET STATISTICS 0;

ALTER TABLE obingresos.tcalculo_over_comison
  ALTER COLUMN calculo_generado SET STATISTICS 0;

ALTER TABLE obingresos.tcalculo_over_comison
  ALTER COLUMN fecha_ini_calculo SET STATISTICS 0;

ALTER TABLE obingresos.tcalculo_over_comison
  ALTER COLUMN fecha_fin_calculo SET STATISTICS 0;

ALTER TABLE obingresos.tcalculo_over_comison OWNER TO postgres;
/***********************************F-SCP-FEA-OBINGRESOS-0-21/07/2021****************************************/

/***********************************I-SCP-IRVA-OBINGRESOS-0-23/08/2021****************************************/
CREATE TABLE obingresos.tlog_modificaciones_medios_pago_completo (
  importe NUMERIC(18,2),
  id_medio_pago INTEGER,
  id_moneda INTEGER,
  numero_tarjeta VARCHAR(50),
  codigo_tarjeta VARCHAR(6),
  tarjeta VARCHAR(20),
  id_auxiliar INTEGER,
  mco VARCHAR(50),
  id_venta INTEGER,
  nro_boleto VARCHAR(13),
  fecha_emision DATE,
  observaciones TEXT
) INHERITS (pxp.tbase)
WITH (oids = false);

COMMENT ON TABLE obingresos.tlog_modificaciones_medios_pago_completo
IS 'Tabla donde se almacenara log de datos originales de los medios de pago ya que se modificara completamente los medios de pago';

ALTER TABLE obingresos.tlog_modificaciones_medios_pago_completo
  OWNER TO postgres;
/***********************************F-SCP-IRVA-OBINGRESOS-0-23/08/2021****************************************/

/***********************************I-SCP-IRVA-OBINGRESOS-0-24/08/2021****************************************/
ALTER TABLE obingresos.tagencia
  ADD COLUMN tipo_institucion VARCHAR(200);

ALTER TABLE obingresos.tagencia
  ALTER COLUMN tipo_institucion SET DEFAULT 'privada';

COMMENT ON COLUMN obingresos.tagencia.tipo_institucion
IS 'Campo el que diferenciara cuando una agencia corporativa es publica o privada';


ALTER TABLE obingresos.tagencia
  ADD COLUMN iata_status VARCHAR(5);

COMMENT ON COLUMN obingresos.tagencia.iata_status
IS 'almacena el estado de la agencia Iata';


ALTER TABLE obingresos.tagencia
  ADD COLUMN osd VARCHAR(15);
/***********************************F-SCP-IRVA-OBINGRESOS-0-24/08/2021****************************************/

/***********************************I-SCP-IRVA-OBINGRESOS-0-07/09/2021****************************************/

ALTER TABLE obingresos.tlog_modificaciones_medios_pago_completo
  ADD COLUMN "pay_code" VARCHAR(8);

COMMENT ON COLUMN obingresos.tlog_modificaciones_medios_pago_completo."pay_code"
IS 'campo para almacenar cuando solo es stage';


ALTER TABLE obingresos.tlog_modificaciones_medios_pago_completo
  ADD COLUMN "pay_description" VARCHAR(100);

COMMENT ON COLUMN obingresos.tlog_modificaciones_medios_pago_completo."pay_description"
IS 'campo para cuando es solo stage';



ALTER TABLE obingresos.tlog_modificaciones_medios_pago_completo
  ADD COLUMN "pay_method_code" VARCHAR(8);

COMMENT ON COLUMN obingresos.tlog_modificaciones_medios_pago_completo."pay_method_code"
IS 'campo para cuando es solo stage';


ALTER TABLE obingresos.tlog_modificaciones_medios_pago_completo
  ADD COLUMN "pay_method_description" VARCHAR(200);

COMMENT ON COLUMN obingresos.tlog_modificaciones_medios_pago_completo."pay_method_description"
IS 'campo para cuando es solo stage';


ALTER TABLE obingresos.tlog_modificaciones_medios_pago_completo
  ADD COLUMN "pay_instance_code" VARCHAR(50);

COMMENT ON COLUMN obingresos.tlog_modificaciones_medios_pago_completo."pay_instance_code"
IS 'campo para cuando es solo stage';


ALTER TABLE obingresos.tlog_modificaciones_medios_pago_completo
  ADD COLUMN "pay_instance_description" VARCHAR(200);

COMMENT ON COLUMN obingresos.tlog_modificaciones_medios_pago_completo."pay_instance_description"
IS 'campo para cuando es solo stage';


ALTER TABLE obingresos.tlog_modificaciones_medios_pago_completo
  ADD COLUMN "pay_amount" NUMERIC(18,2);

  COMMENT ON COLUMN obingresos.tlog_modificaciones_medios_pago_completo."pay_amount"
IS 'campo para cuando es solo stage';


ALTER TABLE obingresos.tlog_modificaciones_medios_pago_completo
  ADD COLUMN "pay_currency" VARCHAR(10);

  COMMENT ON COLUMN obingresos.tlog_modificaciones_medios_pago_completo."pay_currency"
IS 'campo para cuando es solo stage';

ALTER TABLE obingresos.tlog_modificaciones_medios_pago_completo
  ADD COLUMN reference TEXT;


  COMMENT ON COLUMN obingresos.tlog_modificaciones_medios_pago_completo."reference"
IS 'campo para cuando es solo stage';

ALTER TABLE obingresos.tlog_modificaciones_medios_pago_completo
  ADD COLUMN "issue_date" DATE;

    COMMENT ON COLUMN obingresos.tlog_modificaciones_medios_pago_completo."issue_date"
IS 'campo para cuando es solo stage';


ALTER TABLE obingresos.tlog_modificaciones_medios_pago_completo
  ADD COLUMN "credit_card_number" VARCHAR(50);

COMMENT ON COLUMN obingresos.tlog_modificaciones_medios_pago_completo."credit_card_number"
IS 'campo para cuando es solo stage';


ALTER TABLE obingresos.tlog_modificaciones_medios_pago_completo
  ADD COLUMN "authorization_code" VARCHAR(6);

COMMENT ON COLUMN obingresos.tlog_modificaciones_medios_pago_completo."authorization_code"
IS 'campo para cuando es solo stage';

/***********************************F-SCP-IRVA-OBINGRESOS-0-07/09/2021****************************************/

/***********************************I-SCP-IRVA-OBINGRESOS-0-08/09/2021****************************************/
ALTER TABLE obingresos.tdetalle_boletos_web
DROP CONSTRAINT tdetalle_boletos_web_billete_key RESTRICT;

ALTER TABLE obingresos.tdetalle_boletos_web
ADD CONSTRAINT tdetalle_boletos_web_billete_key
UNIQUE (billete, estado_reg, numero_autorizacion) NOT DEFERRABLE;
/***********************************F-SCP-IRVA-OBINGRESOS-0-08/09/2021****************************************/

/***********************************I-SCP-IRVA-OBINGRESOS-0-28/09/2021****************************************/
ALTER TABLE obingresos.tagencia
  ADD COLUMN business_name VARCHAR(300);

COMMENT ON COLUMN obingresos.tagencia.business_name
IS 'Nuevo campo donde alamcenara la Razón Social desde el portal';


ALTER TABLE obingresos.tagencia
  ADD COLUMN representante_legal VARCHAR(300);

COMMENT ON COLUMN obingresos.tagencia.representante_legal
IS 'Nuevo Campo donde Almacenara el representante legal desde el portal';


ALTER TABLE obingresos.tagencia
  ADD COLUMN pasaporte_ci VARCHAR(50);

COMMENT ON COLUMN obingresos.tagencia.pasaporte_ci
IS 'Nuevo campo donde se almacenara el carnet de identidad o algun documento de identificacion';


ALTER TABLE obingresos.tagencia
  ADD COLUMN expedido VARCHAR(50);

COMMENT ON COLUMN obingresos.tagencia.expedido
IS 'Nuevo Campo donde se almacenara donde fue expedido el documento de identificacion dato nos llega desde el portal';
/***********************************F-SCP-IRVA-OBINGRESOS-0-28/09/2021****************************************/

/***********************************I-SCP-BVP-OBINGRESOS-0-17/11/2021****************************************/
CREATE TABLE obingresos.treserva_pnr (
  id_reserva_pnr SERIAL,
  pnr_reserva VARCHAR(10),
  estado VARCHAR(20) DEFAULT 'reservado'::character varying,
  observacion TEXT,
  authorization_code VARCHAR,
  CONSTRAINT treserva_pnr_pkey PRIMARY KEY(id_reserva_pnr)
) INHERITS (pxp.tbase)
WITH (oids = false);

COMMENT ON TABLE obingresos.treserva_pnr
IS 'almacena pnr de reserva, tabla usada para enviar codigo de aprobacion para la emision de boletos, segun pnr.';

COMMENT ON COLUMN obingresos.treserva_pnr.pnr_reserva
IS 'pnr de reserva';

COMMENT ON COLUMN obingresos.treserva_pnr.estado
IS 'estado del pnr, si fue emitito exitosamente.
estados (reservado, error_emision, emitido)';

COMMENT ON COLUMN obingresos.treserva_pnr.authorization_code
IS 'codigo de authorizacion de pago encryptado, con algoritmo 3DES';

ALTER TABLE obingresos.treserva_pnr
  OWNER TO postgres;

ALTER TABLE obingresos.tboleto_amadeus
  ADD COLUMN id_pv_reserva INTEGER;

COMMENT ON COLUMN obingresos.tboleto_amadeus.id_pv_reserva
IS 'id punto de venta reserva';
/***********************************F-SCP-BVP-OBINGRESOS-0-17/11/2021*****************************************/

/***********************************I-SCP-BVP-OBINGRESOS-0-03/12/2021****************************************/
ALTER TABLE obingresos.treserva_pnr
  ADD COLUMN fecha_llamada_consulta_reserva TIMESTAMP(0) WITHOUT TIME ZONE;

COMMENT ON COLUMN obingresos.treserva_pnr.fecha_llamada_consulta_reserva
  IS 'Fecha y hora de llamada a servicio de reserva';

ALTER TABLE obingresos.treserva_pnr
ALTER COLUMN fecha_llamada_consulta_reserva SET DEFAULT now();

ALTER TABLE obingresos.treserva_pnr
  ADD COLUMN fecha_respuesta_consulta_reserva TIMESTAMP(0) WITHOUT TIME ZONE;

COMMENT ON COLUMN obingresos.treserva_pnr.fecha_respuesta_consulta_reserva
  IS 'Fecha y hora respuesta servicio consulta reserva';

ALTER TABLE obingresos.treserva_pnr
  ADD COLUMN fecha_llamada_emision_reserva TIMESTAMP(0) WITHOUT TIME ZONE;

COMMENT ON COLUMN obingresos.treserva_pnr.fecha_llamada_emision_reserva
  IS 'Fecha y hora llamada al servicio de emision de boletos mediante pnr de reserva.';

ALTER TABLE obingresos.treserva_pnr
  ADD COLUMN fecha_respuesta_emision_reserva TIMESTAMP(0) WITHOUT TIME ZONE;

COMMENT ON COLUMN obingresos.treserva_pnr.fecha_respuesta_emision_reserva
  IS 'Fecha y hora de respuesta servicio de emision de reserva';

ALTER TABLE obingresos.treserva_pnr
  ADD COLUMN fecha_llamada_tkts_emitido TIMESTAMP(0) WITHOUT TIME ZONE;

COMMENT ON COLUMN obingresos.treserva_pnr.fecha_llamada_tkts_emitido
  IS 'Fecha y hora llamada a servicio que devuelve informacion de los boletos emitidos';

ALTER TABLE obingresos.treserva_pnr
  ADD COLUMN fecha_respuesta_tkts_emitido TIMESTAMP(0) WITHOUT TIME ZONE;

COMMENT ON COLUMN obingresos.treserva_pnr.fecha_respuesta_tkts_emitido
  IS 'Fecha y hora respuesta que devuelve informacion de los boletos emitidos';

ALTER TABLE obingresos.treserva_pnr
  ADD COLUMN fecha_llamada_servicio_factura TIMESTAMP(0) WITHOUT TIME ZONE;

ALTER TABLE obingresos.treserva_pnr
  ADD COLUMN fecha_respuesta_servicio_factura TIMESTAMP(0) WITHOUT TIME ZONE;

ALTER TABLE obingresos.treserva_pnr
  ADD COLUMN cantidad_llamada_consulta_reserva INTEGER;

ALTER TABLE obingresos.treserva_pnr
  ADD COLUMN cantidad_llamada_tkts_emitido INTEGER;
/***********************************F-SCP-BVP-OBINGRESOS-0-03/12/2021*****************************************/
/***********************************I-SCP-BVP-OBINGRESOS-0-13/12/2021****************************************/
ALTER TABLE obingresos.treserva_pnr
  ADD COLUMN fecha_emision DATE;

COMMENT ON COLUMN obingresos.treserva_pnr.fecha_emision
IS 'fecha emision de reserva';

ALTER TABLE obingresos.treserva_pnr
  ADD COLUMN datos_emision varchar;

COMMENT ON COLUMN obingresos.treserva_pnr.datos_emision
IS 'informacion registrada por el cajero para la emision de la reserva';
/***********************************F-SCP-BVP-OBINGRESOS-0-13/12/2021*****************************************/
/***********************************I-SCP-IRVA-OBINGRESOS-0-23/02/2022****************************************/
CREATE TABLE obingresos.ttlog_boleto_amadeus_forma_pago (
  id_log SERIAL,
  id_boleto_amadeus_forma_pago INTEGER NOT NULL,
  observaciones TEXT NOT NULL,
  estado VARCHAR(50) DEFAULT 'eliminado'::character varying NOT NULL,
  CONSTRAINT ttlog_boleto_amadeus_forma_pago_pkey PRIMARY KEY(id_log)
) INHERITS (pxp.tbase)
WITH (oids = false);

COMMENT ON COLUMN obingresos.ttlog_boleto_amadeus_forma_pago.id_boleto_amadeus_forma_pago
IS 'Id de la forma de pago eliminada';

COMMENT ON COLUMN obingresos.ttlog_boleto_amadeus_forma_pago.observaciones
IS 'Alguna descripcion para saber de donde esta ingresando la modificacion';

COMMENT ON COLUMN obingresos.ttlog_boleto_amadeus_forma_pago.estado
IS 'Estado de la forma de pago, este campo se puede usar en el stage como procesado, etc para marcar cuales ya se excluyeron';

ALTER TABLE obingresos.ttlog_boleto_amadeus_forma_pago
  OWNER TO postgres;
/***********************************F-SCP-IRVA-OBINGRESOS-0-23/02/2022*****************************************/

/***********************************I-SCP-BVP-OBINGRESOS-0-23/05/2022****************************************/
COMMENT ON COLUMN obingresos.treserva_pnr.id_punto_venta
IS 'punto de venta aperturada por el cajero en el ERP, para emision de reserva';


ALTER TABLE obingresos.treserva_pnr
  ADD COLUMN office_id_reserva_pnr VARCHAR(50);

COMMENT ON COLUMN obingresos.treserva_pnr.office_id_reserva_pnr
IS 'office id del punto de venta donde fue generada el PNR de reserva.';


ALTER TABLE obingresos.treserva_pnr
  ADD COLUMN identifier_pnr VARCHAR(100);

COMMENT ON COLUMN obingresos.treserva_pnr.identifier_pnr
IS 'identificador apellido de pasajero, usado para consulta en el servicio de boletos emitidos';


ALTER TABLE obingresos.treserva_pnr
  ADD COLUMN moneda_reserva VARCHAR(20);

COMMENT ON COLUMN obingresos.treserva_pnr.moneda_reserva
IS 'moneda reserva';
/***********************************F-SCP-BVP-OBINGRESOS-0-23/05/2022*****************************************/