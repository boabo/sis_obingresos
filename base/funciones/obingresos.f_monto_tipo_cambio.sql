CREATE OR REPLACE FUNCTION obingresos.f_monto_tipo_cambio (
  p_tipo varchar,
  p_id_apertura_cierre_caja integer
)
RETURNS numeric AS
$body$
DECLARE
  v_nombre_funcion   	text;
  v_resp				varchar;
  v_monto				numeric;	
BEGIN
  v_nombre_funcion = 'obingresos.f_monto_tipo_cambio';
  
  if p_tipo = 'BOB' then
  select COALESCE (sum (d.monto_total),0)
  into
  v_monto
  from obingresos.tdeposito d
  where d.id_apertura_cierre_caja = p_id_apertura_cierre_caja and d.id_moneda_deposito = 1;
  end if;
  
  if p_tipo = 'USD' then
  select COALESCE( sum (d.monto_total),0)
  into
  v_monto
  from obingresos.tdeposito d
  where d.id_apertura_cierre_caja = p_id_apertura_cierre_caja and d.id_moneda_deposito = 2;
  end if;
  
  RETURN v_monto;
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