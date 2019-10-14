CREATE OR REPLACE FUNCTION obingresos.f_corregir_periodos_comisiones_portal (
)
RETURNS varchar AS
$body$
DECLARE
  v_nombre_funcion   	text;
  v_resp				varchar;

  v_datos_mov			record;


BEGIN
  v_nombre_funcion = 'obingresos.f_corregir_periodos_comisiones_portal';

  		for v_datos_mov in (
        					select mo.id_movimiento_entidad,
	   						       mo.id_periodo_venta
                            from obingresos.tmovimiento_entidad mo
                            where mo.tipo = 'credito' and mo.id_periodo_venta is not null
                                  and (mo.tipo_void = 'BOLETO' or mo.tipo_void = 'RESERVA'))
  loop

         UPDATE obingresos.tmovimiento_entidad SET
         		id_periodo_venta = v_datos_mov.id_periodo_venta
         Where tipo = 'debito' and (tipo_void = 'BOLETO' or tipo_void = 'RESERVA')
               and fk_id_movimiento_entidad = v_datos_mov.id_movimiento_entidad;

  end loop;

return 'Exito';

END;
$body$
LANGUAGE 'plpgsql'
VOLATILE
CALLED ON NULL INPUT
SECURITY INVOKER
COST 100;

ALTER FUNCTION obingresos.f_corregir_periodos_comisiones_portal ()
  OWNER TO postgres;
