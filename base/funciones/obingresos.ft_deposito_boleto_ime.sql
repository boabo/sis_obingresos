CREATE OR REPLACE FUNCTION "obingresos"."ft_deposito_boleto_ime" (	
				p_administrador integer, p_id_usuario integer, p_tabla character varying, p_transaccion character varying)
RETURNS character varying AS
$BODY$

/**************************************************************************
 SISTEMA:		Ingresos
 FUNCION: 		obingresos.ft_deposito_boleto_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'obingresos.tdeposito_boleto'
 AUTOR: 		 (jrivera)
 FECHA:	        06-01-2016 22:42:31
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
	v_id_deposito_boleto	integer;
			    
BEGIN

    v_nombre_funcion = 'obingresos.ft_deposito_boleto_ime';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************    
 	#TRANSACCION:  'OBING_DEPBOL_INS'
 	#DESCRIPCION:	Insercion de registros
 	#AUTOR:		jrivera	
 	#FECHA:		06-01-2016 22:42:31
	***********************************/

	if(p_transaccion='OBING_DEPBOL_INS')then
					
        begin
        	--Sentencia de la insercion
        	insert into obingresos.tdeposito_boleto(
			id_boleto,
			id_deposito,
			tc,
			estado_reg,
			monto_moneda_boleto,
			monto_moneda_deposito,
			id_usuario_reg,
			fecha_reg,
			usuario_ai,
			id_usuario_ai,
			fecha_mod,
			id_usuario_mod
          	) values(
			v_parametros.id_boleto,
			v_parametros.id_deposito,
			v_parametros.tc,
			'activo',
			v_parametros.monto_moneda_boleto,
			v_parametros.monto_moneda_deposito,
			p_id_usuario,
			now(),
			v_parametros._nombre_usuario_ai,
			v_parametros._id_usuario_ai,
			null,
			null
							
			
			
			)RETURNING id_deposito_boleto into v_id_deposito_boleto;
			
			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Depositos - Boletos almacenado(a) con exito (id_deposito_boleto'||v_id_deposito_boleto||')'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_deposito_boleto',v_id_deposito_boleto::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************    
 	#TRANSACCION:  'OBING_DEPBOL_MOD'
 	#DESCRIPCION:	Modificacion de registros
 	#AUTOR:		jrivera	
 	#FECHA:		06-01-2016 22:42:31
	***********************************/

	elsif(p_transaccion='OBING_DEPBOL_MOD')then

		begin
			--Sentencia de la modificacion
			update obingresos.tdeposito_boleto set
			id_boleto = v_parametros.id_boleto,
			id_deposito = v_parametros.id_deposito,
			tc = v_parametros.tc,
			monto_moneda_boleto = v_parametros.monto_moneda_boleto,
			monto_moneda_deposito = v_parametros.monto_moneda_deposito,
			fecha_mod = now(),
			id_usuario_mod = p_id_usuario,
			id_usuario_ai = v_parametros._id_usuario_ai,
			usuario_ai = v_parametros._nombre_usuario_ai
			where id_deposito_boleto=v_parametros.id_deposito_boleto;
               
			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Depositos - Boletos modificado(a)'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_deposito_boleto',v_parametros.id_deposito_boleto::varchar);
               
            --Devuelve la respuesta
            return v_resp;
            
		end;

	/*********************************    
 	#TRANSACCION:  'OBING_DEPBOL_ELI'
 	#DESCRIPCION:	Eliminacion de registros
 	#AUTOR:		jrivera	
 	#FECHA:		06-01-2016 22:42:31
	***********************************/

	elsif(p_transaccion='OBING_DEPBOL_ELI')then

		begin
			--Sentencia de la eliminacion
			delete from obingresos.tdeposito_boleto
            where id_deposito_boleto=v_parametros.id_deposito_boleto;
               
            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Depositos - Boletos eliminado(a)'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_deposito_boleto',v_parametros.id_deposito_boleto::varchar);
              
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
ALTER FUNCTION "obingresos"."ft_deposito_boleto_ime"(integer, integer, character varying, character varying) OWNER TO postgres;
