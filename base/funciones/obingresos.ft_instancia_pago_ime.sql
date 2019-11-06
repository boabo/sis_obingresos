CREATE OR REPLACE FUNCTION obingresos.ft_instancia_pago_ime (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Ingresos
 FUNCION: 		obingresos.ft_instancia_pago_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'obingresos.tinstancia_pago'
 AUTOR: 		 (admin)
 FECHA:	        04-06-2019 19:31:28
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				04-06-2019 19:31:28								Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'obingresos.tinstancia_pago'
 #
 ***************************************************************************/

DECLARE

	v_nro_requerimiento    	integer;
	v_parametros           	record;
	v_id_requerimiento     	integer;
	v_resp		            varchar;
	v_nombre_funcion        text;
	v_mensaje_error         text;
	v_id_instancia_pago	integer;

BEGIN

    v_nombre_funcion = 'obingresos.ft_instancia_pago_ime';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'OBING_INSP_INS'
 	#DESCRIPCION:	Insercion de registros
 	#AUTOR:		admin
 	#FECHA:		04-06-2019 19:31:28
	***********************************/

	if(p_transaccion='OBING_INSP_INS')then

        begin
        	--Sentencia de la insercion
        	insert into obingresos.tinstancia_pago(
			estado_reg,
			id_medio_pago,
            instancia_pago_id,
			nombre,
			codigo,
			codigo_forma_pago,
			codigo_medio_pago,
			id_usuario_reg,
			fecha_reg,
			id_usuario_ai,
			usuario_ai,
			id_usuario_mod,
			fecha_mod,
            fp_code,
            ins_code
          	) values(
			'activo',
			v_parametros.id_medio_pago,
            v_parametros.instancia_pago_id,
			v_parametros.nombre,
			v_parametros.codigo,
			v_parametros.codigo_forma_pago,
			v_parametros.codigo_medio_pago,
			p_id_usuario,
			now(),
			v_parametros._id_usuario_ai,
			v_parametros._nombre_usuario_ai,
			null,
			null,
			v_parametros.fp_code,
            v_parametros.ins_code


			)RETURNING id_instancia_pago into v_id_instancia_pago;

			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Instancia Pago almacenado(a) con exito (id_instancia_pago'||v_id_instancia_pago||')');
            v_resp = pxp.f_agrega_clave(v_resp,'id_instancia_pago',v_id_instancia_pago::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'OBING_INSP_MOD'
 	#DESCRIPCION:	Modificacion de registros
 	#AUTOR:		admin
 	#FECHA:		04-06-2019 19:31:28
	***********************************/

	elsif(p_transaccion='OBING_INSP_MOD')then

		begin
			--Sentencia de la modificacion
			update obingresos.tinstancia_pago set
			id_medio_pago = v_parametros.id_medio_pago,
            instancia_pago_id = v_parametros.instancia_pago_id,
			nombre = v_parametros.nombre,
			codigo = v_parametros.codigo,
			codigo_forma_pago = v_parametros.codigo_forma_pago,
			codigo_medio_pago = v_parametros.codigo_medio_pago,
			id_usuario_mod = p_id_usuario,
			fecha_mod = now(),
			id_usuario_ai = v_parametros._id_usuario_ai,
			usuario_ai = v_parametros._nombre_usuario_ai,
            fp_code = v_parametros.fp_code,
            ins_code = v_parametros.ins_code
			where id_instancia_pago=v_parametros.id_instancia_pago;

			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Instancia Pago modificado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_instancia_pago',v_parametros.id_instancia_pago::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'OBING_INSP_ELI'
 	#DESCRIPCION:	Eliminacion de registros
 	#AUTOR:		admin
 	#FECHA:		04-06-2019 19:31:28
	***********************************/

	elsif(p_transaccion='OBING_INSP_ELI')then

		begin
			--Sentencia de la eliminacion
			delete from obingresos.tinstancia_pago
            where id_instancia_pago=v_parametros.id_instancia_pago;

            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Instancia Pago eliminado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_instancia_pago',v_parametros.id_instancia_pago::varchar);

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

ALTER FUNCTION obingresos.ft_instancia_pago_ime (p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
  OWNER TO postgres;
