CREATE OR REPLACE FUNCTION obingresos.ft_viajero_interno_det_ime (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Ingresos
 FUNCION: 		obingresos.ft_viajero_interno_det_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'obingresos.tviajero_interno_det'
 AUTOR: 		 (rzabala)
 FECHA:	        21-12-2018 14:21:07
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				21-12-2018 14:21:07								Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'obingresos.tviajero_interno_det'
 #
 ***************************************************************************/

DECLARE

	v_nro_requerimiento    	integer;
	v_parametros           	record;
	v_id_requerimiento     	integer;
	v_resp		            varchar;
	v_nombre_funcion        text;
	v_mensaje_error         text;
	v_id_viajero_interno_det	integer;
    v_record_detalles			jsonb;
    v_record_request			jsonb;

BEGIN

    v_nombre_funcion = 'obingresos.ft_viajero_interno_det_ime';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'OBING_DVI_INS'
 	#DESCRIPCION:	Insercion de registros
 	#AUTOR:		rzabala
 	#FECHA:		21-12-2018 14:21:07
	***********************************/

	if(p_transaccion='OBING_DVI_INS')then

        begin
        	--Sentencia de la insercion
        	insert into obingresos.tviajero_interno_det(
			estado_reg,
			nombre,
			pnr,
			num_boleto,
			id_usuario_reg,
			fecha_reg,
			id_usuario_ai,
			usuario_ai,
			id_usuario_mod,
			fecha_mod,
            id_viajero_interno,
            solicitud,
            num_documento,
            estado_voucher,
            tarifa
          	) values(
			'activo',
			v_parametros.nombre,
			v_parametros.pnr,
			v_parametros.num_boleto,
			p_id_usuario,
			now(),
			v_parametros._id_usuario_ai,
			v_parametros._nombre_usuario_ai,
			null,
			null,
            v_parametros.id_viajero_interno,
            v_parametros.solicitud,
            v_parametros.num_documento,
            v_parametros.estado_voucher,
            v_parametros.tarifa



			)RETURNING id_viajero_interno_det into v_id_viajero_interno_det;

			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Detalle Viajero Interno almacenado(a) con exito (id_viajero_interno_det'||v_id_viajero_interno_det||')');
            v_resp = pxp.f_agrega_clave(v_resp,'id_viajero_interno_det',v_id_viajero_interno_det::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'OBING_DVI_MOD'
 	#DESCRIPCION:	Modificacion de registros
 	#AUTOR:		rzabala
 	#FECHA:		21-12-2018 14:21:07
	***********************************/

	elsif(p_transaccion='OBING_DVI_MOD')then

		begin
			--Sentencia de la modificacion
			update obingresos.tviajero_interno_det set
			nombre = v_parametros.nombre,
			pnr = v_parametros.pnr,
			num_boleto = '930'||v_parametros.num_boleto,
			id_usuario_mod = p_id_usuario,
			fecha_mod = now(),
			id_usuario_ai = v_parametros._id_usuario_ai,
			usuario_ai = v_parametros._nombre_usuario_ai,
            id_viajero_interno = v_parametros.id_viajero_interno,
            solicitud = v_parametros.solicitud,
            num_documento = v_parametros.num_documento,
            estado_voucher = v_parametros.estado_voucher,
            tarifa=v_parametros.tarifa
			where id_viajero_interno_det=v_parametros.id_viajero_interno_det;

			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Detalle Viajero Interno modificado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_viajero_interno_det',v_parametros.id_viajero_interno_det::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;


        /*********************************
 	#TRANSACCION:  'OBING_DVI_ACT'
 	#DESCRIPCION:	Actualizacion de estado
 	#AUTOR:		rzabala
 	#FECHA:		21-12-2018 14:21:07
	***********************************/

	elsif(p_transaccion='OBING_DVI_ACT')then

		begin
        for v_record_request in SELECT * FROM jsonb_array_elements(v_parametros.request) loop
        	--raise exception 'valor : %', v_record_request;
            IF( (v_record_request->>'numBoleto')::text != 'null' AND (v_record_request->>'pnr')::text !='')then
         	--raise exception 'valor : %', v_record_request;

			--Sentencia de la modificacion
				update obingresos.tviajero_interno_det set
            	pnr = cast(v_record_request->>'pnr'as TEXT),
            	num_boleto = '930'||(v_record_request->>'numBoleto')::varchar,
            	estado_voucher = 'EMITIDO'


				where solicitud=v_record_request->>'solicitudID';
            ELSE
            raise exception 'Se Debe Registrar Numero de Boleto y pnr:';
        	end if;
        end loop;

			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Detalle Viajero Interno modificado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'solicitud',v_record_request->>'solicitudID'::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'OBING_DVI_ELI'
 	#DESCRIPCION:	Eliminacion de registros
 	#AUTOR:		rzabala
 	#FECHA:		21-12-2018 14:21:07
	***********************************/

	elsif(p_transaccion='OBING_DVI_ELI')then

		begin
			--Sentencia de la eliminacion
			delete from obingresos.tviajero_interno_det
            where id_viajero_interno_det=v_parametros.id_viajero_interno_det;

            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Detalle Viajero Interno eliminado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_viajero_interno_det',v_parametros.id_viajero_interno_det::varchar);

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