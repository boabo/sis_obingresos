CREATE OR REPLACE FUNCTION obingresos.ft_control_agencia_ime (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Sistema de Ingresos
 FUNCION: 		obingresos.ft_control_agencia_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'obingresos.tmovimiento_entidad'
 AUTOR: 		 (ivaldivia)
 FECHA:	        23-10-2019 10:45:00
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:

 DESCRIPCION:
 AUTOR:
 FECHA:
***************************************************************************/

DECLARE

	v_nro_requerimiento    	integer;
	v_parametros           	record;
	v_id_requerimiento     	integer;
	v_resp		            varchar;
	v_nombre_funcion        text;
	v_mensaje_error         text;
    v_res					varchar;

    v_saldos_mas_creditos	record;
    v_debitos				record;
    v_saldo_calculado		record;
    v_saldo_obtenido		numeric;

    v_depositos				record;
    v_id_periodo_venta_maximo	integer;
	v_ultimo_periodo		record;
    v_debitos_vigente		record;
    v_saldo_obtenido_vigente	numeric;
  BEGIN

      v_nombre_funcion = 'obingresos.ft_control_agencia_ime';
      v_parametros = pxp.f_get_record(p_tabla);

      /*********************************
      #TRANSACCION:  'OBING_MODSAL_MOD'
      #DESCRIPCION:	Modificacion de registros
      #AUTOR:		ivaldivia
      #FECHA:		23-10-2019 10:45:00
      ***********************************/

      if(p_transaccion='OBING_MODSAL_MOD')then

          begin
              --Sentencia de la modificacion


              /*Actualizamos para los creditos*/

              /*Verificamos si el periodo a actualizar es el periodo vigente*/
              if (v_parametros.id_periodo_venta is null) then

              select mov.id_periodo_venta into v_id_periodo_venta_maximo
              from obingresos.tmovimiento_entidad mov
              where mov.estado_reg = 'activo' and mov.cierre_periodo = 'si'
              and mov.tipo = 'debito' and mov.monto is not NULL
              and mov.id_agencia = v_parametros.id_agencia
              order by mov.id_periodo_venta DESC
              limit 1;


                        update obingresos.tmovimiento_entidad set
                        monto = v_parametros.saldo_arrastrado,
                        monto_total = v_parametros.saldo_arrastrado,
                        fecha_mod = now(),
                        id_usuario_mod = p_id_usuario
                        where id_periodo_venta is null and tipo = 'credito'
                        and id_agencia = v_parametros.id_agencia and cierre_periodo = 'si' and estado_reg = 'activo';

                        update obingresos.tmovimiento_entidad set
                        monto = v_parametros.saldo_arrastrado,
                        monto_total = v_parametros.saldo_arrastrado,
                        fecha_mod = now(),
                        id_usuario_mod = p_id_usuario
                        where id_periodo_venta=(select me.id_periodo_venta
                                                from obingresos.tmovimiento_entidad me
                                                where me.estado_reg = 'activo' and me.cierre_periodo = 'si'
                                                and me.tipo = 'debito' and me.monto is not NULL and me.id_periodo_venta = v_id_periodo_venta_maximo
                                                and me.id_agencia = v_parametros.id_agencia
                                                order by me.id_periodo_venta DESC
                                                limit 1)
                        and tipo = 'debito'
                        and id_agencia = v_parametros.id_agencia and cierre_periodo = 'si' and estado_reg = 'activo';

              else

              /**************************************************************/
                      update obingresos.tmovimiento_entidad set
                      monto = v_parametros.saldo_arrastrado,
                      monto_total = v_parametros.saldo_arrastrado,
                      fecha_mod = now(),
                      id_usuario_mod = p_id_usuario
                      where id_periodo_venta=v_parametros.id_periodo_venta and tipo = 'credito'
                      and id_agencia = v_parametros.id_agencia and cierre_periodo = 'si' and estado_reg = 'activo';

                      update obingresos.tmovimiento_entidad set
                      monto = v_parametros.saldo_arrastrado,
                      monto_total = v_parametros.saldo_arrastrado,
                      fecha_mod = now(),
                      id_usuario_mod = p_id_usuario
                      where id_periodo_venta=(select me.id_periodo_venta
                                              from obingresos.tmovimiento_entidad me
                                              where me.estado_reg = 'activo' and me.cierre_periodo = 'si'
                                              and me.tipo = 'debito' and me.monto is not NULL and me.id_periodo_venta < v_parametros.id_periodo_venta
                                              and me.id_agencia = v_parametros.id_agencia
                                              order by me.id_periodo_venta DESC
                                              limit 1)

                      and tipo = 'debito'
                      and id_agencia = v_parametros.id_agencia and cierre_periodo = 'si' and estado_reg = 'activo';
              end if;

              /*Arrastrar el saldo hasta el periodo vigente*/

              for v_saldos_mas_creditos in (
                  (select  	mo.id_periodo_venta,
                            Sum(mo.monto_total) as saldos,
                            mo.tipo,
                            COALESCE(TO_CHAR(pe.fecha_ini,'DD')||' al '|| TO_CHAR(pe.fecha_fin,'DD')||' '||pe.mes||' '||EXTRACT(YEAR FROM pe.fecha_ini),'Periodo Vigente')::text as periodo
                            from obingresos.tmovimiento_entidad mo
                            LEFT JOIN obingresos.tperiodo_venta pe ON pe.id_periodo_venta = mo.id_periodo_venta
                    where mo.tipo = 'credito' and
                    mo.id_agencia = v_parametros.id_agencia AND
                    mo.estado_reg = 'activo' and
                    mo.garantia = 'no' and
                    mo.id_periodo_venta is not null
                    and mo.id_periodo_venta >= v_parametros.id_periodo_venta
                    group by mo.id_periodo_venta,mo.tipo, pe.fecha_ini, pe.fecha_fin, pe.mes
                    order by mo.id_periodo_venta asc)

                   Union

                  (select  	mo.id_periodo_venta,
                              Sum(mo.monto_total) as saldos,
                              mo.tipo,
                              COALESCE(TO_CHAR(pe.fecha_ini,'DD')||' al '|| TO_CHAR(pe.fecha_fin,'DD')||' '||pe.mes||' '||EXTRACT(YEAR FROM pe.fecha_ini),'Periodo Vigente')::text as periodo
                              from obingresos.tmovimiento_entidad mo
                              LEFT JOIN obingresos.tperiodo_venta pe ON pe.id_periodo_venta = mo.id_periodo_venta
                  where mo.tipo = 'credito' and
                  mo.id_agencia = v_parametros.id_agencia AND
                  mo.estado_reg = 'activo' and
                  mo.garantia = 'no' and
                  mo.id_periodo_venta is  null
                  group by mo.id_periodo_venta,mo.tipo, pe.fecha_ini, pe.fecha_fin, pe.mes
                  order by mo.id_periodo_venta asc)

                  ORDER BY id_periodo_venta ASC

              ) LOOP

              		select  mo.id_periodo_venta,
                            Sum(mo.monto_total) as saldos,
                            mo.tipo,
                            COALESCE(TO_CHAR(pe.fecha_ini,'DD')||' al '|| TO_CHAR(pe.fecha_fin,'DD')||' '||pe.mes||' '||EXTRACT(YEAR FROM pe.fecha_ini),'Periodo Vigente')::text as periodo
                            into v_depositos
                            from obingresos.tmovimiento_entidad mo
                            LEFT JOIN obingresos.tperiodo_venta pe ON pe.id_periodo_venta = mo.id_periodo_venta
                    where mo.tipo = 'credito' and
                    mo.id_agencia = v_parametros.id_agencia AND
                    mo.estado_reg = 'activo' and
                    mo.garantia = 'no'
                    --mo.id_periodo_venta is not null
                    and mo.id_periodo_venta = v_saldos_mas_creditos.id_periodo_venta
                    group by mo.id_periodo_venta,mo.tipo, pe.fecha_ini, pe.fecha_fin, pe.mes
                    order by mo.id_periodo_venta asc;


              	     Select
                            mo.id_periodo_venta,
                            pv.fecha_ini,
                            pv.fecha_fin,
                            sum(mo.monto) as debitos,
                            mo.tipo into v_debitos
                      from obingresos.tmovimiento_entidad mo
                      left join obingresos.tperiodo_venta pv on pv.id_periodo_venta=mo.id_periodo_venta
                      where mo.tipo = 'debito' and
                      mo.id_agencia = v_parametros.id_agencia AND
                      mo.estado_reg = 'activo' and
                      mo.garantia = 'no' and
                      mo.cierre_periodo = 'no' and
                      mo.id_periodo_venta = v_saldos_mas_creditos.id_periodo_venta
                      group by mo.id_periodo_venta,pv.fecha_ini, pv.fecha_fin,mo.tipo
                      order by mo.id_periodo_venta asc;


                      v_saldo_obtenido = (Coalesce (v_depositos.saldos,0) - COALESCE (v_debitos.debitos,0));



                      if (v_saldos_mas_creditos.id_periodo_venta is null) then

                          select mo.id_periodo_venta,
                                 Sum(mo.monto_total) as saldos into v_ultimo_periodo
                          from obingresos.tmovimiento_entidad mo
                          LEFT JOIN obingresos.tperiodo_venta pe ON pe.id_periodo_venta = mo.id_periodo_venta
                          where mo.tipo = 'credito' and
                          mo.id_agencia = v_parametros.id_agencia AND
                          mo.estado_reg = 'activo' and
                          mo.garantia = 'no' and
                          mo.id_periodo_venta is not null
                          and mo.id_periodo_venta >= v_parametros.id_periodo_venta
                          group by mo.id_periodo_venta,mo.tipo, pe.fecha_ini, pe.fecha_fin, pe.mes
                          order by mo.id_periodo_venta DESC
                          limit 1;


                          Select
                                mo.id_periodo_venta,
                                pv.fecha_ini,
                                pv.fecha_fin,
                                sum(mo.monto) as debitos,
                                mo.tipo into v_debitos_vigente
                          from obingresos.tmovimiento_entidad mo
                          left join obingresos.tperiodo_venta pv on pv.id_periodo_venta=mo.id_periodo_venta
                          where mo.tipo = 'debito' and
                          mo.id_agencia = v_parametros.id_agencia AND
                          mo.estado_reg = 'activo' and
                          mo.garantia = 'no' and
                          mo.cierre_periodo = 'no' and
                          mo.id_periodo_venta = v_ultimo_periodo.id_periodo_venta
                          group by mo.id_periodo_venta,pv.fecha_ini, pv.fecha_fin,mo.tipo
                          order by mo.id_periodo_venta asc;


                          v_saldo_obtenido_vigente = (Coalesce (v_ultimo_periodo.saldos,0) - COALESCE (v_debitos_vigente.debitos,0));


                          update obingresos.tmovimiento_entidad set
                          monto = COALESCE (v_saldo_obtenido_vigente,0),
                          monto_total = COALESCE (v_saldo_obtenido_vigente,0),
                          fecha_mod = now(),
                          id_usuario_mod = p_id_usuario
                          where id_periodo_venta is null and tipo = 'credito'
                          and id_agencia = v_parametros.id_agencia and cierre_periodo = 'si' and estado_reg = 'activo';

                          update obingresos.tmovimiento_entidad set
                          monto = COALESCE (v_saldo_obtenido,0),
                          monto_total = COALESCE (v_saldo_obtenido,0),
                          fecha_mod = now(),
                          id_usuario_mod = p_id_usuario
                          where id_periodo_venta = v_ultimo_periodo.id_periodo_venta
                          and tipo = 'debito'
                          and id_agencia = v_parametros.id_agencia and cierre_periodo = 'si' and estado_reg = 'activo';





                      else

                      /*Actualizamos para los creditos*/
                      update obingresos.tmovimiento_entidad set
                      monto = COALESCE (v_saldo_obtenido,0),
                      monto_total = COALESCE (v_saldo_obtenido,0),
                      fecha_mod = now(),
                      id_usuario_mod = p_id_usuario
                      where id_periodo_venta=(v_saldos_mas_creditos.id_periodo_venta+1) and tipo = 'credito'
                      and id_agencia = v_parametros.id_agencia and cierre_periodo = 'si' and estado_reg = 'activo';

                      update obingresos.tmovimiento_entidad set
                      monto = COALESCE (v_saldo_obtenido,0),
                      monto_total = COALESCE (v_saldo_obtenido,0),
                      fecha_mod = now(),
                      id_usuario_mod = p_id_usuario
                      where id_periodo_venta=(select me.id_periodo_venta
                                              from obingresos.tmovimiento_entidad me
                                              where me.estado_reg = 'activo' and me.cierre_periodo = 'si'
                                              and me.tipo = 'debito' and me.monto <> 0 and me.id_periodo_venta <= v_saldos_mas_creditos.id_periodo_venta
                                              and me.id_agencia = v_parametros.id_agencia
                                              order by me.id_periodo_venta DESC
                                              limit 1)

                      and tipo = 'debito'
                      and id_agencia = v_parametros.id_agencia and cierre_periodo = 'si' and estado_reg = 'activo';


                      end if;


              END LOOP;



              --raise exception 'llega aqui el saldo:%.',v_saldo_obtenido;
              /*********************************************/



              --Definicion de la respuesta
              v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Movimiento Entidad modificado(a)');
              --v_resp = pxp.f_agrega_clave(v_resp,'id_venta_forma_pago',v_parametros.id_venta_forma_pago::varchar);

              --Devuelve la respuesta
              return v_resp;

          end;

      else

          raise exception 'Transaccion inexistente: %',p_transaccion;

      end if;

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

ALTER FUNCTION obingresos.ft_control_agencia_ime (p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
  OWNER TO postgres;
