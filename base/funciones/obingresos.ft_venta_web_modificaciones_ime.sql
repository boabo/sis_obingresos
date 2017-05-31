CREATE OR REPLACE FUNCTION obingresos.ft_venta_web_modificaciones_ime (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Ingresos
 FUNCION: 		obingresos.ft_venta_web_modificaciones_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'obingresos.tventa_web_modificaciones'
 AUTOR: 		 (jrivera)
 FECHA:	        11-01-2017 19:44:28
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
	v_id_venta_web_modificaciones	integer;
    v_registro				record;
    v_prueba				varchar;
			    
BEGIN

    v_nombre_funcion = 'obingresos.ft_venta_web_modificaciones_ime';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************    
 	#TRANSACCION:  'OBING_VWEBMOD_INS'
 	#DESCRIPCION:	Insercion de registros
 	#AUTOR:		jrivera	
 	#FECHA:		11-01-2017 19:44:28
	***********************************/

	if(p_transaccion='OBING_VWEBMOD_INS')then
					
        begin
        	--Sentencia de la insercion
        	insert into obingresos.tventa_web_modificaciones(
			nro_boleto,
			tipo,
			motivo,
			nro_boleto_reemision,
			used,
			estado_reg,
			id_usuario_ai,
			usuario_ai,
			fecha_reg,
			id_usuario_reg,
			fecha_mod,
			id_usuario_mod,
            procesado,
            fecha_reserva_antigua,
            pnr_antiguo,
            banco	
          	) values(
			v_parametros.nro_boleto,
			v_parametros.tipo,
			v_parametros.motivo,
			v_parametros.nro_boleto_reemision,
			v_parametros.used,
			'activo',
			v_parametros._id_usuario_ai,
			v_parametros._nombre_usuario_ai,
			now(),
			p_id_usuario,
			null,
			null,
			'no',
            v_parametros.fecha_reserva_antigua,
            v_parametros.pnr_antiguo,
            v_parametros.banco				
			
			
			)RETURNING id_venta_web_modificaciones into v_id_venta_web_modificaciones;
            
            if (v_parametros.tipo = 'emision_manual') then            	
                select informix.f_modificar_datos_web_emi_manual( v_parametros.nro_boleto_reemision,v_parametros.banco)into v_prueba;
            
            end if;
            
            if (v_parametros.tipo = 'reemision') then
            	select informix.f_modificar_datos_web_reemision( v_parametros.nro_boleto_reemision,v_parametros.nro_boleto)into v_prueba;
            end if;
			
			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Modificaciones Venta Web almacenado(a) con exito (id_venta_web_modificaciones'||v_id_venta_web_modificaciones||')'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_venta_web_modificaciones',v_id_venta_web_modificaciones::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************    
 	#TRANSACCION:  'OBING_VWEBMOD_MOD'
 	#DESCRIPCION:	Modificacion de registros
 	#AUTOR:		jrivera	
 	#FECHA:		11-01-2017 19:44:28
	***********************************/

	elsif(p_transaccion='OBING_VWEBMOD_MOD')then

		begin
			--Sentencia de la modificacion
            select * into v_registro
            from obingresos.tventa_web_modificaciones
            where id_venta_web_modificaciones=v_parametros.id_venta_web_modificaciones;
            
            if (v_registro.procesado = 'si') then
            	raise exception 'No es posible modificar el registro porq ya fue procesado';
            end if;
			update obingresos.tventa_web_modificaciones set
			nro_boleto = v_parametros.nro_boleto,
			tipo = v_parametros.tipo,
			motivo = v_parametros.motivo,
			nro_boleto_reemision = v_parametros.nro_boleto_reemision,
			used = v_parametros.used,
			fecha_mod = now(),
			id_usuario_mod = p_id_usuario,
			id_usuario_ai = v_parametros._id_usuario_ai,
			usuario_ai = v_parametros._nombre_usuario_ai,
            fecha_reserva_antigua = v_parametros.fecha_reserva_antigua,
            pnr_antiguo = v_parametros.pnr_antiguo,
            banco = v_parametros.banco
			where id_venta_web_modificaciones=v_parametros.id_venta_web_modificaciones;
            
            select * into v_registro
            from obingresos.tventa_web_modificaciones
            where id_venta_web_modificaciones=v_parametros.id_venta_web_modificaciones;
            
            if ( v_parametros.tipo = 'emision_manual' and v_registro.procesado = 'no') then
            	select informix.f_modificar_datos_web_emi_manual( v_parametros.nro_boleto_reemision,v_parametros.banco)into v_prueba;
            end if;
            
            if (v_registro.tipo = 'reemision'  and v_registro.procesado = 'no') then
            	select informix.f_modificar_datos_web_reemision( v_parametros.nro_boleto_reemision,v_parametros.nro_boleto) into v_prueba;
            end if;
               
			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Modificaciones Venta Web modificado(a)'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_venta_web_modificaciones',v_parametros.id_venta_web_modificaciones::varchar);
               
            --Devuelve la respuesta
            return v_resp;
            
		end;

	/*********************************    
 	#TRANSACCION:  'OBING_VWEBMOD_ELI'
 	#DESCRIPCION:	Eliminacion de registros
 	#AUTOR:		jrivera	
 	#FECHA:		11-01-2017 19:44:28
	***********************************/

	elsif(p_transaccion='OBING_VWEBMOD_ELI')then

		begin
        	select * into v_registro
            from obingresos.tventa_web_modificaciones
            where id_venta_web_modificaciones=v_parametros.id_venta_web_modificaciones;
            
        	if (v_registro.procesado = 'si') then
            	raise exception 'No es posible eliminar el registro porq ya fue procesado';
            end if;
			--Sentencia de la eliminacion
			delete from obingresos.tventa_web_modificaciones
            where id_venta_web_modificaciones=v_parametros.id_venta_web_modificaciones;
               
            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Modificaciones Venta Web eliminado(a)'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_venta_web_modificaciones',v_parametros.id_venta_web_modificaciones::varchar);
              
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