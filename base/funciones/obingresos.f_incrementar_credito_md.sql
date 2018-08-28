CREATE OR REPLACE FUNCTION obingresos.f_incrementar_credito_md (
)
RETURNS void AS
$body$
DECLARE
 v_nombre_funcion   	 text;
 v_resp    				 varchar;

 v_mensaje 			 	 varchar;
 v_record				record;

BEGIN
v_nombre_funcion = 'vef.f_incrementar_credito_md';


  FOR v_record in ( select 	mo.id_agencia,
        					mo.monto,
                			mo.cierre_periodo
                            from obingresos.tmovimiento_entidad mo
                            where mo.tipo = 'debito' and mo.ajuste = 'no'
                            and mo.garantia = 'no' and mo.id_periodo_venta = 51 and mo.cierre_periodo = 'si'
                            and mo.id_agencia <> 78 )LOOP

                            IF( not exists ( select 1
                          	             from obingresos.tmovimiento_entidad mo
                         				 where mo.id_agencia = v_record.id_agencia
                          				 and mo.id_periodo_venta is null
                          				 and mo.cierre_periodo = 'si'
                                         and mo.ajuste = 'no'
                                         and mo.garantia = 'no'
                                         and mo.tipo = 'credito') )THEN

                             RAISE NOTICE 'no hay %',v_record.id_agencia ;
                            /*
                             insert into obingresos.tmovimiento_entidad ( id_usuario_reg,
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
                                                              cierre_periodo)
                                                              VALUES (
                                                              1,
                                                              'credito',
                                                              null,
                                                              now()::date,
                                                              null,
                                                              v_record.monto,
                                                              1,
                                                              null,
                                                              'no',
                                                              'no',
                                                              null,
                                                              v_record.id_agencia,
                                                              v_record.monto,
                                                              'si'
                                                              );
                            */
                            end if ;
  END LOOP;




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