CREATE OR REPLACE FUNCTION obingresos.f_reprocesar_movimientos (
  p_id_periodo_revertir integer,
  p_id_periodo_anterior integer
)
RETURNS void AS
$body$
DECLARE
   v_nombre_funcion   	text;
   v_resp    			varchar;
   v_mensaje 			varchar;
   v_agencias			record;
   v_periodo 			record;
   v_nro_deposito 		record;
   v_monto				numeric;
   v_monto_deposito		numeric;
BEGIN

	FOR v_agencias in (select 	id_agencia,
                                nombre
                                from obingresos.tagencia)LOOP

    	IF EXISTS(select 1
                  from obingresos.tmovimiento_entidad
                  where id_agencia = v_agencias.id_agencia and id_periodo_venta = p_id_periodo_revertir)THEN

            raise notice 'llega agencia: %',v_agencias.id_agencia;

If (v_agencias.id_agencia<>78)then --grover

            if  (select  pe.estado
                from obingresos.tperiodo_venta_agencia pe
                where pe.id_agencia = v_agencias.id_agencia
                and pe.id_periodo_venta = p_id_periodo_revertir) = 'abierto' then

                --raise exception 'llega estado';
                raise notice 'Agencia con estado Abierto';


                ------Elimnar la boleta garatia

                delete from obingresos.tmovimiento_entidad mo
                where mo.id_agencia = v_agencias.id_agencia
                and mo.id_periodo_venta = p_id_periodo_revertir
                and mo.garantia = 'si'
                and mo.tipo = 'credito';

                --Eliminar boletos asignado con periodo 43

                update obingresos.tmovimiento_entidad set
        		id_periodo_venta = null
				where id_agencia = v_agencias.id_agencia
                and id_periodo_venta = p_id_periodo_revertir
                and cierre_periodo = 'no'
            	and tipo = 'debito';


            ---depoisto
	for v_nro_deposito in ( select mo.autorizacion__nro_deposito
               from obingresos.tmovimiento_entidad mo
               where mo.id_agencia = v_agencias.id_agencia
               and mo.id_periodo_venta = p_id_periodo_revertir
               and mo.tipo = 'credito'
               and mo.autorizacion__nro_deposito is not null)loop

    			raise notice 'Llega numero de deposito %, agencia %, periodo %',v_nro_deposito.autorizacion__nro_deposito,v_agencias.id_agencia,p_id_periodo_revertir;
    			--raise exception 'llega deposito grover';

               if exists ( select 1
                         from obingresos.tmovimiento_entidad mo
                         where mo.id_agencia = v_agencias.id_agencia
                         and mo.id_periodo_venta = p_id_periodo_anterior
                         and mo.tipo = 'credito'
                         and mo.autorizacion__nro_deposito = v_nro_deposito.autorizacion__nro_deposito )then

             --  raise exception 'existis';
                      delete from obingresos.tmovimiento_entidad mov
                      where mov.id_agencia = v_agencias.id_agencia
                      and mov.id_periodo_venta = p_id_periodo_revertir
                      and mov.autorizacion__nro_deposito = v_nro_deposito.autorizacion__nro_deposito;

                      select de.monto_deposito
                      into v_monto_deposito
                      from obingresos.tdeposito de
                      where de.nro_deposito  = v_nro_deposito.autorizacion__nro_deposito;


                     update obingresos.tmovimiento_entidad set
                     monto = v_monto_deposito,
                     monto_total = v_monto_deposito
                     where id_agencia = v_agencias.id_agencia
                     and id_periodo_venta = p_id_periodo_anterior
                     and autorizacion__nro_deposito = v_nro_deposito.autorizacion__nro_deposito;
               else
                --  raise exception 'no %',v_nro_deposito.autorizacion__nro_deposito;

                   update obingresos.tmovimiento_entidad set
                   id_periodo_venta = null
                   where id_agencia = v_agencias.id_agencia
                   and autorizacion__nro_deposito = v_nro_deposito.autorizacion__nro_deposito;
              end if;
    	end loop;

               if exists ( select 1
                          from obingresos.tmovimiento_entidad mo
                          where mo.id_agencia = v_agencias.id_agencia
                          and mo.id_periodo_venta = p_id_periodo_anterior
                          and mo.cierre_periodo = 'si'
                          and mo.tipo = 'debito')then

                delete from obingresos.tmovimiento_entidad mo
                where mo.id_agencia = v_agencias.id_agencia
                and mo.tipo = 'credito'
                and mo.autorizacion__nro_deposito is null
                and mo.id_periodo_venta = p_id_periodo_revertir;

               end if;



            ELSE
            	--Eliminar el saldo del perido anterios

            	delete from obingresos.tmovimiento_entidad mo
        		where mo.id_agencia = v_agencias.id_agencia
                and mo.id_periodo_venta = p_id_periodo_revertir
                and mo.cierre_periodo = 'si'
        		and mo.tipo = 'debito';


                --Eliminar boletos asignado con periodo 43

                update obingresos.tmovimiento_entidad set
        		id_periodo_venta = null
				where id_agencia = v_agencias.id_agencia
                and id_periodo_venta = p_id_periodo_revertir
                and cierre_periodo = 'no'
            	and tipo = 'debito';


          ---Eliminar boleta de garantia

                delete from obingresos.tmovimiento_entidad mo
                where mo.id_agencia = v_agencias.id_agencia
                and mo.id_periodo_venta = p_id_periodo_revertir
                and mo.garantia = 'si'
                and mo.tipo = 'credito';

                --Eliminar saldo a favor

                delete from obingresos.tmovimiento_entidad mo
                where mo.id_agencia = v_agencias.id_agencia
                and mo.id_periodo_venta is null
                and mo.cierre_periodo = 'si'
                and mo.tipo = 'credito';


                ---Eliminar saldo de 42

                delete from obingresos.tmovimiento_entidad mo
                where mo.id_agencia = v_agencias.id_agencia
                and mo.id_periodo_venta = p_id_periodo_revertir
                and mo.cierre_periodo = 'si'
                and mo.tipo = 'credito';

                --Eliminar el periodo 43

          for v_nro_deposito in ( select mo.autorizacion__nro_deposito
               from obingresos.tmovimiento_entidad mo
               where mo.id_agencia = v_agencias.id_agencia
               and mo.id_periodo_venta = p_id_periodo_revertir
               and mo.tipo = 'credito'
               and mo.autorizacion__nro_deposito is not null)loop

               if exists ( select 1
                         from obingresos.tmovimiento_entidad mo
                         where mo.id_agencia = v_agencias.id_agencia
                         and mo.id_periodo_venta = p_id_periodo_anterior
                         and mo.tipo = 'credito'
                         and mo.autorizacion__nro_deposito = v_nro_deposito.autorizacion__nro_deposito )then

             --  raise exception 'existis';
                      delete from obingresos.tmovimiento_entidad mov
                      where mov.id_agencia = v_agencias.id_agencia
                      and mov.id_periodo_venta = p_id_periodo_revertir
                      and mov.autorizacion__nro_deposito = v_nro_deposito.autorizacion__nro_deposito;

                      select de.monto_deposito
                      into v_monto_deposito
                      from obingresos.tdeposito de
                      where de.nro_deposito  = v_nro_deposito.autorizacion__nro_deposito;


                     update obingresos.tmovimiento_entidad set
                     monto = v_monto_deposito,
                     monto_total = v_monto_deposito
                     where id_agencia = v_agencias.id_agencia
                     and id_periodo_venta = p_id_periodo_anterior
                     and autorizacion__nro_deposito = v_nro_deposito.autorizacion__nro_deposito;
               else
                --  raise exception 'no %',v_nro_deposito.autorizacion__nro_deposito;

                   update obingresos.tmovimiento_entidad set
                   id_periodo_venta = null
                   where id_agencia = v_agencias.id_agencia
                   and autorizacion__nro_deposito = v_nro_deposito.autorizacion__nro_deposito;
              end if;
               end loop;
                ---insertar saldo a favor
                 if exists ( select 1
                          from obingresos.tmovimiento_entidad mo
                          where mo.id_agencia = v_agencias.id_agencia
                          and mo.id_periodo_venta = p_id_periodo_anterior
                          and mo.cierre_periodo = 'si'
                          and mo.tipo = 'debito')then

                select mo.monto
            	into v_monto
                from obingresos.tmovimiento_entidad mo
                where mo.id_agencia = v_agencias.id_agencia
                and mo.id_periodo_venta = p_id_periodo_anterior
                and mo.cierre_periodo = 'si'
                and mo.tipo = 'debito';

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
                                                              v_monto,
                                                              1,
                                                              null,
                                                              'no',
                                                              'no',
                                                               null,
                                                               v_agencias.id_agencia,
                                                               v_monto,
                                                               'no'
                                                              );
                end if;


            end if;
    	 END IF;  --grover

   	 END IF;


     if(v_agencias.id_agencia <>78)then --grover
          if  (select  pe.estado
                from obingresos.tperiodo_venta_agencia pe
                where pe.id_agencia = v_agencias.id_agencia
                and pe.id_periodo_venta = p_id_periodo_revertir) = 'abierto' then

                 select  pg.monto_mb,
                		 pg.estado
                         into
                         v_periodo
                        from obingresos.tperiodo_venta_agencia pg
                        where pg.id_agencia = v_agencias.id_agencia
                        and pg.id_periodo_venta = p_id_periodo_revertir;

                  update obingresos.tperiodo_venta_agencia set
                  monto_mb = v_periodo.monto_mb,
                  estado = 'abierto'
                  where id_agencia = v_agencias.id_agencia
                  and id_periodo_venta = p_id_periodo_anterior;
          end if;

     end if;--grover

                delete from obingresos.tperiodo_venta_agencia pr
                where pr.id_agencia = v_agencias.id_agencia
                and pr.id_periodo_venta = p_id_periodo_revertir;

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