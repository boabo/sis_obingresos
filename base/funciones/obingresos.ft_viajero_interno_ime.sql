CREATE OR REPLACE FUNCTION obingresos.ft_viajero_interno_ime (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Ingresos
 FUNCION: 		obingresos.ft_viajero_interno_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'obingresos.tviajero_interno'
 AUTOR: 		 (rzabala)
 FECHA:	        21-12-2018 14:21:03
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				21-12-2018 14:21:03								Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'obingresos.tviajero_interno'
 #
 ***************************************************************************/

DECLARE

	v_nro_requerimiento    	integer;
	v_parametros           	record;
	v_id_requerimiento     	integer;
	v_resp		            varchar;
	v_nombre_funcion        text;
	v_mensaje_error         text;
	v_id_viajero_interno	integer;
    v_record_json			jsonb;
    v_beneficiario          varchar;
    v_codvoucher				varchar;
    v_estadviaint			varchar;
    v_prueba				varchar;

BEGIN

    v_nombre_funcion = 'obingresos.ft_viajero_interno_ime';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'OBING_CVI_INS'
 	#DESCRIPCION:	Insercion de registros
 	#AUTOR:		rzabala
 	#FECHA:		21-12-2018 14:21:03
	***********************************/

	if(p_transaccion='OBING_CVI_INS')then

        begin
        	select ac.codigo_voucher,ac.estado_reg
            	into v_codvoucher, v_estadviaint
            	from obingresos.tviajero_interno ac
            	where ac.codigo_voucher = 'OB.PD.VO'||v_parametros.codigo_voucher
            	order by ac.fecha_reg desc
            	limit 1;
            if (v_estadviaint = 'activo') then
             	update obingresos.tviajero_interno set
            	estado_reg = 'inactivo'
            	where codigo_voucher = v_codvoucher;
            end if;
         /*for v_record_json in SELECT * FROM jsonb_array_elements(v_parametros.detalles) loop
         	raise exception 'valor : %', v_record_json->>'funcionario';
         end loop; */
        	--Sentencia de la insercion
            --raise exception 'valor : %', '=>'||v_parametros.mensaje;
        	insert into obingresos.tviajero_interno(
			estado_reg,
			codigo_voucher,
			mensaje,
			estado,
			id_usuario_reg,
			fecha_reg,
			id_usuario_ai,
			usuario_ai,
			id_usuario_mod,
			fecha_mod
          	) values(
			'activo',
			'OB.PD.VO'||v_parametros.codigo_voucher,
			'=>'||v_parametros.mensaje,
			v_parametros.estado,
			p_id_usuario,
			now(),
			v_parametros._id_usuario_ai,
			v_parametros._nombre_usuario_ai,
			null,
			null



			)RETURNING id_viajero_interno into v_id_viajero_interno;

            --v_prueba =
            --(v_parametros.detalles)::text ='null';
            --raise exception 'valor : %', v_parametros.detalles;
            if ((v_parametros.detalles)::text !='null') then
            --raise exception 'entra';
            for v_record_json in SELECT * FROM jsonb_array_elements(v_parametros.detalles) loop
            --raise exception 'valor : %', v_record_json->>'tarifa';
            	if (v_record_json->>'beneficiario' = '')then
                	v_beneficiario = v_record_json->>'funcionario';
                ELSE
                	v_beneficiario = v_record_json->>'beneficiario';
                end if;

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
				v_beneficiario,
				v_record_json->>'pnr',
				v_record_json->>'Boleto',
				p_id_usuario,
				now(),
				v_parametros._id_usuario_ai,
				v_parametros._nombre_usuario_ai,
				null,
				null,
	            v_id_viajero_interno,
    	        cast(v_record_json->>'PsjSolicitudID'as TEXT),
        	    v_record_json->>'numDocumento',
            	v_record_json->>'estadoVoucher',
                v_record_json->>'tarifa'
				);


         	--raise exception 'valor : %', v_record_json->>'PsjSolicitudID';
         end loop;
            --raise exception 'valor : %', v_id_viajero_interno;
            end if;
             --raise exception 'salse';

			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Consulta Viajero Interno almacenado(a) con exito (id_viajero_interno'||v_id_viajero_interno||')');
            v_resp = pxp.f_agrega_clave(v_resp,'id_viajero_interno',v_id_viajero_interno::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;
	/*********************************
 	#TRANSACCION:  'OBING_CVI_MOD'
 	#DESCRIPCION:	Modificacion de registros
 	#AUTOR:		rzabala
 	#FECHA:		21-12-2018 14:21:03
	***********************************/

	elsif(p_transaccion='OBING_CVI_MOD')then

		begin
			--Sentencia de la modificacion
			update obingresos.tviajero_interno set
			codigo_voucher = 'OB.FF.VO'||v_parametros.codigo_voucher,
			mensaje = v_parametros.mensaje,
			estado = 'abierto'||v_parametros.estado,
			id_usuario_mod = p_id_usuario,
			fecha_mod = now(),
			id_usuario_ai = v_parametros._id_usuario_ai,
			usuario_ai = v_parametros._nombre_usuario_ai
			where id_viajero_interno=v_parametros.id_viajero_interno;

			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Consulta Viajero Interno modificado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_viajero_interno',v_parametros.id_viajero_interno::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'OBING_CVI_ELI'
 	#DESCRIPCION:	Eliminacion de registros
 	#AUTOR:		rzabala
 	#FECHA:		21-12-2018 14:21:03
	***********************************/

	elsif(p_transaccion='OBING_CVI_ELI')then

		begin
			--Sentencia de la eliminacion
			delete from obingresos.tviajero_interno
            where id_viajero_interno=v_parametros.id_viajero_interno;

            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Consulta Viajero Interno eliminado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_viajero_interno',v_parametros.id_viajero_interno::varchar);

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