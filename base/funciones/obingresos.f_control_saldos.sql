CREATE OR REPLACE FUNCTION obingresos.f_control_saldos (
  p_id_periodo integer
)
RETURNS void AS
$body$
DECLARE
 v_nombre_funcion   	 text;
 v_resp    				 varchar;
 v_mensaje 			 	 varchar;
 v_record 				 record;
 v_credito				 numeric;
 v_debito 				 numeric;
 v_resultado 			 numeric;
 v_saldo				record;




BEGIN
v_nombre_funcion = 'obingresos.f_control_saldos';



  FOR v_record in (select id_agencia
                   from obingresos.tagencia
                   ) LOOP


       	IF EXISTS(select 1
                  from obingresos.tmovimiento_entidad
                  where id_agencia = v_record.id_agencia )THEN

      select  COALESCE( sum (mo.monto),0)
            into
            v_credito
            from obingresos.tmovimiento_entidad mo
            where mo.id_agencia = v_record.id_agencia and mo.tipo = 'credito'
            and mo.estado_reg = 'activo'
            and mo.id_periodo_venta = 41
            and mo.garantia = 'no';


      select  COALESCE(sum ( mo.monto),0)
      			into
                v_debito
                from obingresos.tmovimiento_entidad mo
                where mo.id_agencia = v_record.id_agencia and mo.tipo = 'debito'
                and mo.id_periodo_venta = 41
                and mo.estado_reg = 'activo'
                and mo.cierre_periodo = 'no';

                v_resultado =  v_credito - v_debito;

            -- raise exception 'id %',v_resultado;

      		if exists ( select 1
                        from obingresos.tmovimiento_entidad mo
                        where mo.id_agencia = v_record.id_agencia
                        and mo.id_periodo_venta = 41
                        and mo.tipo = 'debito'
                        and mo.cierre_periodo = 'si'
                        and mo.monto = v_resultado)then

            else
             if v_resultado <> 0 then
            		insert into obingresos.tsaldo_agencia( monto,
                                                            id_periodo,
                                                            tipo,
                                                            cierre_periodo,
                                                            id_agencia
                                      						)
                                                            values (v_resultado,
                                                                   41,
                                                                    'debito',
                                                                    'si',
                                                                    v_record.id_agencia
                                                            );
                    end if;
            end if;

                select  COALESCE( sum (mo.monto),0)
            into
            v_credito
            from obingresos.tmovimiento_entidad mo
            where mo.id_agencia = v_record.id_agencia and mo.tipo = 'credito'
            and mo.estado_reg = 'activo'
            and mo.id_periodo_venta = 42
            and mo.garantia = 'no';


      select  COALESCE(sum ( mo.monto),0)
      			into
                v_debito
                from obingresos.tmovimiento_entidad mo
                where mo.id_agencia = v_record.id_agencia and mo.tipo = 'debito'
                and mo.id_periodo_venta = 42
                and mo.estado_reg = 'activo'
                and mo.cierre_periodo = 'no';

                v_resultado =  v_credito - v_debito;

            -- raise exception 'id %',v_resultado;

      		if exists ( select 1
                        from obingresos.tmovimiento_entidad mo
                        where mo.id_agencia = v_record.id_agencia
                        and mo.id_periodo_venta = 42
                        and mo.tipo = 'debito'
                        and mo.cierre_periodo = 'si'
                        and mo.monto = v_resultado)then

            else
            if v_resultado <> 0 then
            		insert into obingresos.tsaldo_agencia( monto,
                                                            id_periodo,
                                                            tipo,
                                                            cierre_periodo,
                                                            id_agencia
                                      						)
                                                            values (v_resultado,
                                                                   42,
                                                                    'debito',
                                                                    'si' ,
                                                                    v_record.id_agencia
                                                            );
                                                            end if;
            end if;
          select  COALESCE( sum (mo.monto),0)
            into
            v_credito
            from obingresos.tmovimiento_entidad mo
            where mo.id_agencia = v_record.id_agencia and mo.tipo = 'credito'
            and mo.estado_reg = 'activo'
            and mo.id_periodo_venta = 44
            and mo.garantia = 'no';


      select  COALESCE(sum ( mo.monto),0)
      			into
                v_debito
                from obingresos.tmovimiento_entidad mo
                where mo.id_agencia = v_record.id_agencia and mo.tipo = 'debito'
                and mo.id_periodo_venta = 44
                and mo.estado_reg = 'activo'
                and mo.cierre_periodo = 'no';

                v_resultado =  v_credito - v_debito;

            -- raise exception 'id %',v_resultado;

      		if exists ( select 1
                        from obingresos.tmovimiento_entidad mo
                        where mo.id_agencia = v_record.id_agencia
                        and mo.id_periodo_venta = 44
                        and mo.tipo = 'debito'
                        and mo.cierre_periodo = 'si'
                        and mo.monto = v_resultado)then

            else
            if v_resultado <> 0 then
            		insert into obingresos.tsaldo_agencia( monto,
                                                            id_periodo,
                                                            tipo,
                                                            cierre_periodo,
                                                            id_agencia
                                      						)
                                                            values (v_resultado,
                                                                   44,
                                                                    'debito',
                                                                    'si' ,
                                                                    v_record.id_agencia
                                                            );
                                                            end if;
            end if;


         END IF;

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