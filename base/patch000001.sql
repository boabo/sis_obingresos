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