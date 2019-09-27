CREATE OR REPLACE FUNCTION obingresos.ft_activar_contrato_agencias_ime (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Ingresos
 FUNCION: 		obingresos.ft_activar_contrato_agencias_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'obingresos.tforma_pago'
 AUTOR: 		 (ivaldivia)
 FECHA:	        20-09-2019 9:00:00
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:

 DESCRIPCION: SERVICIO PARA ACTUALIZAR EL CONTRATO
 AUTOR:
 FECHA:
***************************************************************************/

DECLARE

	v_parametros           	record;
	v_resp		            varchar;
	v_nombre_funcion        text;
	v_mensaje_error         text;
	v_record				record;
	v_respuesta 			varchar;
    v_datos_movimiento		record;
    v_estado_contrato		varchar;

    v_creditos				numeric;
    v_debitos				numeric;
    v_diferencia			numeric;
BEGIN

    v_nombre_funcion = 'obingresos.ft_activar_contrato_agencias_ime';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'OBING_AC_CONT_MOD'
 	#DESCRIPCION:	Modificación de registros
 	#AUTOR:		ivaldivia
 	#FECHA:		08-03-2019 16:05:00
	***********************************/

	if(p_transaccion='OBING_AC_CONT_MOD')then

        begin
			for v_record in  (select ag.id_agencia
                      from obingresos.tagencia ag
                      where ag.id_agencia in (v_parametros.id_agencia))loop


                      if exists (select 1
                                  from obingresos.tagencia ag
                                  where ag.id_agencia = v_record.id_agencia)then

                            if exists (    	select 1
			          					    from leg.tcontrato c
                                    		where c.id_agencia = v_record.id_agencia)then

                            /*******************Poniendo control para verificar si se hara el UPDATE***************************/

                            				select c.estado into v_estado_contrato
                                            from leg.tcontrato c
                                            where c.id_agencia = v_record.id_agencia and c.fecha_fin >= now()::date;

                               if (	v_estado_contrato = 'finalizado' ) then
                               			raise exception 'El contrato para la agencia ya esta en estado finalizado';

                               elsif (	v_estado_contrato is NULL ) then
                            		   raise exception 'No se encontro un contrato vigente para realizar la actualizacion';
                               else
                                                      update leg.tcontrato set
                                                      estado = 'finalizado'
                                                      where id_agencia = v_record.id_agencia and fecha_fin >= now()::date;


                                /****************************Recuperamos datos del ultimo movimiento******************************************/
                                /*****************************Para arrastrar su saldo como credito**************************************/
                                                select mov.* into v_datos_movimiento
                                                from obingresos.tmovimiento_entidad mov
                                                where mov.cierre_periodo = 'si' and mov.id_agencia = v_record.id_agencia
                                                order by mov.id_periodo_venta desc limit 1;
                                /*************************************************************************************/

                                /*****************************RECUPERAMOS LA DIFERENCIA DE SALDOS PARA INGRESAR AL CREDITO*********************/
                                /**************************************************************************************************************/

                                        select Sum(mo.monto_total) into v_creditos
                                        from obingresos.tmovimiento_entidad mo
                                        LEFT JOIN obingresos.tperiodo_venta pe ON pe.id_periodo_venta = mo.id_periodo_venta
                                        where mo.tipo = 'credito' and
                                        mo.id_agencia = v_record.id_agencia AND
                                        mo.estado_reg = 'activo' and
                                        mo.garantia = 'no' and
                                        mo.id_periodo_venta is not null
                                        and mo.id_periodo_venta = v_datos_movimiento.id_periodo_venta
                                        group by mo.id_periodo_venta,mo.tipo, pe.fecha_ini, pe.fecha_fin, pe.mes
                                        order by mo.id_periodo_venta asc;

                                		if (v_creditos is null) then
                                        	v_creditos = 0;
                                        end if;

                                        Select
                                        sum(mo.monto) into v_debitos
                                        from obingresos.tmovimiento_entidad mo
                                        left join obingresos.tperiodo_venta pv on pv.id_periodo_venta=mo.id_periodo_venta
                                        where mo.tipo = 'debito' and
                                        mo.id_agencia = v_record.id_agencia AND
                                        mo.estado_reg = 'activo' and
                                        mo.garantia = 'no' and
                                        mo.cierre_periodo = 'no' and
                                        mo.id_periodo_venta is not null
                                        and mo.id_periodo_venta =v_datos_movimiento.id_periodo_venta
                                        group by mo.id_periodo_venta,pv.fecha_ini, pv.fecha_fin,mo.tipo
                                        order by mo.id_periodo_venta asc;

                                        if (v_debitos is null) then
                                        	v_debitos = 0;
                                        end if;



                                		v_diferencia = v_creditos - v_debitos;


                                 /***************************Realizamos la inserccion recuperando los datos como debito*******************************/

                                                insert into obingresos.tmovimiento_entidad(
                                                      id_moneda,
                                                      id_periodo_venta,
                                                      id_agencia,
                                                      garantia,
                                                      monto_total,
                                                      tipo,
                                                      autorizacion__nro_deposito,
                                                      estado_reg,
                                                      monto,
                                                      ajuste,
                                                      fecha,
                                                      pnr,
                                                      apellido,
                                                      id_usuario_reg,
                                                      fecha_reg,
                                                      fecha_mod,
                                                      id_usuario_mod,
                                                      cierre_periodo,
                                                      fk_id_movimiento_entidad
                                                      ) values(
                                                      v_datos_movimiento.id_moneda,
                                                      v_datos_movimiento.id_periodo_venta,
                                                      v_datos_movimiento.id_agencia,
                                                      v_datos_movimiento.garantia,
                                                      v_datos_movimiento.monto,
                                                      'debito',
                                                      v_datos_movimiento.autorizacion__nro_deposito,
                                                      'activo',
                                                      v_datos_movimiento.monto,
                                                      v_datos_movimiento.ajuste,
                                                      now()::date,
                                                      v_datos_movimiento.pnr,
                                                      v_datos_movimiento.apellido,
                                                      p_id_usuario,
                                                      now(),
                                                      now(),
                                                      p_id_usuario,
                                                      v_datos_movimiento.cierre_periodo,
                                                      v_datos_movimiento.fk_id_movimiento_entidad
                                                      );

                                 /***************************Realizamos la inserccion recuperando los datos como credito periodo vigente*******************************/
                                               insert into obingresos.tmovimiento_entidad(
                                                      id_moneda,
                                                      id_periodo_venta,
                                                      id_agencia,
                                                      garantia,
                                                      monto_total,
                                                      tipo,
                                                      autorizacion__nro_deposito,
                                                      estado_reg,
                                                      monto,
                                                      ajuste,
                                                      fecha,
                                                      pnr,
                                                      apellido,
                                                      id_usuario_reg,
                                                      fecha_reg,
                                                      fecha_mod,
                                                      id_usuario_mod,
                                                      cierre_periodo,
                                                      fk_id_movimiento_entidad
                                                      ) values(
                                                      v_datos_movimiento.id_moneda,
                                                      NULL,
                                                      v_datos_movimiento.id_agencia,
                                                      v_datos_movimiento.garantia,
                                                      v_diferencia,
                                                      'credito',
                                                      v_datos_movimiento.autorizacion__nro_deposito,
                                                      'activo',
                                                      v_diferencia,
                                                      v_datos_movimiento.ajuste,
                                                      now()::date,
                                                      v_datos_movimiento.pnr,
                                                      v_datos_movimiento.apellido,
                                                      p_id_usuario,
                                                      now(),
                                                      now(),
                                                      p_id_usuario,
                                                      v_datos_movimiento.cierre_periodo,
                                                      v_datos_movimiento.fk_id_movimiento_entidad
                                                      );

                                                      v_respuesta = 'Actualización y Arrastre de saldo correctamente verifique';


                                end if;
                            /*************************************************************************************************/

                            else
                                raise exception 'No existe un contrato para la Agencia %',v_record.id_agencia;
                            end if;
                      else
                      	raise exception 'No existe la Agencia %',v_record.id_agencia;
                      end if ;

			end loop;

             --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','La Actualización se registro exitosamente');
            v_resp = pxp.f_agrega_clave(v_resp,'tipo_mensaje',v_respuesta);
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

ALTER FUNCTION obingresos.ft_activar_contrato_agencias_ime (p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
  OWNER TO postgres;
