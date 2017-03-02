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