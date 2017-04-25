CREATE OR REPLACE FUNCTION "obingresos"."ft_skybiz_archivo_detalle_ime" (	
				p_administrador integer, p_id_usuario integer, p_tabla character varying, p_transaccion character varying)
RETURNS character varying AS
$BODY$

/**************************************************************************
 SISTEMA:		Ingresos
 FUNCION: 		obingresos.ft_skybiz_archivo_detalle_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'obingresos.tskybiz_archivo_detalle'
 AUTOR: 		 (admin)
 FECHA:	        15-02-2017 19:08:58
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
	v_id_skybiz_archivo_detalle	integer;
	v_registros_json	RECORD;
	v_skybiz_archivo	RECORD;
	v_banco VARCHAR[];
	v_id_skybiz_archivo INTEGER;
	v_fecha VARCHAR[];

BEGIN

    v_nombre_funcion = 'obingresos.ft_skybiz_archivo_detalle_ime';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************    
 	#TRANSACCION:  'OBING_SKYDET_INS'
 	#DESCRIPCION:	Insercion de registros
 	#AUTOR:		admin	
 	#FECHA:		15-02-2017 19:08:58
	***********************************/

	if(p_transaccion='OBING_SKYDET_INS')then
					
        begin
        	--Sentencia de la insercion
        	insert into obingresos.tskybiz_archivo_detalle(
			entity,
			request_date_time,
			currency,
			total_amount,
			ip,
			status,
			estado_reg,
			issue_date_time,
			identifier_pnr,
			id_skybiz_archivo,
			pnr,
			authorization_,
			id_usuario_ai,
			usuario_ai,
			fecha_reg,
			id_usuario_reg,
			id_usuario_mod,
			fecha_mod
          	) values(
			v_parametros.entity,
			v_parametros.request_date_time,
			v_parametros.currency,
			v_parametros.total_amount,
			v_parametros.ip,
			v_parametros.status,
			'activo',
			v_parametros.issue_date_time,
			v_parametros.identifier_pnr,
			v_parametros.id_skybiz_archivo,
			v_parametros.pnr,
			v_parametros.authorization_,
			v_parametros._id_usuario_ai,
			v_parametros._nombre_usuario_ai,
			now(),
			p_id_usuario,
			null,
			null
							
			
			
			)RETURNING id_skybiz_archivo_detalle into v_id_skybiz_archivo_detalle;
			
			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Skybiz Archivo Detalle almacenado(a) con exito (id_skybiz_archivo_detalle'||v_id_skybiz_archivo_detalle||')'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_skybiz_archivo_detalle',v_id_skybiz_archivo_detalle::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************    
 	#TRANSACCION:  'OBING_SKYDET_MOD'
 	#DESCRIPCION:	Modificacion de registros
 	#AUTOR:		admin	
 	#FECHA:		15-02-2017 19:08:58
	***********************************/

	elsif(p_transaccion='OBING_SKYDET_MOD')then

		begin
			--Sentencia de la modificacion
			update obingresos.tskybiz_archivo_detalle set
			entity = v_parametros.entity,
			request_date_time = v_parametros.request_date_time,
			currency = v_parametros.currency,
			total_amount = v_parametros.total_amount,
			ip = v_parametros.ip,
			status = v_parametros.status,
			issue_date_time = v_parametros.issue_date_time,
			identifier_pnr = v_parametros.identifier_pnr,
			id_skybiz_archivo = v_parametros.id_skybiz_archivo,
			pnr = v_parametros.pnr,
			authorization_ = v_parametros.authorization_,
			id_usuario_mod = p_id_usuario,
			fecha_mod = now(),
			id_usuario_ai = v_parametros._id_usuario_ai,
			usuario_ai = v_parametros._nombre_usuario_ai
			where id_skybiz_archivo_detalle=v_parametros.id_skybiz_archivo_detalle;
               
			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Skybiz Archivo Detalle modificado(a)'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_skybiz_archivo_detalle',v_parametros.id_skybiz_archivo_detalle::varchar);
               
            --Devuelve la respuesta
            return v_resp;
            
		end;

	/*********************************    
 	#TRANSACCION:  'OBING_SKYDET_ELI'
 	#DESCRIPCION:	Eliminacion de registros
 	#AUTOR:		admin	
 	#FECHA:		15-02-2017 19:08:58
	***********************************/

	elsif(p_transaccion='OBING_SKYDET_ELI')then

		begin
			--Sentencia de la eliminacion
			delete from obingresos.tskybiz_archivo_detalle
            where id_skybiz_archivo_detalle=v_parametros.id_skybiz_archivo_detalle;
               
            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Skybiz Archivo Detalle eliminado(a)'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_skybiz_archivo_detalle',v_parametros.id_skybiz_archivo_detalle::varchar);
              
            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'OBING_SKYDET_JSON'
 	#DESCRIPCION:	registro de json
 	#AUTOR:		admin
 	#FECHA:		15-02-2017 19:08:58
	***********************************/

	elsif(p_transaccion='OBING_SKYDET_JSON')then

		begin
			--Sentencia de la eliminacion

			--RAISE EXCEPTION '%',v_parametros.nombre_archivo;




			v_banco = regexp_split_to_array(v_parametros.nombre_archivo, '.xlsx');

			v_banco = regexp_split_to_array(v_banco[1], '_');

			v_fecha = regexp_split_to_array(v_banco[1], E'\\s+'); --separamos por el espacio
			v_banco = regexp_split_to_array(v_banco[2], ' ');



			insert into obingresos.tskybiz_archivo(
				fecha,
				subido,
				comentario,
				estado_reg,
				nombre_archivo,
				id_usuario_ai,
				usuario_ai,
				fecha_reg,
				id_usuario_reg,
				id_usuario_mod,
				fecha_mod,
				moneda,
				banco
			) values(
				v_fecha[1]::DATE,
				'si',
				'',
				'activo',
				v_parametros.nombre_archivo,
				v_parametros._id_usuario_ai,
				v_parametros._nombre_usuario_ai,
				now(),
				p_id_usuario,
				null,
				null,
				v_banco[2],
				v_banco[1]



			)RETURNING id_skybiz_archivo into v_id_skybiz_archivo;






			FOR v_registros_json IN (SELECT *
															 FROM json_populate_recordset(NULL :: obingresos.json_ins_skybiz_archivo_detalle,
																														v_parametros.arra_json :: JSON)) LOOP






				insert into obingresos.tskybiz_archivo_detalle(
					entity,
					request_date_time,
					currency,
					total_amount,
					ip,
					status,
					estado_reg,
					issue_date_time,
					identifier_pnr,
					id_skybiz_archivo,
					pnr,
					authorization_,
					id_usuario_ai,
					usuario_ai,
					fecha_reg,
					id_usuario_reg,
					id_usuario_mod,
					fecha_mod
				) values(
					v_registros_json.entity,
					v_registros_json.request_date_time,
					v_registros_json.currency,
					v_registros_json.total_amount::NUMERIC(10,2),
					v_registros_json.ip,
					v_registros_json.status,
					'activo',
					v_registros_json.issue_date_time,
					v_registros_json.identifier_pnr,
					v_id_skybiz_archivo,
					v_registros_json.pnr,
					v_registros_json.authorization_,
					v_parametros._id_usuario_ai,
					v_parametros._nombre_usuario_ai,
					now(),
					p_id_usuario,
					null,
					null



				);

			END LOOP;


            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Skybiz Archivo Detalle eliminado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_skybiz_archivo_detalle','id'::varchar);

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
$BODY$
LANGUAGE 'plpgsql' VOLATILE
COST 100;
ALTER FUNCTION "obingresos"."ft_skybiz_archivo_detalle_ime"(integer, integer, character varying, character varying) OWNER TO postgres;
