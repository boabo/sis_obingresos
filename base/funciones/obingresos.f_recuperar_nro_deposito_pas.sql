CREATE OR REPLACE FUNCTION obingresos.f_recuperar_nro_deposito_pas (
  p_id_deposito integer
)
RETURNS varchar AS
$body$
DECLARE
   v_resp	            varchar;
  v_nombre_funcion      text;
  v_consulta			varchar;
BEGIN
v_nombre_funcion = 'vef.f_recuperar_nro_deposito_pas';
select COALESCE (d.nro_deposito_aux,d.nro_deposito) as nro_deposito
into
v_consulta
  from obingresos.tdeposito d
  where d.id_deposito = p_id_deposito;

return v_consulta;

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