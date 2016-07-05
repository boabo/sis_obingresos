CREATE OR REPLACE FUNCTION "obingresos"."ft_forma_pago_ime" (	
				p_administrador integer, p_id_usuario integer, p_tabla character varying, p_transaccion character varying)
RETURNS character varying AS
$BODY$

/**************************************************************************
 SISTEMA:		Ingresos
 FUNCION: 		obingresos.ft_forma_pago_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'obingresos.tforma_pago'
 AUTOR: 		 (jrivera)
 FECHA:	        10-06-2016 20:37:45
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
	v_id_forma_pago	integer;
			    
BEGIN

    v_nombre_funcion = 'obingresos.ft_forma_pago_ime';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************    
 	#TRANSACCION:  'OBING_FOP_INS'
 	#DESCRIPCION:	Insercion de registros
 	#AUTOR:		jrivera	
 	#FECHA:		10-06-2016 20:37:45
	***********************************/

	if(p_transaccion='OBING_FOP_INS')then
					
        begin
        	--Sentencia de la insercion
        	insert into obingresos.tforma_pago(
			id_entidad,
			id_moneda,
			codigo,
			registrar_tarjeta,
			defecto,
			estado_reg,
			registrar_cc,
			nombre,
			usuario_ai,
			fecha_reg,
			id_usuario_reg,
			id_usuario_ai,
			fecha_mod,
			id_usuario_mod
          	) values(
			v_parametros.id_entidad,
			v_parametros.id_moneda,
			v_parametros.codigo,
			v_parametros.registrar_tarjeta,
			v_parametros.defecto,
			'activo',
			v_parametros.registrar_cc,
			v_parametros.nombre,
			v_parametros._nombre_usuario_ai,
			now(),
			p_id_usuario,
			v_parametros._id_usuario_ai,
			null,
			null
							
			
			
			)RETURNING id_forma_pago into v_id_forma_pago;
			
			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Forma Pago almacenado(a) con exito (id_forma_pago'||v_id_forma_pago||')'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_forma_pago',v_id_forma_pago::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************    
 	#TRANSACCION:  'OBING_FOP_MOD'
 	#DESCRIPCION:	Modificacion de registros
 	#AUTOR:		jrivera	
 	#FECHA:		10-06-2016 20:37:45
	***********************************/

	elsif(p_transaccion='OBING_FOP_MOD')then

		begin
			--Sentencia de la modificacion
			update obingresos.tforma_pago set
			id_entidad = v_parametros.id_entidad,
			id_moneda = v_parametros.id_moneda,
			codigo = v_parametros.codigo,
			registrar_tarjeta = v_parametros.registrar_tarjeta,
			defecto = v_parametros.defecto,
			registrar_cc = v_parametros.registrar_cc,
			nombre = v_parametros.nombre,
			fecha_mod = now(),
			id_usuario_mod = p_id_usuario,
			id_usuario_ai = v_parametros._id_usuario_ai,
			usuario_ai = v_parametros._nombre_usuario_ai
			where id_forma_pago=v_parametros.id_forma_pago;
               
			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Forma Pago modificado(a)'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_forma_pago',v_parametros.id_forma_pago::varchar);
               
            --Devuelve la respuesta
            return v_resp;
            
		end;

	/*********************************    
 	#TRANSACCION:  'OBING_FOP_ELI'
 	#DESCRIPCION:	Eliminacion de registros
 	#AUTOR:		jrivera	
 	#FECHA:		10-06-2016 20:37:45
	***********************************/

	elsif(p_transaccion='OBING_FOP_ELI')then

		begin
			--Sentencia de la eliminacion
			delete from obingresos.tforma_pago
            where id_forma_pago=v_parametros.id_forma_pago;
               
            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Forma Pago eliminado(a)'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_forma_pago',v_parametros.id_forma_pago::varchar);
              
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
ALTER FUNCTION "obingresos"."ft_forma_pago_ime"(integer, integer, character varying, character varying) OWNER TO postgres;
