/***********************************I-DAT-JRR-OBINGRESOS-0-26/01/2016*****************************************/

INSERT INTO segu.tsubsistema ("codigo", "nombre", "fecha_reg", "prefijo", "estado_reg", "nombre_carpeta", "id_subsis_orig")
VALUES (E'OBINGRESOS', E'Ingresos', E'2015-12-10', E'OBING', E'activo', E'obingresos', NULL);

select pxp.f_insert_tgui ('INGRESOS', '', 'OBINGRESOS', 'si', 1, '', 1, '', '', 'OBINGRESOS');
select pxp.f_insert_tgui ('Agencias', 'Agencias', 'AGEING', 'si', 1, 'sis_obingresos/vista/agencia/Agencia.php', 2, '', 'Agencia', 'OBINGRESOS');
select pxp.f_insert_tgui ('Periodos', 'Periodos de Venta', 'INPERVEN', 'si', 2, '', 2, '', '', 'OBINGRESOS');
select pxp.f_insert_tgui ('Periodo de Venta', 'Periodo de Venta', 'PERVEN', 'si', 2, 'sis_obingresos/vista/periodo_venta/PeriodoVenta.php', 2, '', 'PeriodoVenta', 'OBINGRESOS');

----------------------------------
--COPY LINES TO dependencies.sql FILE  
---------------------------------

select pxp.f_insert_testructura_gui ('OBINGRESOS', 'SISTEMA');
select pxp.f_insert_testructura_gui ('AGEING', 'OBINGRESOS');

select pxp.f_insert_testructura_gui ('INPERVEN', 'OBINGRESOS');
select pxp.f_insert_testructura_gui ('PERVEN', 'INPERVEN');
/***********************************F-DAT-JRR-OBINGRESOS-0-26/01/2016*****************************************/

/***********************************I-DAT-MAM-OBINGRESOS-0-16/11/2016*****************************************/
select pxp.f_insert_tgui ('Reporte Nit y Razon', 'reporte nit y razon', 'RNR', 'si', 4, '/sis_obingresos/vista/reporte_nit_razon/ReporteNitRazon.php', 2, '', 'ReporteNitRazon', 'OBINGRESOS');
select pxp.f_insert_testructura_gui ('RNR', 'OBINGRESOS');
/***********************************F-DAT-MAM-OBINGRESOS-0-16/11/2016*****************************************/

/***********************************I-DAT-MAM-OBINGRESOS-0-25/11/2016*****************************************/
select pxp.f_insert_tgui ('Subir Deposito', 'Subir deposito', 'SUDE', 'si', 3, '/sis_obingresos/vista/deposito/SubirDeposito.php', 2, '', 'SubirDeposito', 'OBINGRESOS');
select pxp.f_insert_testructura_gui ('SUDE', 'OBINGRESOS');
select pxp.f_insert_tgui ('Reporte Deposito', 'Reporte Deposito', 'REDE', 'si', 5, '/sis_obingresos/vista/deposito/ReporteDeposito.php', 2, '', 'ReporteDeposito', 'OBINGRESOS');
select pxp.f_insert_testructura_gui ('REDE', 'OBINGRESOS');
/***********************************F-DAT-MAM-OBINGRESOS-0-25/11/2016*****************************************/

/***********************************I-DAT-JRR-OBINGRESOS-0-01/03/2017*****************************************/
select pxp.f_insert_tgui ('Banca por Internet', 'Banca por Internet', 'CBANXIN', 'si', 3, '', 2, '', '', 'OBINGRESOS');
select pxp.f_insert_testructura_gui ('CBANXIN', 'OBINGRESOS');

select pxp.f_insert_tgui ('Depositos Banca X Inter', 'Depositos Banca X Inter', 'DEPBANINT', 'si', 3, '/sis_obingresos/vista/deposito/DepositoVentaWeb.php', 2, '', 'DepositoVentaWeb', 'OBINGRESOS');
select pxp.f_insert_testructura_gui ('DEPBANINT', 'CBANXIN');

select pxp.f_insert_tgui ('Reporte Deposito Banca X Inter', 'Reporte Deposito Banca X Inter', 'RDEPBINT', 'si', 5, '/sis_obingresos/vista/deposito/ReporteDepositoBancaInter.php', 2, '', 'ReporteDepositoBancaInter', 'OBINGRESOS');
select pxp.f_insert_testructura_gui ('RDEPBINT', 'CBANXIN');

/***********************************F-DAT-JRR-OBINGRESOS-0-01/03/2017*****************************************/

/************************************I-DAT-JRR-OBINGRESOS-0-07/04/2017*************************************************/

INSERT INTO pxp.variable_global ("variable", "valor", "descripcion")
VALUES (E'obingresos_validar_tarjeta', E'no', NULL);

/************************************F-DAT-JRR-OBINGRESOS-0-07/04/2017*************************************************/



/************************************I-DAT-JRR-OBINGRESOS-0-17/03/2017*************************************************/

