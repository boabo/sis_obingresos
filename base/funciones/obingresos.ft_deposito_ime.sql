CREATE OR REPLACE FUNCTION "obingresos"."ft_deposito_ime" (	
				p_administrador integer, p_id_usuario integer, p_tabla character varying, p_transaccion character varying)
RETURNS character varying AS
$BODY$

/**************************************************************************
 SISTEMA:		Ingresos
 FUNCION: 		obingresos.ft_deposito_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'obingresos.tdeposito'
 AUTOR: 		 (jrivera)
 FECHA:	        06-01-2016 22:42:28
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
	v_id_deposito	integer;
			    
BEGIN

    v_nombre_funcion = 'obingresos.ft_deposito_ime';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************    
 	#TRANSACCION:  'OBING_DEP_INS'
 	#DESCRIPCION:	Insercion de registros
 	#AUTOR:		jrivera	
 	#FECHA:		06-01-2016 22:42:28
	***********************************/

	if(p_transaccion='OBING_DEP_INS')then
					
        begin
        	--Sentencia de la insercion
        	insert into obingresos.tdeposito(
			estado_reg,
			nro_deposito,
			monto_deposito,
			id_moneda_deposito,
			id_agencia,
			fecha,
			saldo,
			id_usuario_reg,
			fecha_reg,
			id_usuario_ai,
			usuario_ai,
			id_usuario_mod,
			fecha_mod
          	) values(
			'activo',
			v_parametros.nro_deposito,
			v_parametros.monto_deposito,
			v_parametros.id_moneda_deposito,
			v_parametros.id_agencia,
			v_parametros.fecha,
			v_parametros.saldo,
			p_id_usuario,
			now(),
			v_parametros._id_usuario_ai,
			v_parametros._nombre_usuario_ai,
			null,
			null
							
			
			
			)RETURNING id_deposito into v_id_deposito;
			
			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Depositos almacenado(a) con exito (id_deposito'||v_id_deposito||')'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_deposito',v_id_deposito::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************    
 	#TRANSACCION:  'OBING_DEP_MOD'
 	#DESCRIPCION:	Modificacion de registros
 	#AUTOR:		jrivera	
 	#FECHA:		06-01-2016 22:42:28
	***********************************/

	elsif(p_transaccion='OBING_DEP_MOD')then

		begin
			--Sentencia de la modificacion
			update obingresos.tdeposito set
			nro_deposito = v_parametros.nro_deposito,
			monto_deposito = v_parametros.monto_deposito,
			id_moneda_deposito = v_parametros.id_moneda_deposito,
			id_agencia = v_parametros.id_agencia,
			fecha = v_parametros.fecha,
			saldo = v_parametros.saldo,
			id_usuario_mod = p_id_usuario,
			fecha_mod = now(),
			id_usuario_ai = v_parametros._id_usuario_ai,
			usuario_ai = v_parametros._nombre_usuario_ai
			where id_deposito=v_parametros.id_deposito;
               
			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Depositos modificado(a)'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_deposito',v_parametros.id_deposito::varchar);
               
            --Devuelve la respuesta
            return v_resp;
            
		end;

	/*********************************    
 	#TRANSACCION:  'OBING_DEP_ELI'
 	#DESCRIPCION:	Eliminacion de registros
 	#AUTOR:		jrivera	
 	#FECHA:		06-01-2016 22:42:28
	***********************************/

	elsif(p_transaccion='OBING_DEP_ELI')then

		begin
			--Sentencia de la eliminacion
			delete from obingresos.tdeposito
            where id_deposito=v_parametros.id_deposito;
               
            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Depositos eliminado(a)'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_deposito',v_parametros.id_deposito::varchar);
              
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
ALTER FUNCTION "obingresos"."ft_deposito_ime"(integer, integer, character varying, character varying) OWNER TO postgres;
