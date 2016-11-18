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
