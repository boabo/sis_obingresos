CREATE OR REPLACE FUNCTION obingresos.f_control_saldo_agencia_gvc (
)
RETURNS void AS
$body$
DECLARE
 v_nombre_funcion   	 text;
 v_resp    				 varchar;
 v_mensaje 			 	 varchar;
 v_ids_peridos			 integer[];


 v_record_1			record;
 v_record_2			record;

 v_total_saldo		numeric;
 v_total_credito	numeric;
 v_total_debito		numeric;
 v_id_periodo_venta integer;




BEGIN
v_nombre_funcion = 'vef.f_control_saldo_agencia_gvc';

 v_total_saldo		=0;
 v_total_credito	=0;
 v_total_debito		=0;

for v_record_1 in (Select DISTINCT a.id_agencia
					from obingresos.tagencia a
    				inner join obingresos.tmovimiento_entidad me on me.id_agencia=a.id_agencia
                    --where a.id_agencia = 127
                    order by a.id_agencia asc) loop

                    --raise notice 'EL ID agencia es:%',v_record_1.id_agencia;

 	for v_record_2 in (Select p.id_periodo_venta
    					from obingresos.tperiodo_venta p
                        order by  p.id_periodo_venta asc) loop

                        --raise notice 'Id_periodo:%',v_record_2.id_periodo_venta;

              --total saldo
                select  Sum(COALESCE(mo.monto_total,0))
                into v_total_saldo
                  from obingresos.tmovimiento_entidad mo
                  where mo.tipo = 'credito' and
                    mo.id_agencia = v_record_1.id_agencia AND
                        mo.estado_reg = 'activo' and
                        mo.id_periodo_venta=v_record_2.id_periodo_venta and
                        mo.garantia = 'no' and
                        mo.cierre_periodo = 'si'
                  group by mo.id_periodo_venta
                  order by mo.id_periodo_venta asc;

                  IF(v_total_saldo is null)then
                  	v_total_saldo=0;
                  end if;

                 --raise notice  'v_total_credito:%,v_total_debito:%,v_total_saldo%',v_total_credito,v_total_debito,v_total_saldo;

                IF( v_total_credito -  v_total_debito)<> v_total_saldo then

                raise notice 'EL ID agencia es:%',v_record_1.id_agencia;
                raise notice 'Id_periodo:%',v_record_2.id_periodo_venta;
                raise notice 'v_total_credito:%,v_total_debito:%,v_total_saldo:%',v_total_credito,v_total_debito,v_total_saldo;


                	INSERT INTO
                      obingresos.t_diferencias_saldos
                    (
                      id_agencia,
                      id_periodo_venta,
                      total_credito,
                      total_debito,
                      saldo_arrastrado,
                      saldo_calculado,
                      diferencia_saldos
                    )
                    VALUES (

                      v_record_1.id_agencia,
                      v_id_periodo_venta,
                      v_total_credito,
                      v_total_debito,
                      v_total_saldo,
                      v_total_credito-v_total_debito,
                      (v_total_credito-v_total_debito) - v_total_saldo
                    );

                end IF;



             --total creditos
                  select  Sum(Coalesce(mo.monto_total,0) )
                  into v_total_credito
                  from obingresos.tmovimiento_entidad mo
                  where mo.tipo = 'credito' and
                    mo.id_agencia = v_record_1.id_agencia AND
                        mo.estado_reg = 'activo' and
                        mo.id_periodo_venta=v_record_2.id_periodo_venta and
                        mo.garantia = 'no' --and
                        --mo.cierre_periodo = 'no'
                  group by mo.id_periodo_venta
                  order by mo.id_periodo_venta asc;

    		--total debitos
    			Select  Sum(Coalesce(mo.monto,0))
                into v_total_debito
                from obingresos.tmovimiento_entidad mo
                left join obingresos.tperiodo_venta pv on pv.id_periodo_venta=mo.id_periodo_venta
                where mo.tipo = 'debito' and
                    mo.id_agencia = v_record_1.id_agencia AND
                        mo.estado_reg = 'activo' and
                        mo.id_periodo_venta=v_record_2.id_periodo_venta and
                        mo.garantia = 'no' and
                        mo.cierre_periodo = 'no'
                group by mo.id_periodo_venta
                order by mo.id_periodo_venta asc;

                IF(v_total_credito is null)then
                  	v_total_credito=0;
                  end if;

                IF(v_total_debito is null)then
                  v_total_debito=0;
                end if;


    			v_id_periodo_venta=v_record_2.id_periodo_venta;

 	end loop;

    --total saldo
                select  Sum(COALESCE(mo.monto_total,0))
                into v_total_saldo
                  from obingresos.tmovimiento_entidad mo
                  where mo.tipo = 'credito' and
                    mo.id_agencia = v_record_1.id_agencia AND
                        mo.estado_reg = 'activo' and
                        mo.id_periodo_venta is null and
                        mo.garantia = 'no' and
                        mo.cierre_periodo = 'si'
                  group by mo.id_periodo_venta
                  order by mo.id_periodo_venta asc;

                  IF(v_total_saldo is null)then
                  	v_total_saldo=0;
                  end if;

                 --raise notice  'v_total_credito:%,v_total_debito:%,v_total_saldo%',v_total_credito,v_total_debito,v_total_saldo;

                IF( v_total_credito -  v_total_debito)<> v_total_saldo then

                raise notice 'EL ID agencia es:%',v_record_1.id_agencia;
                raise notice 'Id_periodo:%',v_record_2.id_periodo_venta;
                raise notice 'v_total_credito:%,v_total_debito:%,v_total_saldo:%',v_total_credito,v_total_debito,v_total_saldo;


                	INSERT INTO
                      obingresos.t_diferencias_saldos
                    (
                      id_agencia,
                      id_periodo_venta,
                      total_credito,
                      total_debito,
                      saldo_arrastrado,
                      saldo_calculado,
                      diferencia_saldos
                    )
                    VALUES (

                      v_record_1.id_agencia,
                      v_id_periodo_venta,
                      v_total_credito,
                      v_total_debito,
                      v_total_saldo,
                      v_total_credito-v_total_debito,
                      (v_total_credito-v_total_debito) - v_total_saldo
                    );

                end IF;

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