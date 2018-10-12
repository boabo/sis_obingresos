CREATE OR REPLACE FUNCTION obingresos.ft_acm_det_ime (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Ingresos
 FUNCION: 		obingresos.ft_acm_det_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'obingresos.tacm_det'
 AUTOR: 		 (ivaldivia)
 FECHA:	        05-09-2018 20:52:05
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				05-09-2018 20:52:05								Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'obingresos.tacm_det'
 #
 ***************************************************************************/

DECLARE

	v_nro_requerimiento    	integer;
	v_parametros           	record;
	v_id_requerimiento     	integer;
	v_resp		            varchar;
	v_nombre_funcion        text;
	v_mensaje_error         text;
	v_id_acm_det	integer;

BEGIN

    v_nombre_funcion = 'obingresos.ft_acm_det_ime';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'OBING_ACMDET_INS'
 	#DESCRIPCION:	Insercion de registros
 	#AUTOR:		ivaldivia
 	#FECHA:		05-09-2018 20:52:05
	***********************************/

	if(p_transaccion='OBING_ACMDET_INS')then

        begin
        	--Sentencia de la insercion
        	insert into obingresos.tacm_det(
			id_acm,
			id_detalle_boletos_web,
			neto,
			comision,
			estado_reg,
			id_usuario_ai,
			usuario_ai,
			fecha_reg,
			id_usuario_reg,
			id_usuario_mod,
			fecha_mod
          	) values(
			v_parametros.id_acm,
			v_parametros.id_detalle_boletos_web,
			v_parametros.neto,
			v_parametros.comision,
			'activo',
			v_parametros._id_usuario_ai,
			v_parametros._nombre_usuario_ai,
			now(),
			p_id_usuario,
			null,
			null



			)RETURNING id_acm_det into v_id_acm_det;

			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','ACM det almacenado(a) con exito (id_acm_det'||v_id_acm_det||')');
            v_resp = pxp.f_agrega_clave(v_resp,'id_acm_det',v_id_acm_det::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'OBING_ACMDET_MOD'
 	#DESCRIPCION:	Modificacion de registros
 	#AUTOR:		ivaldivia
 	#FECHA:		05-09-2018 20:52:05
	***********************************/

	elsif(p_transaccion='OBING_ACMDET_MOD')then

		begin
			--Sentencia de la modificacion
			update obingresos.tacm_det set
			id_acm = v_parametros.id_acm,
			id_detalle_boletos_web = v_parametros.id_detalle_boletos_web,
			neto = v_parametros.neto,
			comision = v_parametros.comision,
			id_usuario_mod = p_id_usuario,
			fecha_mod = now(),
			id_usuario_ai = v_parametros._id_usuario_ai,
			usuario_ai = v_parametros._nombre_usuario_ai
			where id_acm_det=v_parametros.id_acm_det;

			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','ACM det modificado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_acm_det',v_parametros.id_acm_det::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'OBING_ACMDET_ELI'
 	#DESCRIPCION:	Eliminacion de registros
 	#AUTOR:		ivaldivia
 	#FECHA:		05-09-2018 20:52:05
	***********************************/

	elsif(p_transaccion='OBING_ACMDET_ELI')then

		begin
			--Sentencia de la eliminacion
			delete from obingresos.tacm_det
            where id_acm_det=v_parametros.id_acm_det;

            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','ACM det eliminado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_acm_det',v_parametros.id_acm_det::varchar);

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