CREATE OR REPLACE FUNCTION obingresos.ft_consulta_viajero_frecuente_ime (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Ingresos
 FUNCION: 		obingresos.ft_consulta_viajero_frecuente_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'obingresos.tconsulta_viajero_frecuente'
 AUTOR: 		 (miguel.mamani)
 FECHA:	        15-12-2017 14:59:25
 COMENTARIOS:	
***************************************************************************
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				15-12-2017 14:59:25								Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'obingresos.tconsulta_viajero_frecuente'	
 #
 ***************************************************************************/

DECLARE

	v_nro_requerimiento    	integer;
	v_parametros           	record;
	v_id_requerimiento     	integer;
	v_resp		            varchar;
	v_nombre_funcion        text;
	v_mensaje_error         text;
	v_id_consulta_viajero_frecuente	integer;
			    
BEGIN

    v_nombre_funcion = 'obingresos.ft_consulta_viajero_frecuente_ime';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************    
 	#TRANSACCION:  'OBING_VIF_INS'
 	#DESCRIPCION:	Insercion de registros
 	#AUTOR:		miguel.mamani	
 	#FECHA:		15-12-2017 14:59:25
	***********************************/

	if(p_transaccion='OBING_VIF_INS')then
					
        begin
        	--Sentencia de la insercion
        	insert into obingresos.tconsulta_viajero_frecuente(
			ffid,
			estado_reg,
			message,
			voucher_code,
			status,
			id_usuario_reg,
			fecha_reg,
			usuario_ai,
			id_usuario_ai,
			fecha_mod,
			id_usuario_mod
          	) values(
			v_parametros.ffid,
			'activo',
			v_parametros.message,
			'OB.FF.VO'||v_parametros.voucher_code,
			v_parametros.status,
			p_id_usuario,
			now(),
			v_parametros._nombre_usuario_ai,
			v_parametros._id_usuario_ai,
			null,
			null
							
			)RETURNING id_consulta_viajero_frecuente into v_id_consulta_viajero_frecuente;
			 
			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','consulta viajero frecuente almacenado(a) con exito (id_consulta_viajero_frecuente'||v_id_consulta_viajero_frecuente||')'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_consulta_viajero_frecuente',v_id_consulta_viajero_frecuente::varchar);
		
            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************    
 	#TRANSACCION:  'OBING_VIF_MOD'
 	#DESCRIPCION:	Modificacion de registros
 	#AUTOR:		miguel.mamani	
 	#FECHA:		15-12-2017 14:59:25
	***********************************/

	elsif(p_transaccion='OBING_VIF_MOD')then

		begin
			--Sentencia de la modificacion
			update obingresos.tconsulta_viajero_frecuente set
			ffid = v_parametros.ffid,
			message = v_parametros.message,
			voucher_code = 'OB.FF.VO'||v_parametros.voucher_code,
			status = v_parametros.status,
			fecha_mod = now(),
			id_usuario_mod = p_id_usuario,
			id_usuario_ai = v_parametros._id_usuario_ai,
			usuario_ai = v_parametros._nombre_usuario_ai
			where id_consulta_viajero_frecuente=v_parametros.id_consulta_viajero_frecuente;
               
			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','consulta viajero frecuente modificado(a)'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_consulta_viajero_frecuente',v_parametros.id_consulta_viajero_frecuente::varchar);
               
            --Devuelve la respuesta
            return v_resp;
            
		end;

	/*********************************    
 	#TRANSACCION:  'OBING_VIF_ELI'
 	#DESCRIPCION:	Eliminacion de registros
 	#AUTOR:		miguel.mamani	
 	#FECHA:		15-12-2017 14:59:25
	***********************************/

	elsif(p_transaccion='OBING_VIF_ELI')then

		begin
			--Sentencia de la eliminacion
			delete from obingresos.tconsulta_viajero_frecuente
            where id_consulta_viajero_frecuente=v_parametros.id_consulta_viajero_frecuente;
               
            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','consulta viajero frecuente eliminado(a)'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_consulta_viajero_frecuente',v_parametros.id_consulta_viajero_frecuente::varchar);
              
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