CREATE OR REPLACE FUNCTION obingresos.ft_boletos_observados_ime (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Ingresos
 FUNCION: 		obingresos.ft_boletos_observados_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'obingresos.tboletos_observados'
 AUTOR: 		 (admin)
 FECHA:	        04-06-2019 19:39:16
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				04-06-2019 19:39:16								Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'obingresos.tboletos_observados'
 #
 ***************************************************************************/

DECLARE

	v_nro_requerimiento    	integer;
	v_parametros           	record;
	v_id_requerimiento     	integer;
	v_resp		            varchar;
	v_nombre_funcion        text;
	v_mensaje_error         text;
	v_id_boletos_observados	integer;

BEGIN

    v_nombre_funcion = 'obingresos.ft_boletos_observados_ime';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'OBING_BOBS_INS'
 	#DESCRIPCION:	Insercion de registros
 	#AUTOR:		admin
 	#FECHA:		04-06-2019 19:39:16
	***********************************/

	if(p_transaccion='OBING_BOBS_INS')then

        begin
        	--Sentencia de la insercion
        	insert into obingresos.tboletos_observados(
			estado_reg,
			pnr,
			nro_autorizacion,
			moneda,
			importe_total,
			fecha_emision,
			estado_p,
			forma_pago,
			medio_pago,
			instancia_pago,
			office_id_emisor,
			pnr_prov,
			nro_autorizacion_prov,
			office_id_emisor_prov,
			importe_prov,
			moneda_prov,
			estado_prov,
			fecha_autorizacion_prov,
			tipo_error,
			tipo_validacion,
			prov_informacion,
			--id_instancia_pago,
			id_usuario_reg,
			fecha_reg,
			id_usuario_ai,
			usuario_ai,
			id_usuario_mod,
			fecha_mod
          	) values(
			'activo',
			v_parametros.pnr,
			v_parametros.nro_autorizacion,
			v_parametros.moneda,
			v_parametros.importe_total,
			v_parametros.fecha_emision,
			v_parametros.estado_p,
			v_parametros.forma_pago,
			v_parametros.medio_pago,
			v_parametros.instancia_pago,
			v_parametros.office_id_emisor,
			v_parametros.pnr_prov,
			v_parametros.nro_autorizacion_prov,
			v_parametros.office_id_emisor_prov,
			v_parametros.importe_prov,
			v_parametros.moneda_prov,
			v_parametros.estado_prov,
			v_parametros.fecha_autorizacion_prov,
			v_parametros.tipo_error,
			v_parametros.tipo_validacion,
			v_parametros.prov_informacion,
			--v_parametros.id_instancia_pago,
			p_id_usuario,
			now(),
			v_parametros._id_usuario_ai,
			v_parametros._nombre_usuario_ai,
			null,
			null



			)RETURNING id_boletos_observados into v_id_boletos_observados;

			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Boletos Observados almacenado(a) con exito (id_boletos_observados'||v_id_boletos_observados||')');
            v_resp = pxp.f_agrega_clave(v_resp,'id_boletos_observados',v_id_boletos_observados::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'OBING_BOBS_MOD'
 	#DESCRIPCION:	Modificacion de registros
 	#AUTOR:		admin
 	#FECHA:		04-06-2019 19:39:16
	***********************************/

	elsif(p_transaccion='OBING_BOBS_MOD')then

		begin
			--Sentencia de la modificacion
			update obingresos.tboletos_observados set
			pnr = v_parametros.pnr,
			nro_autorizacion = v_parametros.nro_autorizacion,
			moneda = v_parametros.moneda,
			importe_total = v_parametros.importe_total,
			fecha_emision = v_parametros.fecha_emision,
			estado_p = v_parametros.estado_p,
			forma_pago = v_parametros.forma_pago,
			medio_pago = v_parametros.medio_pago,
			instancia_pago = v_parametros.instancia_pago,
			office_id_emisor = v_parametros.office_id_emisor,
			pnr_prov = v_parametros.pnr_prov,
			nro_autorizacion_prov = v_parametros.nro_autorizacion_prov,
			office_id_emisor_prov = v_parametros.office_id_emisor_prov,
			importe_prov = v_parametros.importe_prov,
			moneda_prov = v_parametros.moneda_prov,
			estado_prov = v_parametros.estado_prov,
			fecha_autorizacion_prov = v_parametros.fecha_autorizacion_prov,
			tipo_error = v_parametros.tipo_error,
			tipo_validacion = v_parametros.tipo_validacion,
			prov_informacion = v_parametros.prov_informacion,
			--id_instancia_pago = v_parametros.id_instancia_pago,
			id_usuario_mod = p_id_usuario,
			fecha_mod = now(),
			id_usuario_ai = v_parametros._id_usuario_ai,
			usuario_ai = v_parametros._nombre_usuario_ai
			where id_boletos_observados=v_parametros.id_boletos_observados;

			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Boletos Observados modificado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_boletos_observados',v_parametros.id_boletos_observados::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'OBING_BOBS_ELI'
 	#DESCRIPCION:	Eliminacion de registros
 	#AUTOR:		admin
 	#FECHA:		04-06-2019 19:39:16
	***********************************/

	elsif(p_transaccion='OBING_BOBS_ELI')then

		begin
			--Sentencia de la eliminacion
			delete from obingresos.tboletos_observados
            where id_boletos_observados=v_parametros.id_boletos_observados;

            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Boletos Observados eliminado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_boletos_observados',v_parametros.id_boletos_observados::varchar);

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

ALTER FUNCTION obingresos.ft_boletos_observados_ime (p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
  OWNER TO postgres;
