CREATE OR REPLACE FUNCTION obingresos.ft_forma_pago_pw_ime (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Ingresos
 FUNCION: 		obingresos.ft_forma_pago_pw_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'obingresos.tforma_pago_pw'
 AUTOR: 		 (admin)
 FECHA:	        04-06-2019 21:58:00
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				04-06-2019 21:58:00								Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'obingresos.tforma_pago_pw'
 #
 ***************************************************************************/

DECLARE

	v_nro_requerimiento    	integer;
	v_parametros           	record;
	v_id_requerimiento     	integer;
	v_resp		            varchar;
	v_nombre_funcion        text;
	v_mensaje_error         text;
	v_id_forma_pago_pw	integer;

BEGIN

    v_nombre_funcion = 'obingresos.ft_forma_pago_pw_ime';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'OBING_FPPW_INS'
 	#DESCRIPCION:	Insercion de registros
 	#AUTOR:		admin
 	#FECHA:		04-06-2019 21:58:00
	***********************************/

	if(p_transaccion='OBING_FPPW_INS')then

        begin
        	--Sentencia de la insercion
        	insert into obingresos.tforma_pago_pw(
			estado_reg,
			name,
			country_code,
			erp_code,
			fop_code,
			manage_account,
			id_usuario_reg,
			fecha_reg,
			id_usuario_ai,
			usuario_ai,
			id_usuario_mod,
			fecha_mod
          	) values(
			'activo',
			v_parametros.name,
			v_parametros.country_code,
			v_parametros.erp_code,
			v_parametros.fop_code,
			v_parametros.manage_account,
			p_id_usuario,
			now(),
			v_parametros._id_usuario_ai,
			v_parametros._nombre_usuario_ai,
			null,
			null



			)RETURNING id_forma_pago_pw into v_id_forma_pago_pw;

			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Forma Pago PW almacenado(a) con exito (id_forma_pago_pw'||v_id_forma_pago_pw||')');
            v_resp = pxp.f_agrega_clave(v_resp,'id_forma_pago_pw',v_id_forma_pago_pw::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'OBING_FPPW_MOD'
 	#DESCRIPCION:	Modificacion de registros
 	#AUTOR:		admin
 	#FECHA:		04-06-2019 21:58:00
	***********************************/

	elsif(p_transaccion='OBING_FPPW_MOD')then

		begin
			--Sentencia de la modificacion
			update obingresos.tforma_pago_pw set
			name = v_parametros.name,
			country_code = v_parametros.country_code,
			erp_code = v_parametros.erp_code,
			fop_code = v_parametros.fop_code,
			manage_account = v_parametros.manage_account,
			id_usuario_mod = p_id_usuario,
			fecha_mod = now(),
			id_usuario_ai = v_parametros._id_usuario_ai,
			usuario_ai = v_parametros._nombre_usuario_ai
			where id_forma_pago_pw=v_parametros.id_forma_pago_pw;

			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Forma Pago PW modificado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_forma_pago_pw',v_parametros.id_forma_pago_pw::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'OBING_FPPW_ELI'
 	#DESCRIPCION:	Eliminacion de registros
 	#AUTOR:		admin
 	#FECHA:		04-06-2019 21:58:00
	***********************************/

	elsif(p_transaccion='OBING_FPPW_ELI')then

		begin
			--Sentencia de la eliminacion
			delete from obingresos.tforma_pago_pw
            where id_forma_pago_pw=v_parametros.id_forma_pago_pw;

            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Forma Pago PW eliminado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_forma_pago_pw',v_parametros.id_forma_pago_pw::varchar);

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

ALTER FUNCTION obingresos.ft_forma_pago_pw_ime (p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
  OWNER TO postgres;
