CREATE OR REPLACE FUNCTION obingresos.f_get_total_depositos_agencia (
  p_tipo varchar,
  p_fecha_ini date,
  p_fecha_fin date,
  p_estado varchar,
  p_id_agencia integer
)
RETURNS numeric AS
$body$
DECLARE

	 v_resp							varchar;
     v_nombre_funcion   			varchar;
     v_total						numeric;
   
 
BEGIN
  	   v_nombre_funcion:='obingresos.f_get_total_depositos_agencia';
     
     select sum(d.monto_deposito)
     into 
     v_total
      from obingresos.tdeposito d
      where d.tipo = p_tipo and d.fecha  BETWEEN p_fecha_ini and p_fecha_fin
       and d.estado = p_estado and d.id_agencia = p_id_agencia;
    
        
       return COALESCE(v_total,0.0);
     
     
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
STABLE
CALLED ON NULL INPUT
SECURITY INVOKER
COST 100;