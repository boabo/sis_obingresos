CREATE OR REPLACE FUNCTION obingresos.f_generar_total_comision_mes (
  p_id_usuario integer,
  p_fecha date
)
RETURNS varchar AS
$body$
DECLARE
  v_resp	            varchar;
  v_nombre_funcion      text;
  v_id_tipo_periodo_cc	integer;
  v_id_tipo_periodo_ban	integer;
  v_ultimo_dia_mes		date;

BEGIN
	v_nombre_funcion = 'obingresos.f_generar_total_comision_mes';



    select (date_trunc('MONTH', p_fecha) + INTERVAL '1 MONTH - 1 day')::date into v_ultimo_dia_mes;

	--si es el utlimo dia del mes proceso facturas
    if (p_fecha = v_ultimo_dia_mes) then
    	--obtener  id_tipo_periodo_cc
    	select id_tipo_periodo into v_id_tipo_periodo_cc
    	from obingresos.ttipo_periodo tp
    	where tp.estado_reg = 'activo' and tp.medio_pago = 'cuenta_corriente' and tipo = 'portal';


        insert into obingresos.ttotal_comision_mes
       		(gestion,periodo,id_agencia,id_usuario_reg,
            total_comision,id_tipo_periodo,
            id_periodos,estado)
        select to_char(p_fecha,'YYYY')::numeric, to_char(p_fecha,'MM')::numeric,pva.id_agencia,
        p_id_usuario,sum(pva.total_comision_mb),pv.id_tipo_periodo,
        array_agg(pva.id_periodo_venta),'pendiente'
        from obingresos.tperiodo_venta pv
        inner join obingresos.tperiodo_venta_agencia pva on pv.id_periodo_venta = pva.id_periodo_venta
        where to_char(pv.fecha_fin,'MM') =  to_char(p_fecha,'MM') and to_char(pv.fecha_fin,'YYYY') =  to_char(p_fecha,'YYYY')
        group by pva.id_agencia,pv.id_tipo_periodo;


    end if;

  	return 'exito';
EXCEPTION

	WHEN OTHERS THEN
		v_resp='';
		v_resp = pxp.f_agrega_clave(v_resp,'mensaje',SQLERRM);
		v_resp = pxp.f_agrega_clave(v_resp,'codigo_error',SQLSTATE);
		v_resp = pxp.f_agrega_clave(v_resp,'procedimientos',v_nombre_funcion);
		raise exception '%',v_resp;

END;
$body$
LANGUAGE 'plpgsql'
VOLATILE
CALLED ON NULL INPUT
SECURITY INVOKER
COST 100;