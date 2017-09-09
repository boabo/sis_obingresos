CREATE OR REPLACE FUNCTION obingresos.ft_boleto_vuelo_ime (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Ingresos
 FUNCION: 		obingresos.ft_boleto_vuelo_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'obingresos.tboleto_vuelo'
 AUTOR: 		 (jrivera)
 FECHA:	        29-03-2017 10:59:33
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
	v_id_boleto_vuelo	integer;
			    
BEGIN

    v_nombre_funcion = 'obingresos.ft_boleto_vuelo_ime';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************    
 	#TRANSACCION:  'OBING_BVU_INS'
 	#DESCRIPCION:	Insercion de registros
 	#AUTOR:		jrivera	
 	#FECHA:		29-03-2017 10:59:33
	***********************************/

	if(p_transaccion='OBING_BVU_INS')then
					
        begin
        	--Sentencia de la insercion
        	insert into obingresos.tboleto_vuelo(
			id_aeropuerto_destino,
			id_aeropuerto_origen,
			fecha_hora_origen,
			id_boleto_conjuncion,
			linea,
			estado_reg,
			vuelo,
			fecha,
			hora_destino,
			status,
			equipaje,
			hora_origen,
			retorno,
			fecha_hora_destino,
			tiempo_conexion,
			cupon,
			id_boleto,
			aeropuerto_origen,
			aeropuerto_destino,
			tarifa,
			usuario_ai,
			fecha_reg,
			id_usuario_reg,
			id_usuario_ai,
			id_usuario_mod,
			fecha_mod
          	) values(
			v_parametros.id_aeropuerto_destino,
			v_parametros.id_aeropuerto_origen,
			v_parametros.fecha_hora_origen,
			v_parametros.id_boleto_conjuncion,
			v_parametros.linea,
			'activo',
			v_parametros.vuelo,
			v_parametros.fecha,
			v_parametros.hora_destino,
			v_parametros.status,
			v_parametros.equipaje,
			v_parametros.hora_origen,
			v_parametros.retorno,
			v_parametros.fecha_hora_destino,
			v_parametros.tiempo_conexion,
			v_parametros.cupon,
			v_parametros.id_boleto,
			v_parametros.aeropuerto_origen,
			v_parametros.aeropuerto_destino,
			v_parametros.tarifa,
			v_parametros._nombre_usuario_ai,
			now(),
			p_id_usuario,
			v_parametros._id_usuario_ai,
			null,
			null
							
			
			
			)RETURNING id_boleto_vuelo into v_id_boleto_vuelo;
			
			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Boleto Vuelo almacenado(a) con exito (id_boleto_vuelo'||v_id_boleto_vuelo||')'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_boleto_vuelo',v_id_boleto_vuelo::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************    
 	#TRANSACCION:  'OBING_BVU_MOD'
 	#DESCRIPCION:	Modificacion de registros
 	#AUTOR:		jrivera	
 	#FECHA:		29-03-2017 10:59:33
	***********************************/

	elsif(p_transaccion='OBING_BVU_MOD')then

		begin
			--Sentencia de la modificacion
			update obingresos.tboleto_vuelo set
			id_aeropuerto_destino = v_parametros.id_aeropuerto_destino,
			id_aeropuerto_origen = v_parametros.id_aeropuerto_origen,
			fecha_hora_origen = v_parametros.fecha_hora_origen,
			id_boleto_conjuncion = v_parametros.id_boleto_conjuncion,
			linea = v_parametros.linea,
			vuelo = v_parametros.vuelo,
			fecha = v_parametros.fecha,
			hora_destino = v_parametros.hora_destino,
			status = v_parametros.status,
			equipaje = v_parametros.equipaje,
			hora_origen = v_parametros.hora_origen,
			retorno = v_parametros.retorno,
			fecha_hora_destino = v_parametros.fecha_hora_destino,
			tiempo_conexion = v_parametros.tiempo_conexion,
			cupon = v_parametros.cupon,
			id_boleto = v_parametros.id_boleto,
			aeropuerto_origen = v_parametros.aeropuerto_origen,
			aeropuerto_destino = v_parametros.aeropuerto_destino,
			tarifa = v_parametros.tarifa,
			id_usuario_mod = p_id_usuario,
			fecha_mod = now(),
			id_usuario_ai = v_parametros._id_usuario_ai,
			usuario_ai = v_parametros._nombre_usuario_ai
			where id_boleto_vuelo=v_parametros.id_boleto_vuelo;
               
			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Boleto Vuelo modificado(a)'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_boleto_vuelo',v_parametros.id_boleto_vuelo::varchar);
               
            --Devuelve la respuesta
            return v_resp;
            
		end;

	/*********************************    
 	#TRANSACCION:  'OBING_BVU_ELI'
 	#DESCRIPCION:	Eliminacion de registros
 	#AUTOR:		jrivera	
 	#FECHA:		29-03-2017 10:59:33
	***********************************/

	elsif(p_transaccion='OBING_BVU_ELI')then

		begin
			--Sentencia de la eliminacion
			delete from obingresos.tboleto_vuelo
            where id_boleto_vuelo=v_parametros.id_boleto_vuelo;
               
            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Boleto Vuelo eliminado(a)'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_boleto_vuelo',v_parametros.id_boleto_vuelo::varchar);
              
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