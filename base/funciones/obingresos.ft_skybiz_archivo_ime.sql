CREATE OR REPLACE FUNCTION "obingresos"."ft_skybiz_archivo_ime" (	
				p_administrador integer, p_id_usuario integer, p_tabla character varying, p_transaccion character varying)
RETURNS character varying AS
$BODY$

/**************************************************************************
 SISTEMA:		Ingresos
 FUNCION: 		obingresos.ft_skybiz_archivo_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'obingresos.tskybiz_archivo'
 AUTOR: 		 (admin)
 FECHA:	        15-02-2017 15:18:39
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
	v_id_skybiz_archivo	integer;
	v_registros_json RECORD;
	v_banco VARCHAR[];
			    
BEGIN

    v_nombre_funcion = 'obingresos.ft_skybiz_archivo_ime';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************    
 	#TRANSACCION:  'OBING_SKYBIZ_INS'
 	#DESCRIPCION:	Insercion de registros
 	#AUTOR:		admin	
 	#FECHA:		15-02-2017 15:18:39
	***********************************/

	if(p_transaccion='OBING_SKYBIZ_INS')then
					
        begin
        	--Sentencia de la insercion
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
			fecha_mod
          	) values(
			v_parametros.fecha,
			v_parametros.subido,
			v_parametros.comentario,
			'activo',
			v_parametros.nombre_archivo,
			v_parametros._id_usuario_ai,
			v_parametros._nombre_usuario_ai,
			now(),
			p_id_usuario,
			null,
			null
							
			
			
			)RETURNING id_skybiz_archivo into v_id_skybiz_archivo;
			
			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Skybiz Archivo almacenado(a) con exito (id_skybiz_archivo'||v_id_skybiz_archivo||')'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_skybiz_archivo',v_id_skybiz_archivo::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************    
 	#TRANSACCION:  'OBING_SKYBIZ_MOD'
 	#DESCRIPCION:	Modificacion de registros
 	#AUTOR:		admin	
 	#FECHA:		15-02-2017 15:18:39
	***********************************/

	elsif(p_transaccion='OBING_SKYBIZ_MOD')then

		begin
			--Sentencia de la modificacion
			update obingresos.tskybiz_archivo set
			fecha = v_parametros.fecha,
			subido = v_parametros.subido,
			comentario = v_parametros.comentario,
			nombre_archivo = v_parametros.nombre_archivo,
			id_usuario_mod = p_id_usuario,
			fecha_mod = now(),
			id_usuario_ai = v_parametros._id_usuario_ai,
			usuario_ai = v_parametros._nombre_usuario_ai
			where id_skybiz_archivo=v_parametros.id_skybiz_archivo;
               
			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Skybiz Archivo modificado(a)'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_skybiz_archivo',v_parametros.id_skybiz_archivo::varchar);
               
            --Devuelve la respuesta
            return v_resp;
            
		end;

	/*********************************    
 	#TRANSACCION:  'OBING_SKYBIZ_ELI'
 	#DESCRIPCION:	Eliminacion de registros
 	#AUTOR:		admin	
 	#FECHA:		15-02-2017 15:18:39
	***********************************/

	elsif(p_transaccion='OBING_SKYBIZ_ELI')then

		begin
			--Sentencia de la eliminacion
			delete from obingresos.tskybiz_archivo
            where id_skybiz_archivo=v_parametros.id_skybiz_archivo;
               
            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Skybiz Archivo eliminado(a)'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_skybiz_archivo',v_parametros.id_skybiz_archivo::varchar);
              
            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'OBING_SKYBIZ_JSON'
 	#DESCRIPCION:	registro con json
 	#AUTOR:		admin
 	#FECHA:		15-02-2017 15:18:39
	***********************************/

	elsif(p_transaccion='OBING_SKYBIZ_JSON')then

		begin
			--Sentencia de la eliminacion


			--DELETE FROM obingresos.tskybiz_archivo where fecha = v_parametros.fecha;

			FOR v_registros_json IN (SELECT *
															 FROM json_populate_recordset(NULL :: obingresos.json_ins_skybiz_archivo,
																														v_parametros.arra_json :: JSON)) LOOP

				IF v_registros_json.subido = 'si' THEN

					v_banco = regexp_split_to_array(v_registros_json.nombre_archivo, '.xlsx');

					v_banco = regexp_split_to_array(v_banco[1], '_');

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
						now(),
						v_registros_json.subido,
						v_registros_json.comentario,
						'activo',
						v_registros_json.nombre_archivo,
						v_parametros._id_usuario_ai,
						v_parametros._nombre_usuario_ai,
						now(),
						p_id_usuario,
						null,
						null,
						v_banco[2],
						v_banco[1]



					)RETURNING id_skybiz_archivo into v_id_skybiz_archivo;

				END IF;




			END LOOP;


            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Skybiz Archivo eliminado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_skybiz_archivo','subidos'::varchar);

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
ALTER FUNCTION "obingresos"."ft_skybiz_archivo_ime"(integer, integer, character varying, character varying) OWNER TO postgres;
