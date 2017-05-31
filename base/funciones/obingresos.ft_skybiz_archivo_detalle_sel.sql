CREATE OR REPLACE FUNCTION "obingresos"."ft_skybiz_archivo_detalle_sel"(	
				p_administrador integer, p_id_usuario integer, p_tabla character varying, p_transaccion character varying)
RETURNS character varying AS
$BODY$
/**************************************************************************
 SISTEMA:		Ingresos
 FUNCION: 		obingresos.ft_skybiz_archivo_detalle_sel
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'obingresos.tskybiz_archivo_detalle'
 AUTOR: 		 (admin)
 FECHA:	        15-02-2017 19:08:58
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

	v_nombre_funcion = 'obingresos.ft_skybiz_archivo_detalle_sel';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************    
 	#TRANSACCION:  'OBING_SKYDET_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		admin	
 	#FECHA:		15-02-2017 19:08:58
	***********************************/

	if(p_transaccion='OBING_SKYDET_SEL')then
     				
    	begin
    		--Sentencia de la consulta
			v_consulta:='select
						skydet.id_skybiz_archivo_detalle,
						skydet.entity,
						skydet.request_date_time,
						skydet.currency,
						skydet.total_amount,
						skydet.ip,
						skydet.status,
						skydet.estado_reg,
						skydet.issue_date_time,
						skydet.identifier_pnr,
						skydet.id_skybiz_archivo,
						skydet.pnr,
						skydet.authorization_,
						skydet.id_usuario_ai,
						skydet.usuario_ai,
						skydet.fecha_reg,
						skydet.id_usuario_reg,
						skydet.id_usuario_mod,
						skydet.fecha_mod,
						usu1.cuenta as usr_reg,
						usu2.cuenta as usr_mod	
						from obingresos.tskybiz_archivo_detalle skydet
						inner join segu.tusuario usu1 on usu1.id_usuario = skydet.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = skydet.id_usuario_mod
				        where  ';
			
			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

			--Devuelve la respuesta
			return v_consulta;
						
		end;

	/*********************************    
 	#TRANSACCION:  'OBING_SKYDET_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		admin	
 	#FECHA:		15-02-2017 19:08:58
	***********************************/

	elsif(p_transaccion='OBING_SKYDET_CONT')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:='select count(id_skybiz_archivo_detalle)
					    from obingresos.tskybiz_archivo_detalle skydet
					    inner join segu.tusuario usu1 on usu1.id_usuario = skydet.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = skydet.id_usuario_mod
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
ALTER FUNCTION "obingresos"."ft_skybiz_archivo_detalle_sel"(integer, integer, character varying, character varying) OWNER TO postgres;
