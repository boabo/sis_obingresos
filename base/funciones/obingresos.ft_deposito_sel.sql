CREATE OR REPLACE FUNCTION "obingresos"."ft_deposito_sel"(	
				p_administrador integer, p_id_usuario integer, p_tabla character varying, p_transaccion character varying)
RETURNS character varying AS
$BODY$
/**************************************************************************
 SISTEMA:		Ingresos
 FUNCION: 		obingresos.ft_deposito_sel
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'obingresos.tdeposito'
 AUTOR: 		 (jrivera)
 FECHA:	        06-01-2016 22:42:28
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

	v_nombre_funcion = 'obingresos.ft_deposito_sel';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************    
 	#TRANSACCION:  'OBING_DEP_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		jrivera	
 	#FECHA:		06-01-2016 22:42:28
	***********************************/

	if(p_transaccion='OBING_DEP_SEL')then
     				
    	begin
    		--Sentencia de la consulta
			v_consulta:='select
						dep.id_deposito,
						dep.estado_reg,
						dep.nro_deposito,
						dep.monto_deposito,
						dep.id_moneda_deposito,
						dep.id_agencia,
						dep.fecha,
						dep.saldo,
						dep.id_usuario_reg,
						dep.fecha_reg,
						dep.id_usuario_ai,
						dep.usuario_ai,
						dep.id_usuario_mod,
						dep.fecha_mod,
						usu1.cuenta as usr_reg,
						usu2.cuenta as usr_mod,
						mon.codigo_internacional as desc_moneda		
						from obingresos.tdeposito dep
						inner join segu.tusuario usu1 on usu1.id_usuario = dep.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = dep.id_usuario_mod
						inner join param.tmoneda mon on mon.id_moneda = dep.id_moneda_deposito
				        where  ';
			
			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

			--Devuelve la respuesta
			return v_consulta;
						
		end;

	/*********************************    
 	#TRANSACCION:  'OBING_DEP_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		jrivera	
 	#FECHA:		06-01-2016 22:42:28
	***********************************/

	elsif(p_transaccion='OBING_DEP_CONT')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:='select count(id_deposito)
					    from obingresos.tdeposito dep
					    inner join segu.tusuario usu1 on usu1.id_usuario = dep.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = dep.id_usuario_mod
						inner join param.tmoneda mon on mon.id_moneda = dep.id_moneda_deposito
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
ALTER FUNCTION "obingresos"."ft_deposito_sel"(integer, integer, character varying, character varying) OWNER TO postgres;
