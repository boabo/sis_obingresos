CREATE OR REPLACE FUNCTION obingresos.f_incrementar_credito_md (
  p_id_periodo varchar,
  p_id_agencia integer,
  p_monto numeric
)
RETURNS void AS
$body$
DECLARE
 v_nombre_funcion   	 text;
 v_resp    				 varchar;
 v_mensaje 			 	 varchar;
 v_ids_peridos			 integer[];
 v_id_periodo 			 integer;
 v_fecha_max			 date;
 v_monto numeric;
 

BEGIN
v_nombre_funcion = 'vef.f_incrementar_credito_md';

  v_ids_peridos	 = string_to_array(p_id_periodo,',');
  
  /*select string_to_array(ve.id_periodo_venta::varchar,',')
  into 
  v_ids_peridos
  from obingresos.tperiodo_venta ve
  where ve.id_periodo_venta >= 24;*/
 

  
  FOREACH v_id_periodo IN ARRAY v_ids_peridos
  LOOP

  INSERT INTO 
                        obingresos.tmovimiento_entidad
                      (
                        id_usuario_reg,                        
                        id_usuario_ai,
                        usuario_ai,                        
                        tipo,
                        pnr,
                        fecha,
                        apellido,
                        monto,
                        id_moneda,
                        autorizacion__nro_deposito,
                        garantia,
                        ajuste,
                        id_periodo_venta,
                        id_agencia,
                        monto_total,
                        cierre_periodo
                      )
                      VALUES (
                        366,                        
                        null,
              		  	 null,
                        'debito',
                        NULL,
                        now()::date,
                        NULL,
                        p_monto,
                        1,
                        'Ajuste comision',
                        'no',
                        'si',
                        v_id_periodo,
                        p_id_agencia,
                        p_monto,
                        'no'
                      );
  
  update obingresos.tperiodo_venta_agencia s set 
        monto_debito_mb = monto_debito_mb + p_monto
        where id_agencia = p_id_agencia and id_periodo_venta = v_id_periodo;
     
        
  END LOOP;
  
  
  INSERT INTO 
                        obingresos.tmovimiento_entidad
                      (
                        id_usuario_reg,                        
                        id_usuario_ai,
                        usuario_ai,                        
                        tipo,
                        pnr,
                        fecha,
                        apellido,
                        monto,
                        id_moneda,
                        autorizacion__nro_deposito,
                        garantia,
                        ajuste,
                        id_periodo_venta,
                        id_agencia,
                        monto_total,
                        cierre_periodo
                      )
                      VALUES (
                        366,                        
                        null,
              		  	 null,
                        'debito',
                        NULL,
                        now()::date,
                        NULL,
                        p_monto,
                        1,
                        'Ajuste comision',
                        'no',
                        'si',
                        null,
                        p_id_agencia,
                        p_monto,
                        'no'
                      );
  
  
  

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