CREATE OR REPLACE FUNCTION obingresos.ft_viajero_frecuente_ime (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Ingresos
 FUNCION: 		obingresos.ft_viajero_frecuente_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'obingresos.tviajero_frecuente'
 AUTOR: 		 (miguel.mamani)
 FECHA:	        12-12-2017 19:32:55
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				12-12-2017 19:32:55								Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'obingresos.tviajero_frecuente'
 #
 ***************************************************************************/

DECLARE

	v_nro_requerimiento    	integer;
	v_parametros           	record;
	v_id_requerimiento     	integer;
	v_resp		            varchar;
	v_nombre_funcion        text;
	v_mensaje_error         text;
	v_id_viajero_frecuente	integer;

BEGIN

    v_nombre_funcion = 'obingresos.ft_viajero_frecuente_ime';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'OBING_VFB_INS'
 	#DESCRIPCION:	Insercion de registros
 	#AUTOR:		miguel.mamani
 	#FECHA:		12-12-2017 19:32:55
	***********************************/

	if(p_transaccion='OBING_VFB_INS')then

        begin
        	--Sentencia de la insercion
        	insert into obingresos.tviajero_frecuente(
			nombre_completo,
			voucher_code,
			estado_reg,
			pnr,
			status,
			ffid,
			ticket_number,
			mensaje,
			id_pasajero_frecuente,
			id_boleto_amadeus,
			id_usuario_reg,
			fecha_reg,
			usuario_ai,
			id_usuario_ai,
			id_usuario_mod,
			fecha_mod
          	) values(
			v_parametros.nombre_completo,
			v_parametros.voucher_code,
			'activo',
			v_parametros.pnr,
			v_parametros.status,
			v_parametros.ffid,
			v_parametros.ticket_number,
			v_parametros.mensaje,
			v_parametros.id_pasajero_frecuente,
			v_parametros.id_boleto_amadeus,
			p_id_usuario,
			now(),
			v_parametros._nombre_usuario_ai,
			v_parametros._id_usuario_ai,
			null,
			null



			)RETURNING id_viajero_frecuente into v_id_viajero_frecuente;

			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Viajero Frecuente almacenado(a) con exito (id_viajero_frecuente'||v_id_viajero_frecuente||')');
            v_resp = pxp.f_agrega_clave(v_resp,'id_viajero_frecuente',v_id_viajero_frecuente::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'OBING_VFB_MOD'
 	#DESCRIPCION:	Modificacion de registros
 	#AUTOR:		miguel.mamani
 	#FECHA:		12-12-2017 19:32:55
	***********************************/

	elsif(p_transaccion='OBING_VFB_MOD')then

		begin
			--Sentencia de la modificacion
			update obingresos.tviajero_frecuente set
			nombre_completo = v_parametros.nombre_completo,
			voucher_code = v_parametros.voucher_code,
			pnr = v_parametros.pnr,
			status = v_parametros.status,
			ffid = v_parametros.ffid,
			ticket_number = v_parametros.ticket_number,
			mensaje = v_parametros.mensaje,
			id_pasajero_frecuente = v_parametros.id_pasajero_frecuente,
			id_boleto_amadeus = v_parametros.id_boleto_amadeus,
			id_usuario_mod = p_id_usuario,
			fecha_mod = now(),
			id_usuario_ai = v_parametros._id_usuario_ai,
			usuario_ai = v_parametros._nombre_usuario_ai
			where id_viajero_frecuente=v_parametros.id_viajero_frecuente;

			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Viajero Frecuente modificado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_viajero_frecuente',v_parametros.id_viajero_frecuente::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'OBING_VFB_ELI'
 	#DESCRIPCION:	Eliminacion de registros
 	#AUTOR:		miguel.mamani
 	#FECHA:		12-12-2017 19:32:55
	***********************************/

	elsif(p_transaccion='OBING_VFB_ELI')then

		begin
			--Sentencia de la eliminacion
			delete from obingresos.tviajero_frecuente
            where id_viajero_frecuente=v_parametros.id_viajero_frecuente;

            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Viajero Frecuente eliminado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_viajero_frecuente',v_parametros.id_viajero_frecuente::varchar);

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