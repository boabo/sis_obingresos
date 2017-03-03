/***********************************I-DAT-JRR-OBINGRESOS-0-26/01/2016*****************************************/

INSERT INTO segu.tsubsistema ("codigo", "nombre", "fecha_reg", "prefijo", "estado_reg", "nombre_carpeta", "id_subsis_orig")
VALUES (E'OBINGRESOS', E'Ingresos', E'2015-12-10', E'OBING', E'activo', E'obingresos', NULL);

select pxp.f_insert_tgui ('INGRESOS', '', 'OBINGRESOS', 'si', 1, '', 1, '', '', 'OBINGRESOS');
select pxp.f_insert_tgui ('Agencias', 'Agencias', 'AGEING', 'si', 1, 'sis_obingresos/vista/agencia/Agencia.php', 2, '', 'Agencia', 'OBINGRESOS');
select pxp.f_insert_tgui ('Periodo de Venta', 'Periodo de Venta', 'PERVEN', 'si', 2, 'sis_obingresos/vista/periodo_venta/PeriodoVenta.php', 2, '', 'PeriodoVenta', 'OBINGRESOS');
----------------------------------
--COPY LINES TO dependencies.sql FILE  
---------------------------------

select pxp.f_insert_testructura_gui ('OBINGRESOS', 'SISTEMA');
select pxp.f_insert_testructura_gui ('AGEING', 'OBINGRESOS');
select pxp.f_insert_testructura_gui ('PERVEN', 'OBINGRESOS');
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
select pxp.f_insert_tgui ('Depositos Banca X Inter', 'Depositos Banca X Inter', 'DEPBANINT', 'si', 3, '/sis_obingresos/vista/deposito/DepositoVentaWeb.php', 2, '', 'DepositoVentaWeb', 'OBINGRESOS');
select pxp.f_insert_testructura_gui ('DEPBANINT', 'OBINGRESOS');

select pxp.f_insert_tgui ('Reporte Deposito Banca X Inter', 'Reporte Deposito Banca X Inter', 'RDEPBINT', 'si', 5, '/sis_obingresos/vista/deposito/ReporteDepositoBancaInter.php', 2, '', 'ReporteDepositoBancaInter', 'OBINGRESOS');
select pxp.f_insert_testructura_gui ('RDEPBINT', 'OBINGRESOS');

/***********************************F-DAT-JRR-OBINGRESOS-0-01/03/2017*****************************************/

