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
  codigo_noiata VARCHAR(20) NOT NULL,
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
  ADD COLUMN retbsp VARCHAR(5) NOT NULL;

ALTER TABLE obingresos.tboleto
  ADD COLUMN estado VARCHAR(20);
  
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
/********************************************F-SCP-JRR-OBINGRESOS-0-08/04/2016********************************************/
