CREATE OR REPLACE FUNCTION obingresos.ft_periodo_venta_ime (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Ingresos
 FUNCION: 		obingresos.ft_periodo_venta_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'obingresos.tperiodo_venta'
 AUTOR: 		 (jrivera)
 FECHA:	        08-04-2016 22:44:37
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
	v_id_periodo_venta	integer;
	v_respuesta				varchar;
			    
BEGIN

    v_nombre_funcion = 'obingresos.ft_periodo_venta_ime';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************    
 	#TRANSACCION:  'OBING_PERVEN_INS'
 	#DESCRIPCION:	Insercion de registros
 	#AUTOR:		jrivera	
 	#FECHA:		08-04-2016 22:44:37
	***********************************/

	if(p_transaccion='OBING_PERVEN_INS')then
					
        begin
        	
        	if (v_parametros.tipo_periodo is not null)then
            
        		v_respuesta = obingresos.f_generar_periodo(v_parametros.id_pais,v_parametros.id_gestion,v_parametros.tipo,p_id_usuario,v_parametros.tipo_periodo);
        	else
        		if (exists (select 1 
        					from obingresos.tperiodo_venta tp
        					where  tp.estado_reg = 'activo' and 
        					tp.nro_periodo_mes = v_parametros.nro_periodo_mes and tp.mes = v_parametros.mes and
        					tp.id_pais = v_parametros.id_pais and td.id_gestion = v_parametros.id_gestion and
        					tp.tipo = v_parametros.tipo
        					)) then
        			raise exception 'Ya existe un registro con el mismo nro y el mismo mes';
        		end if;
        		
        		if (exists (select 1 
        					from obingresos.tperiodo_venta tp
        					where  tp.estado_reg = 'activo' and 
        					v_parametros.fecha_ini between (tp.fecha_ini and tp.fecha_fin) and
        					tp.id_pais = v_parametros.id_pais and td.id_gestion = v_parametros.id_gestion and
        					tp.tipo = v_parametros.tipo
        					)) then
        			raise exception 'El rango de fechas indicado se sobrepone con otro ya existente';
        		end if;
        		--Sentencia de la insercion
	        	insert into obingresos.tperiodo_venta(
				id_pais,
				id_gestion,
				mes,
				estado,
				nro_periodo_mes,
				fecha_fin,
				fecha_ini,
				tipo,
				estado_reg,
				id_usuario_ai,
				id_usuario_reg,
				usuario_ai,
				fecha_reg,
				fecha_mod,
				id_usuario_mod
	          	) values(
				v_parametros.id_pais,
				v_parametros.id_gestion,
				v_parametros.mes,
				v_parametros.estado,
				v_parametros.nro_periodo_mes,
				v_parametros.fecha_fin,
				v_parametros.fecha_ini,
				v_parametros.tipo,
				'activo',
				v_parametros._id_usuario_ai,
				p_id_usuario,
				v_parametros._nombre_usuario_ai,
				now(),
				null,
				null
								
				
				
				)RETURNING id_periodo_venta into v_id_periodo_venta;
        		
        	end if;
        	
			
			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Periodo de Venta almacenado(a) con exito (id_periodo_venta'||v_id_periodo_venta||')'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_periodo_venta',v_id_periodo_venta::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************    
 	#TRANSACCION:  'OBING_PERVEN_MOD'
 	#DESCRIPCION:	Modificacion de registros
 	#AUTOR:		jrivera	
 	#FECHA:		08-04-2016 22:44:37
	***********************************/

	elsif(p_transaccion='OBING_PERVEN_MOD')then

		begin
		
			if (exists (select 1 
    					from obingresos.tperiodo_venta tp
    					where  tp.estado_reg = 'activo' and 
    					tp.nro_periodo_mes = v_parametros.nro_periodo_mes and tp.mes = v_parametros.mes and
    					tp.id_pais = v_parametros.id_pais and td.id_gestion = v_parametros.id_gestion and
    					tp.tipo = v_parametros.tipo and tp.id_periodo_venta != v_parametros.id_periodo_venta
    					)) then
    			raise exception 'Ya existe un registro con el mismo nro y el mismo mes';
    		end if;
    		
    		if (exists (select 1 
    					from obingresos.tperiodo_venta tp
    					where  tp.estado_reg = 'activo' and 
    					v_parametros.fecha_ini between (tp.fecha_ini and tp.fecha_fin) and
    					tp.id_pais = v_parametros.id_pais and td.id_gestion = v_parametros.id_gestion and
    					tp.tipo = v_parametros.tipo and tp.id_periodo_venta != v_parametros.id_periodo_venta
    					)) then
    			raise exception 'El rango de fechas indicado se sobrepone con otro ya existente';
    		end if;
    		
			--Sentencia de la modificacion
			update obingresos.tperiodo_venta set
			id_pais = v_parametros.id_pais,
			id_gestion = v_parametros.id_gestion,
			mes = v_parametros.mes,
			estado = v_parametros.estado,
			nro_periodo_mes = v_parametros.nro_periodo_mes,
			fecha_fin = v_parametros.fecha_fin,
			fecha_ini = v_parametros.fecha_ini,
			tipo = v_parametros.tipo,
			fecha_mod = now(),
			id_usuario_mod = p_id_usuario,
			id_usuario_ai = v_parametros._id_usuario_ai,
			usuario_ai = v_parametros._nombre_usuario_ai
			where id_periodo_venta=v_parametros.id_periodo_venta;
               
			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Periodo de Venta modificado(a)'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_periodo_venta',v_parametros.id_periodo_venta::varchar);
               
            --Devuelve la respuesta
            return v_resp;
            
		end;

	/*********************************    
 	#TRANSACCION:  'OBING_PERVEN_ELI'
 	#DESCRIPCION:	Eliminacion de registros
 	#AUTOR:		jrivera	
 	#FECHA:		08-04-2016 22:44:37
	***********************************/

	elsif(p_transaccion='OBING_PERVEN_ELI')then

		begin
			--Sentencia de la eliminacion
			delete from obingresos.tperiodo_venta
            where id_periodo_venta=v_parametros.id_periodo_venta;
               
            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Periodo de Venta eliminado(a)'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_periodo_venta',v_parametros.id_periodo_venta::varchar);
              
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