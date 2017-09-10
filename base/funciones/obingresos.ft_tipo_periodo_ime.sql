CREATE OR REPLACE FUNCTION obingresos.ft_tipo_periodo_ime (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Ingresos
 FUNCION: 		obingresos.ft_tipo_periodo_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'obingresos.ttipo_periodo'
 AUTOR: 		 (jrivera)
 FECHA:	        08-05-2017 20:02:14
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
	v_id_tipo_periodo	integer;
    v_fecha_fin			date;
			    
BEGIN

    v_nombre_funcion = 'obingresos.ft_tipo_periodo_ime';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************    
 	#TRANSACCION:  'OBING_TIPER_INS'
 	#DESCRIPCION:	Insercion de registros
 	#AUTOR:		jrivera	
 	#FECHA:		08-05-2017 20:02:14
	***********************************/

	if(p_transaccion='OBING_TIPER_INS')then
					
        begin
        	if (exists( 
            	select 1 from obingresos.ttipo_periodo 
            	where tipo = v_parametros.tipo and medio_pago = v_parametros.medio_pago and
                tipo_cc = v_parametros.tipo_cc and estado = 'activo')) then
                raise exception 'Existe un tipo de periodo similar definido que se encuentra activo';
            end if;
            if (v_parametros.fecha_ini_primer_periodo is not null) THEN
            	if (exists (select 1 from obingresos.tperiodo_venta pv
                			inner join obingresos.ttipo_periodo tp on pv.id_tipo_periodo = tp.id_tipo_periodo
                            where tp.tipo = v_parametros.tipo and tp.medio_pago = v_parametros.medio_pago and
                			tp.tipo_cc = v_parametros.tipo_cc)) then
                	raise exception 'No requiere definir la fecha inicio primer periodo ya que este tipo de periodo ya tiene periodos de venta generados';            
                end if;
            end if;
            
            
        	--Sentencia de la insercion
        	insert into obingresos.ttipo_periodo(
			pago_comision,
			tipo,
			estado,
			estado_reg,
			medio_pago,
			tiempo,
			tipo_cc,
			id_usuario_reg,
			fecha_reg,
			usuario_ai,
			id_usuario_ai,
			fecha_mod,
			id_usuario_mod,
            fecha_ini_primer_periodo
          	) values(
			v_parametros.pago_comision,
			v_parametros.tipo,
			'activo',
			'activo',
			v_parametros.medio_pago,
			v_parametros.tiempo,
			v_parametros.tipo_cc,
			p_id_usuario,
			now(),
			v_parametros._nombre_usuario_ai,
			v_parametros._id_usuario_ai,
			null,
			null,
            v_parametros.fecha_ini_primer_periodo
			)RETURNING id_tipo_periodo into v_id_tipo_periodo;
			
			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Tipo Periodo almacenado(a) con exito (id_tipo_periodo'||v_id_tipo_periodo||')'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_tipo_periodo',v_id_tipo_periodo::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************    
 	#TRANSACCION:  'OBING_TIPER_MOD'
 	#DESCRIPCION:	Modificacion de registros
 	#AUTOR:		jrivera	
 	#FECHA:		08-05-2017 20:02:14
	***********************************/

	elsif(p_transaccion='OBING_TIPER_MOD')then

		begin
			--Sentencia de la modificacion
			update obingresos.ttipo_periodo set
			pago_comision = v_parametros.pago_comision,
			tipo = v_parametros.tipo,
			estado = v_parametros.estado,
			medio_pago = v_parametros.medio_pago,
			tiempo = v_parametros.tiempo,
			tipo_cc = v_parametros.tipo_cc,
			fecha_mod = now(),
			id_usuario_mod = p_id_usuario,
			id_usuario_ai = v_parametros._id_usuario_ai,
			usuario_ai = v_parametros._nombre_usuario_ai,
            fecha_ini_primer_periodo = v_parametros.fecha_ini_primer_periodo
			where id_tipo_periodo=v_parametros.id_tipo_periodo;
               
			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Tipo Periodo modificado(a)'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_tipo_periodo',v_parametros.id_tipo_periodo::varchar);
               
            --Devuelve la respuesta
            return v_resp;
            
		end;

	/*********************************    
 	#TRANSACCION:  'OBING_TIPER_ELI'
 	#DESCRIPCION:	Eliminacion de registros
 	#AUTOR:		jrivera	
 	#FECHA:		08-05-2017 20:02:14
	***********************************/

	elsif(p_transaccion='OBING_TIPER_ELI')then

		begin
        
        	select max(fecha_fin) into v_fecha_fin
            from obingresos.tperiodo_venta pv
            where pv.id_tipo_periodo = v_parametros.id_tipo_periodo;
        	if (exists (
                select 1 
                from obingresos.tperiodo_venta pv
                where id_tipo_periodo = v_parametros.id_tipo_periodo and
                pv.fecha_fin = v_fecha_fin and 
                
                pv.fecha_fin != pxp.f_obtener_ultimo_dia_mes(
                						to_char(pv.fecha_fin,'MM')::"numeric",
                                        to_char(pv.fecha_fin,'YYYY')::"numeric"))) then
             	raise exception 'No se puede inactivar este tipo periodo debido a que no se generaron todos los periodos para el mes';
             end if;
			--Sentencia de la eliminacion
			update obingresos.ttipo_periodo
            set estado = 'inactivo',
            fecha_mod = now(),
			id_usuario_mod = p_id_usuario
            where id_tipo_periodo=v_parametros.id_tipo_periodo;
               
            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Tipo Periodo eliminado(a)'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_tipo_periodo',v_parametros.id_tipo_periodo::varchar);
              
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