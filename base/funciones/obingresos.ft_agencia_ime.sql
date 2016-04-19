CREATE OR REPLACE FUNCTION "obingresos"."ft_agencia_ime" (	
				p_administrador integer, p_id_usuario integer, p_tabla character varying, p_transaccion character varying)
RETURNS character varying AS
$BODY$

/**************************************************************************
 SISTEMA:		Ingresos
 FUNCION: 		obingresos.ft_agencia_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'obingresos.tagencia'
 AUTOR: 		 (jrivera)
 FECHA:	        06-01-2016 22:02:33
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
	v_id_agencia	integer;
			    
BEGIN

    v_nombre_funcion = 'obingresos.ft_agencia_ime';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************    
 	#TRANSACCION:  'OBING_AGE_INS'
 	#DESCRIPCION:	Insercion de registros
 	#AUTOR:		jrivera	
 	#FECHA:		06-01-2016 22:02:33
	***********************************/

	if(p_transaccion='OBING_AGE_INS')then
					
        begin
        	--Sentencia de la insercion
        	insert into obingresos.tagencia(
			id_moneda_control,
			tipo_cambio,
			codigo,
			monto_maximo_deuda,
			tipo_agencia,
			codigo_int,
			nombre,
			tipo_pago,
			estado_reg,
			depositos_moneda_boleto,
			id_usuario_ai,
			id_usuario_reg,
			usuario_ai,
			fecha_reg,
			fecha_mod,
			id_usuario_mod
          	) values(
			v_parametros.id_moneda_control,
			v_parametros.tipo_cambio,
			v_parametros.codigo,
			v_parametros.monto_maximo_deuda,
			v_parametros.tipo_agencia,
			v_parametros.codigo_int,
			v_parametros.nombre,
			v_parametros.tipo_pago,
			'activo',
			v_parametros.depositos_moneda_boleto,
			v_parametros._id_usuario_ai,
			p_id_usuario,
			v_parametros._nombre_usuario_ai,
			now(),
			null,
			null
							
			
			
			)RETURNING id_agencia into v_id_agencia;
			
			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Agencias almacenado(a) con exito (id_agencia'||v_id_agencia||')'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_agencia',v_id_agencia::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************    
 	#TRANSACCION:  'OBING_AGE_MOD'
 	#DESCRIPCION:	Modificacion de registros
 	#AUTOR:		jrivera	
 	#FECHA:		06-01-2016 22:02:33
	***********************************/

	elsif(p_transaccion='OBING_AGE_MOD')then

		begin
			--Sentencia de la modificacion
			update obingresos.tagencia set
			id_moneda_control = v_parametros.id_moneda_control,
			tipo_cambio = v_parametros.tipo_cambio,
			codigo = v_parametros.codigo,
			monto_maximo_deuda = v_parametros.monto_maximo_deuda,
			tipo_agencia = v_parametros.tipo_agencia,
			codigo_int = v_parametros.codigo_int,
			nombre = v_parametros.nombre,
			tipo_pago = v_parametros.tipo_pago,
			depositos_moneda_boleto = v_parametros.depositos_moneda_boleto,
			fecha_mod = now(),
			id_usuario_mod = p_id_usuario,
			id_usuario_ai = v_parametros._id_usuario_ai,
			usuario_ai = v_parametros._nombre_usuario_ai
			where id_agencia=v_parametros.id_agencia;
               
			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Agencias modificado(a)'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_agencia',v_parametros.id_agencia::varchar);
               
            --Devuelve la respuesta
            return v_resp;
            
		end;

	/*********************************    
 	#TRANSACCION:  'OBING_AGE_ELI'
 	#DESCRIPCION:	Eliminacion de registros
 	#AUTOR:		jrivera	
 	#FECHA:		06-01-2016 22:02:33
	***********************************/

	elsif(p_transaccion='OBING_AGE_ELI')then

		begin
			--Sentencia de la eliminacion
			delete from obingresos.tagencia
            where id_agencia=v_parametros.id_agencia;
               
            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Agencias eliminado(a)'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_agencia',v_parametros.id_agencia::varchar);
              
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
ALTER FUNCTION "obingresos"."ft_agencia_ime"(integer, integer, character varying, character varying) OWNER TO postgres;
