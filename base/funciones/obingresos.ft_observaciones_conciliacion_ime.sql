CREATE OR REPLACE FUNCTION "obingresos"."ft_observaciones_conciliacion_ime" (	
				p_administrador integer, p_id_usuario integer, p_tabla character varying, p_transaccion character varying)
RETURNS character varying AS
$BODY$

/**************************************************************************
 SISTEMA:		Ingresos
 FUNCION: 		obingresos.ft_observaciones_conciliacion_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'obingresos.tobservaciones_conciliacion'
 AUTOR: 		 (jrivera)
 FECHA:	        01-06-2017 21:16:45
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
	v_id_observaciones_conciliacion	integer;
			    
BEGIN

    v_nombre_funcion = 'obingresos.ft_observaciones_conciliacion_ime';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************    
 	#TRANSACCION:  'OBING_OBC_INS'
 	#DESCRIPCION:	Insercion de registros
 	#AUTOR:		jrivera	
 	#FECHA:		01-06-2017 21:16:45
	***********************************/

	if(p_transaccion='OBING_OBC_INS')then
					
        begin
        	--Sentencia de la insercion
        	insert into obingresos.tobservaciones_conciliacion(
			tipo_observacion,
			estado_reg,
			fecha_observacion,
			banco,
			observacion,
			id_usuario_reg,
			fecha_reg,
			usuario_ai,
			id_usuario_ai,
			fecha_mod,
			id_usuario_mod
          	) values(
			v_parametros.tipo_observacion,
			'activo',
			v_parametros.fecha_observacion,
			v_parametros.banco,
			v_parametros.observacion,
			p_id_usuario,
			now(),
			v_parametros._nombre_usuario_ai,
			v_parametros._id_usuario_ai,
			null,
			null
							
			
			
			)RETURNING id_observaciones_conciliacion into v_id_observaciones_conciliacion;
			
			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Observaciones Conciliacion almacenado(a) con exito (id_observaciones_conciliacion'||v_id_observaciones_conciliacion||')'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_observaciones_conciliacion',v_id_observaciones_conciliacion::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************    
 	#TRANSACCION:  'OBING_OBC_MOD'
 	#DESCRIPCION:	Modificacion de registros
 	#AUTOR:		jrivera	
 	#FECHA:		01-06-2017 21:16:45
	***********************************/

	elsif(p_transaccion='OBING_OBC_MOD')then

		begin
			--Sentencia de la modificacion
			update obingresos.tobservaciones_conciliacion set
			tipo_observacion = v_parametros.tipo_observacion,
			fecha_observacion = v_parametros.fecha_observacion,
			banco = v_parametros.banco,
			observacion = v_parametros.observacion,
			fecha_mod = now(),
			id_usuario_mod = p_id_usuario,
			id_usuario_ai = v_parametros._id_usuario_ai,
			usuario_ai = v_parametros._nombre_usuario_ai
			where id_observaciones_conciliacion=v_parametros.id_observaciones_conciliacion;
               
			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Observaciones Conciliacion modificado(a)'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_observaciones_conciliacion',v_parametros.id_observaciones_conciliacion::varchar);
               
            --Devuelve la respuesta
            return v_resp;
            
		end;

	/*********************************    
 	#TRANSACCION:  'OBING_OBC_ELI'
 	#DESCRIPCION:	Eliminacion de registros
 	#AUTOR:		jrivera	
 	#FECHA:		01-06-2017 21:16:45
	***********************************/

	elsif(p_transaccion='OBING_OBC_ELI')then

		begin
			--Sentencia de la eliminacion
			delete from obingresos.tobservaciones_conciliacion
            where id_observaciones_conciliacion=v_parametros.id_observaciones_conciliacion;
               
            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Observaciones Conciliacion eliminado(a)'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_observaciones_conciliacion',v_parametros.id_observaciones_conciliacion::varchar);
              
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
ALTER FUNCTION "obingresos"."ft_observaciones_conciliacion_ime"(integer, integer, character varying, character varying) OWNER TO postgres;
