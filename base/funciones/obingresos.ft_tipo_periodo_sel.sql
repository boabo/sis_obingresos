CREATE OR REPLACE FUNCTION obingresos.ft_tipo_periodo_sel (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Ingresos
 FUNCION: 		obingresos.ft_tipo_periodo_sel
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'obingresos.ttipo_periodo'
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

	v_consulta    		varchar;
	v_parametros  		record;
	v_nombre_funcion   	text;
	v_resp				varchar;
			    
BEGIN

	v_nombre_funcion = 'obingresos.ft_tipo_periodo_sel';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************    
 	#TRANSACCION:  'OBING_TIPER_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		jrivera	
 	#FECHA:		08-05-2017 20:02:14
	***********************************/

	if(p_transaccion='OBING_TIPER_SEL')then
     				
    	begin
    		--Sentencia de la consulta
			v_consulta:='select
						tiper.id_tipo_periodo,
						tiper.pago_comision,
						tiper.tipo,
						tiper.estado,
						tiper.estado_reg,
						tiper.medio_pago,
						tiper.tiempo,
						tiper.tipo_cc,
						tiper.id_usuario_reg,
						tiper.fecha_reg,
						tiper.usuario_ai,
						tiper.id_usuario_ai,
						tiper.fecha_mod,
						tiper.id_usuario_mod,
						usu1.cuenta as usr_reg,
						usu2.cuenta as usr_mod,
                        tiper.fecha_ini_primer_periodo	
						from obingresos.ttipo_periodo tiper
						inner join segu.tusuario usu1 on usu1.id_usuario = tiper.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = tiper.id_usuario_mod
				        where  ';
			
			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

			--Devuelve la respuesta
			return v_consulta;
						
		end;

	/*********************************    
 	#TRANSACCION:  'OBING_TIPER_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		jrivera	
 	#FECHA:		08-05-2017 20:02:14
	***********************************/

	elsif(p_transaccion='OBING_TIPER_CONT')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:='select count(id_tipo_periodo)
					    from obingresos.ttipo_periodo tiper
					    inner join segu.tusuario usu1 on usu1.id_usuario = tiper.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = tiper.id_usuario_mod
					    where ';
			
			--Definicion de la respuesta		    
			v_consulta:=v_consulta||v_parametros.filtro;

			--Devuelve la respuesta
			return v_consulta;

		end;
    /*********************************    
 	#TRANSACCION:  'OBING_TIPERXFP_SEL'
 	#DESCRIPCION:	Obtener tipo de periodo por forma de pago
 	#AUTOR:		jrivera	
 	#FECHA:		08-05-2017 20:02:14
	***********************************/

	elsif(p_transaccion='OBING_TIPERXFP_SEL')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:='select id_tipo_periodo, tiempo,
            			(case when tiper.medio_pago = ''cuenta_corriente'' then
                        	tiper.tipo_cc
                        else
                        	tiper.medio_pago
                        end)::varchar
					    from obingresos.ttipo_periodo tiper					    
					    where estado_reg = ''activo'' and estado = ''activo'' and tiper.tipo = ''portal'' and
                        (tiper.medio_pago = ANY(string_to_array(''' || v_parametros.formas_pago || ''','','')) or 
                        (tiper.medio_pago = ''cuenta_corriente'' and tiper.tipo_cc = ANY(string_to_array(''' || v_parametros.formas_pago || ''','','')) ))';
			
			
			--Devuelve la respuesta
			return v_consulta;

		end;
					
	else
					     
		raise exception 'Transaccion inexistente';
					         
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