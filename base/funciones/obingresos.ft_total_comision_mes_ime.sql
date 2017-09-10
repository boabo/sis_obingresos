CREATE OR REPLACE FUNCTION "obingresos"."ft_total_comision_mes_ime" (	
				p_administrador integer, p_id_usuario integer, p_tabla character varying, p_transaccion character varying)
RETURNS character varying AS
$BODY$

/**************************************************************************
 SISTEMA:		Ingresos
 FUNCION: 		obingresos.ft_total_comision_mes_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'obingresos.ttotal_comision_mes'
 AUTOR: 		 (jrivera)
 FECHA:	        17-08-2017 21:28:24
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
	v_id_total_comision_mes	integer;
			    
BEGIN

    v_nombre_funcion = 'obingresos.ft_total_comision_mes_ime';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************    
 	#TRANSACCION:  'OBING_TOTFAC_INS'
 	#DESCRIPCION:	Insercion de registros
 	#AUTOR:		jrivera	
 	#FECHA:		17-08-2017 21:28:24
	***********************************/

	if(p_transaccion='OBING_TOTFAC_INS')then
					
        begin
        	--Sentencia de la insercion
        	insert into obingresos.ttotal_comision_mes(
			gestion,
			estado,
			max_fecha_fin_periodo,
			periodo,
			total_comision,
			id_periodos,
			estado_reg,
			id_tipo_periodo,
			id_usuario_ai,
			id_usuario_reg,
			fecha_reg,
			usuario_ai,
			fecha_mod,
			id_usuario_mod
          	) values(
			v_parametros.gestion,
			v_parametros.estado,
			v_parametros.max_fecha_fin_periodo,
			v_parametros.periodo,
			v_parametros.total_comision,
			v_parametros.id_periodos,
			'activo',
			v_parametros.id_tipo_periodo,
			v_parametros._id_usuario_ai,
			p_id_usuario,
			now(),
			v_parametros._nombre_usuario_ai,
			null,
			null
							
			
			
			)RETURNING id_total_comision_mes into v_id_total_comision_mes;
			
			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Facturas por Mes almacenado(a) con exito (id_total_comision_mes'||v_id_total_comision_mes||')'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_total_comision_mes',v_id_total_comision_mes::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************    
 	#TRANSACCION:  'OBING_TOTFAC_MOD'
 	#DESCRIPCION:	Modificacion de registros
 	#AUTOR:		jrivera	
 	#FECHA:		17-08-2017 21:28:24
	***********************************/

	elsif(p_transaccion='OBING_TOTFAC_MOD')then

		begin
			--Sentencia de la modificacion
			update obingresos.ttotal_comision_mes set
			gestion = v_parametros.gestion,
			estado = v_parametros.estado,
			max_fecha_fin_periodo = v_parametros.max_fecha_fin_periodo,
			periodo = v_parametros.periodo,
			total_comision = v_parametros.total_comision,
			id_periodos = v_parametros.id_periodos,
			id_tipo_periodo = v_parametros.id_tipo_periodo,
			fecha_mod = now(),
			id_usuario_mod = p_id_usuario,
			id_usuario_ai = v_parametros._id_usuario_ai,
			usuario_ai = v_parametros._nombre_usuario_ai
			where id_total_comision_mes=v_parametros.id_total_comision_mes;
               
			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Facturas por Mes modificado(a)'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_total_comision_mes',v_parametros.id_total_comision_mes::varchar);
               
            --Devuelve la respuesta
            return v_resp;
            
		end;

	/*********************************    
 	#TRANSACCION:  'OBING_VALCOMMES_MOD'
 	#DESCRIPCION:	Validacion factura de cosmision
 	#AUTOR:		jrivera	
 	#FECHA:		17-08-2017 21:28:24
	***********************************/

	elsif(p_transaccion='OBING_VALCOMMES_MOD')then

		begin
			--Sentencia de la eliminacion
			update obingresos.ttotal_comision_mes
			set estado = 'validada'
            where id_total_comision_mes=v_parametros.id_total_comision_mes;
               
            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Facturas por Mes validada');
            v_resp = pxp.f_agrega_clave(v_resp,'id_total_comision_mes',v_parametros.id_total_comision_mes::varchar);
              
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
ALTER FUNCTION "obingresos"."ft_total_comision_mes_ime"(integer, integer, character varying, character varying) OWNER TO postgres;
