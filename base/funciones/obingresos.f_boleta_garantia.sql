CREATE OR REPLACE FUNCTION obingresos.f_boleta_garantia (
  p_id_agencia integer
)
RETURNS numeric AS
$body$
DECLARE
   v_nombre_funcion   	text;
   v_resp    			varchar;
   v_mensaje 			varchar;
   v_reusltado			numeric;
BEGIN
	v_nombre_funcion = 'obingresos.f_boleta_garantia';

		select  COALESCE( mo.monto_total,0)
        into
        v_reusltado
from obingresos.tmovimiento_entidad mo
where mo.garantia = 'si' and mo.id_periodo_venta is null
and mo.id_agencia = p_id_agencia;

RETURN v_reusltado;
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