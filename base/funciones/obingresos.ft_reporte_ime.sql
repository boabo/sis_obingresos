CREATE OR REPLACE FUNCTION obingresos.ft_reporte_ime (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Ingresos
 FUNCION: 		obingresos.ft_reporte_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'obingresos.ft_reporte_ime'
 AUTOR: 		 (franklin.espinoza)
 FECHA:	        31-05-2021
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:

 DESCRIPCION:
 AUTOR:
 FECHA:
***************************************************************************/

DECLARE

	v_parametros           	record;
	v_resp		            varchar;
	v_nombre_funcion        text;
	v_mensaje_error         text;


    v_moneda				varchar;
    v_agencia				varchar;
    v_usuario				varchar;
   	v_id_movimiento_entidad	integer;
    v_id_alarma				integer;
    v_id_moneda				integer;
    v_cod_moneda			varchar;
    v_id_agencia			integer;

    v_record_json			jsonb;
    v_estado_generado		varchar = 'pendiente';

    v_lista_acm					text = '';
    v_id_calculo_over_comison	integer;
    v_lista_movimiento_entidad 	text = '';

    v_validacion_inicio			varchar='';

BEGIN

    v_nombre_funcion = 'obingresos.ft_reporte_ime';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'OBING_CRED_NO_IATA'
 	#DESCRIPCION:	Insercion de registros
 	#AUTOR:		franklin.espinoza
 	#FECHA:		31-05-2021
	***********************************/

	if(p_transaccion='OBING_CRED_NO_IATA')then

		begin
        	--Sentencia de inserccion

			if v_parametros.fecha_desde >= '01/06/2021'::date and v_parametros.fecha_hasta >= '01/06/2021'::date then
              --raise 'entra';
              select coalesce(tco.calculo_generado,'pendiente') estado
              into v_estado_generado
              from obingresos.tcalculo_over_comison tco
              where tco.fecha_ini_calculo = v_parametros.fecha_desde and tco.fecha_fin_calculo = v_parametros.fecha_hasta and tco.tipo = v_parametros.tipo;
              --raise 'v_estado_generado: %,v_parametros.fecha_desde: %, v_parametros.fecha_hasta: %,  v_parametros.tipo: %,  v_parametros.dataJson: %',v_estado_generado, v_parametros.fecha_desde, v_parametros.fecha_hasta, v_parametros.tipo, v_parametros.dataJson;

              if v_estado_generado is null then
                  v_estado_generado = 'pendiente';
              end if;

              if v_estado_generado = 'pendiente' then

                --raise 'v_record_json: [%]', v_parametros.dataJson;

                insert into obingresos.tcalculo_over_comison(
                  tipo,
                  calculo_generado,
                  fecha_ini_calculo,
                  fecha_fin_calculo,
                  documento
                ) values(
                  v_parametros.tipo,
                  'generado',
                  v_parametros.fecha_desde,
                  v_parametros.fecha_hasta,
                  'ACM'
                )RETURNING id_calculo_over_comison into v_id_calculo_over_comison;

                if v_parametros.tipo = 'NO-IATA' then

                  for v_record_json in SELECT * FROM jsonb_array_elements(v_parametros.dataJson) loop

                      /*ID moneda y codigo internacional*/
                      select mon.id_moneda into v_id_moneda
                      from param.tmoneda mon
                      where mon.codigo_internacional = v_record_json->>'Currency';

                      v_cod_moneda = v_record_json->>'Currency';
                      /**************************************************/

                      /*Id Agencia y nombre de la agencia*/
                      select ag.id_agencia,
                             ag.nombre
                              into
                              v_id_agencia,
                              v_agencia
                      from obingresos.tagencia ag
                      where ag.codigo_int = v_record_json->>'OfficeId';

                      if (v_id_agencia is null) then
                          raise exception 'No se ecuentra la agencia para el officeId: %',v_record_json->>'OfficeId';
                      end if;


                      select u.cuenta into v_usuario
                      from segu.tusuario u
                      where u.id_usuario = 366;

                      --Sentencia de la insercion
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
                        usuario_ai,
                        id_usuario_ai,
                        fecha_mod,
                        id_usuario_mod,
                        fk_id_movimiento_entidad,
                        observaciones,
                        id_origen
                      ) values(
                        v_id_moneda,
                        NULL,
                        v_id_agencia,
                        'no',
                        (v_record_json->>'CommssionAmount')::numeric,
                        'credito',
                        (v_record_json->>'CommissionDescription')::varchar ||' - Nro ACM: '|| (v_record_json->>'DocumentNumber')::varchar,
                        'activo',
                        (v_record_json->>'CommssionAmount')::numeric,
                        'no',
                        (v_record_json->>'From')::date,
                        NULL,
                        NULL,
                        p_id_usuario,
                        now(),
                        NULL,
                        null,
                        null,
                        null,
                        null,
                        'CALCULO_OVER_COMISON',
                        (v_record_json->>'AcmKey')::integer
                      )RETURNING id_movimiento_entidad into v_id_movimiento_entidad;


                    v_lista_acm = v_lista_acm || (v_record_json->>'DocumentNumber')::text || ',';
                    v_lista_movimiento_entidad =  v_lista_movimiento_entidad || v_id_movimiento_entidad || ',';

                    /*if (exists(select 1 from pxp.variable_global va where va.variable = 'obingresos_notidep'))  then
                        v_id_alarma = (select param.f_inserta_alarma_dblink (1,'Nuevo '|| v_registros.tipo || 'registrado para la agencia ' || v_agencia,'El usuario ' || v_usuario || ' ha registrado un ajuste de tipo '
                                        || v_registros.tipo || ' para la agencia ' || v_agencia || ' por un monto de ' || v_registros.monto || ' ' || v_moneda || ' . Ingrese al ERP para verificarlo',
                                            pxp.f_get_variable_global('obingresos_notidep')));
                    end if;*/

                  end loop;

                  update obingresos.tcalculo_over_comison set
                      lista_acm = trim(v_lista_acm,','),
                      lista_movimiento_entidad = trim(v_lista_movimiento_entidad,',')
                  where id_calculo_over_comison = v_id_calculo_over_comison;
                end if;
              end if;
              v_validacion_inicio = 'activo';
            else
            	v_validacion_inicio = 'inactivo';
            end if;
            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Creditos Insertados Correctamente');
            v_resp = pxp.f_agrega_clave(v_resp,'lista_acm',v_lista_acm::varchar);
            v_resp = pxp.f_agrega_clave(v_resp,'lista_movimiento_entidad',v_lista_movimiento_entidad::varchar);
            v_resp = pxp.f_agrega_clave(v_resp,'validacion_inicio',v_validacion_inicio::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

    /*********************************
 	#TRANSACCION:  'OBING_VER_PER_GENERA'
 	#DESCRIPCION:	Insercion de registros
 	#AUTOR		:	franklin.espinoza
 	#FECHA		:	31-05-2021
	***********************************/

	elsif(p_transaccion='OBING_VER_PER_GENERA')then

		begin

        	select coalesce(tco.calculo_generado,'pendiente')
            into v_estado_generado
            from obingresos.tcalculo_over_comison tco
            where tco.fecha_ini_calculo = v_parametros.fecha_ini and tco.fecha_fin_calculo = v_parametros.fecha_fin and tco.tipo = v_parametros.tipo;

            if v_estado_generado is null then
            	v_estado_generado = 'pendiente';
            end if;

            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Consulta Periodo Generado');
            v_resp = pxp.f_agrega_clave(v_resp,'estado_generado',v_estado_generado::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;
    /*********************************
 	#TRANSACCION:  'OBING_REVERTIR_ABONO'
 	#DESCRIPCION:	Eliminar registros Movimiento Entidad
 	#AUTOR		:	franklin.espinoza
 	#FECHA		:	02-08-2021
	***********************************/

	elsif(p_transaccion='OBING_REVERTIR_ABONO')then

		begin

			--raise 'AcmKey: %, DocumentNumber :%', v_parametros.AcmKey,  v_parametros.DocumentNumber;
        	/*delete from  obingresos.tmovimiento_entidad
  			where observaciones = 'CALCULO_OVER_COMISON' and id_usuario_reg = 612 and id_origen = v_parametros.AcmKey::integer;*/
            update obingresos.tmovimiento_entidad set
            	estado_reg = 'inactivo'
            where observaciones = 'CALCULO_OVER_COMISON' and id_origen = v_parametros.AcmKey::integer;

            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Acm Key Inactivado Exitosamente');
            v_resp = pxp.f_agrega_clave(v_resp,'AcmKey',v_parametros.AcmKey::varchar);

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

ALTER FUNCTION obingresos.ft_reporte_ime (p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
  OWNER TO postgres;