select pxp.f_insert_tgui ('Venta de Boletos', 'Venta de Boletos', 'REGBOL', 'si', 7, 'sis_obingresos/vista/boleto/Boleto.php', 2, '', 'Boleto', 'VEF');
select pxp.f_insert_tgui ('Boletos en Caja', 'Boletos en Caja', 'BOLCAJ', 'si', 7, 'sis_obingresos/vista/boleto/BoletoCaja.php', 2, '', 'BoletoCaja', 'VEF');
select pxp.f_insert_tgui ('Boletos Vendidos', 'Boletos Vendidos', 'VENBOLVEN', 'si', 8, 'sis_obingresos/vista/boleto/BoletoVendido.php', 2, '', 'BoletoVendido', 'VEF');

/************************************F-DAT-JRR-OBINGRESOS-0-17/03/2017*************************************************/

/************************************I-DAT-JRR-OBINGRESOS-0-08/05/2017*************************************************/
select pxp.f_insert_tgui ('Control Portal Corporativo', 'Control Portal Corporativo', 'CONPORCOR', 'si', 1, '', 2, '', '', 'OBINGRESOS');
select pxp.f_insert_tgui ('Tipo de Periodo', 'Tipo de Periodo', 'INTIPPER', 'si', 1, 'sis_obingresos/vista/tipo_periodo/TipoPeriodo.php', 3, '', 'TipoPeriodo', 'OBINGRESOS');
select pxp.f_insert_tgui ('Entidades', 'Entidades', 'POREN', 'si', 1, 'sis_obingresos/vista/agencia/AgenciaPortal.php', 3, '', 'AgenciaPortal', 'OBINGRESOS');
select pxp.f_insert_tgui ('Depositos', 'Depositos', 'INDEPPOR', 'si', 2, 'sis_obingresos/vista/deposito/DepositoPortal.php', 3, '', 'DepositoPortal', 'OBINGRESOS');

select pxp.f_insert_testructura_gui ('CONPORCOR', 'OBINGRESOS');
select pxp.f_insert_testructura_gui ('POREN', 'CONPORCOR');
select pxp.f_insert_testructura_gui ('INDEPPOR', 'CONPORCOR');
select pxp.f_insert_testructura_gui ('INTIPPER', 'INPERVEN');

/************************************F-DAT-JRR-OBINGRESOS-0-08/05/2017*************************************************/

/************************************I-DAT-JRR-OBINGRESOS-0-01/06/2017*************************************************/
select pxp.f_insert_tgui ('Conciliacion Det. Banca X Inter', 'Conciliacion Banca X Inter', 'CONBANINT', 'si', 0, '/sis_obingresos/vista/conciliacion_banca_inter/ConciliacionBancaInter.php', 2, '', 'ConciliacionBancaInter', 'OBINGRESOS');
select pxp.f_insert_testructura_gui ('CONBANINT', 'CBANXIN');

select pxp.f_insert_tgui ('Modificaciones a Venta Web', 'Modificaciones a Venta Web', 'VWEBMOD', 'si', 0, 'sis_obingresos/vista/venta_web_modificaciones/VentaWebModificaciones.php', 2, '', 'VentaWebModificaciones', 'OBINGRESOS');
select pxp.f_insert_testructura_gui ('VWEBMOD', 'CBANXIN');

select pxp.f_insert_tgui ('Reporte Ingresos vs Skybiz', 'Reporte Ingresos vs Skybiz', 'REPBRVW', 'si', 0, '/sis_obingresos/vista/boleto/ReporteBoletoResiberVentasWeb.php', 2, '', 'ReporteBoletoResiberVentasWeb', 'OBINGRESOS');
select pxp.f_insert_testructura_gui ('REPBRVW', 'CBANXIN');

select pxp.f_insert_tgui ('Skybiz', 'Skybiz', 'SKYBIZ', 'si', 0, 'sis_obingresos/vista/skybiz_archivo/SkybizArchivo.php', 2, '', 'SkybizArchivo', 'OBINGRESOS');
select pxp.f_insert_testructura_gui ('SKYBIZ', 'CBANXIN');

select pxp.f_insert_tgui ('Observaciones Conciliacion', 'Observaciones Conciliacion', 'OBSCONC', 'si', 0, 'sis_obingresos/vista/observaciones_conciliacion/ObservacionesConciliacion.php', 5, '', 'ObservacionesConciliacion', 'OBINGRESOS');
select pxp.f_insert_testructura_gui ('OBSCONC', 'CBANXIN');

select pxp.f_insert_tgui ('Conciliacion Res Banca X Inter', 'Conciliacion Banca X Inter', 'CONBANIRES', 'si', 0, '/sis_obingresos/vista/conciliacion_banca_inter/ConciliacionBancaInterRes.php', 2, '', 'ConciliacionBancaInterRes', 'OBINGRESOS');
select pxp.f_insert_testructura_gui ('CONBANIRES', 'CBANXIN');

/************************************F-DAT-JRR-OBINGRESOS-0-01/06/2017*************************************************/
