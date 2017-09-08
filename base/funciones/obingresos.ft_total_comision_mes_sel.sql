CREATE OR REPLACE FUNCTION "obingresos"."ft_total_comision_mes_sel"(	
				p_administrador integer, p_id_usuario integer, p_tabla character varying, p_transaccion character varying)
RETURNS character varying AS
$BODY$
/**************************************************************************
 SISTEMA:		Ingresos
 FUNCION: 		obingresos.ft_total_comision_mes_sel
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'obingresos.ttotal_comision_mes'
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

	v_consulta    		varchar;
	v_parametros  		record;
	v_nombre_funcion   	text;
	v_resp				varchar;
			    
BEGIN

	v_nombre_funcion = 'obingresos.ft_total_comision_mes_sel';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************    
 	#TRANSACCION:  'OBING_TOTFAC_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		jrivera	
 	#FECHA:		17-08-2017 21:28:24
	***********************************/

	if(p_transaccion='OBING_TOTFAC_SEL')then
     				
    	begin
    		--Sentencia de la consulta
			v_consulta:='select
						totfac.id_total_comision_mes,
						totfac.gestion,
						totfac.estado,
						totfac.periodo,
						totfac.total_comision,
						totfac.id_periodos,
						totfac.estado_reg,
						totfac.id_tipo_periodo,
						totfac.id_usuario_ai,
						totfac.id_usuario_reg,
						totfac.fecha_reg,
						totfac.usuario_ai,
						totfac.fecha_mod,
						totfac.id_usuario_mod,
						usu1.cuenta as usr_reg,
						usu2.cuenta as usr_mod	,
                        age.codigo_int,
                        age.nombre,
                        tp.medio_pago
						from obingresos.ttotal_comision_mes totfac
                        inner join obingresos.tagencia age on age.id_agencia = totfac.id_agencia
                        inner join obingresos.ttipo_periodo tp on  tp.id_tipo_periodo = totfac.id_tipo_periodo  
						inner join segu.tusuario usu1 on usu1.id_usuario = totfac.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = totfac.id_usuario_mod
				        where  ';
			
			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

			--Devuelve la respuesta
			return v_consulta;
						
		end;

	/*********************************    
 	#TRANSACCION:  'OBING_TOTFAC_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		jrivera	
 	#FECHA:		17-08-2017 21:28:24
	***********************************/

	elsif(p_transaccion='OBING_TOTFAC_CONT')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:='select count(id_total_comision_mes)
					    from obingresos.ttotal_comision_mes totfac
					    inner join obingresos.tagencia age on age.id_agencia = totfac.id_agencia
                        inner join obingresos.ttipo_periodo tp on  tp.id_tipo_periodo = totfac.id_tipo_periodo  
                        inner join segu.tusuario usu1 on usu1.id_usuario = totfac.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = totfac.id_usuario_mod
					    where ';
			
			--Definicion de la respuesta		    
			v_consulta:=v_consulta||v_parametros.filtro;

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
$BODY$
LANGUAGE 'plpgsql' VOLATILE
COST 100;
ALTER FUNCTION "obingresos"."ft_total_comision_mes_sel"(integer, integer, character varying, character varying) OWNER TO postgres;
