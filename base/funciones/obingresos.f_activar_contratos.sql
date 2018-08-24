CREATE OR REPLACE FUNCTION obingresos.f_activar_contratos (
  p_id_agencia integer
)
RETURNS void AS
$body$
DECLARE
   v_nombre_funcion   	text;
   v_resp    			varchar;
   v_mensaje 			varchar; 
   v_record				record;
BEGIN
	v_nombre_funcion = 'obingresos.f_activar_contratos';
  
for v_record in  (select ag.id_agencia
                      from obingresos.tagencia ag
                      where ag.id_agencia in (p_id_agencia))loop
                      
                      
                      if exists (select 1
                                  from obingresos.tagencia ag
                                  where ag.id_agencia = v_record.id_agencia)then
                                  
                            if exists (    select 1
                                from leg.tcontrato c
                                where c.id_agencia = v_record.id_agencia)then
                                
                                update leg.tcontrato set
                                estado = 'finalizado' 
                                where id_agencia = v_record.id_agencia and fecha_fin >= now()::date;
                                
                            else
                                raise exception 'No existe un contrato para la Agencia %',v_record.id_agencia;
                            end if;        
                      else 
                      	raise exception 'No existe u la Agencia %',v_record.id_agencia;
                      end if ;
                      
end loop;
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