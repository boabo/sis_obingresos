CREATE OR REPLACE FUNCTION "obingresos"."ft_periodo_venta_sel"(	
				p_administrador integer, p_id_usuario integer, p_tabla character varying, p_transaccion character varying)
RETURNS character varying AS
$BODY$
/**************************************************************************
 SISTEMA:		Ingresos
 FUNCION: 		obingresos.ft_periodo_venta_sel
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'obingresos.tperiodo_venta'
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

	v_consulta    		varchar;
	v_parametros  		record;
	v_nombre_funcion   	text;
	v_resp				varchar;
			    
BEGIN

	v_nombre_funcion = 'obingresos.ft_periodo_venta_sel';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************    
 	#TRANSACCION:  'OBING_PERVEN_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		jrivera	
 	#FECHA:		08-04-2016 22:44:37
	***********************************/

	if(p_transaccion='OBING_PERVEN_SEL')then
     				
    	begin
    		--Sentencia de la consulta
			v_consulta:='select
						perven.id_periodo_venta,
						perven.id_pais,
						perven.id_gestion,
						perven.mes,
						perven.estado,
						perven.nro_periodo_mes,
						perven.fecha_fin,
						perven.fecha_ini,
						perven.tipo,
						perven.estado_reg,
						perven.id_usuario_ai,
						perven.id_usuario_reg,
						perven.usuario_ai,
						perven.fecha_reg,
						perven.fecha_mod,
						perven.id_usuario_mod,
						usu1.cuenta as usr_reg,
						usu2.cuenta as usr_mod	
						from obingresos.tperiodo_venta perven
						inner join segu.tusuario usu1 on usu1.id_usuario = perven.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = perven.id_usuario_mod
				        where  ';
			
			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

			--Devuelve la respuesta
			return v_consulta;
						
		end;

	/*********************************    
 	#TRANSACCION:  'OBING_PERVEN_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		jrivera	
 	#FECHA:		08-04-2016 22:44:37
	***********************************/

	elsif(p_transaccion='OBING_PERVEN_CONT')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:='select count(id_periodo_venta)
					    from obingresos.tperiodo_venta perven
					    inner join segu.tusuario usu1 on usu1.id_usuario = perven.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = perven.id_usuario_mod
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
ALTER FUNCTION "obingresos"."ft_periodo_venta_sel"(integer, integer, character varying, character varying) OWNER TO postgres;
