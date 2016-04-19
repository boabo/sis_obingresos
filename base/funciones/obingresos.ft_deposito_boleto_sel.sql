CREATE OR REPLACE FUNCTION "obingresos"."ft_deposito_boleto_sel"(	
				p_administrador integer, p_id_usuario integer, p_tabla character varying, p_transaccion character varying)
RETURNS character varying AS
$BODY$
/**************************************************************************
 SISTEMA:		Ingresos
 FUNCION: 		obingresos.ft_deposito_boleto_sel
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'obingresos.tdeposito_boleto'
 AUTOR: 		 (jrivera)
 FECHA:	        06-01-2016 22:42:31
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

	v_nombre_funcion = 'obingresos.ft_deposito_boleto_sel';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************    
 	#TRANSACCION:  'OBING_DEPBOL_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		jrivera	
 	#FECHA:		06-01-2016 22:42:31
	***********************************/

	if(p_transaccion='OBING_DEPBOL_SEL')then
     				
    	begin
    		--Sentencia de la consulta
			v_consulta:='select
						depbol.id_deposito_boleto,
						depbol.id_boleto,
						depbol.id_deposito,
						depbol.tc,
						depbol.estado_reg,
						depbol.monto_moneda_boleto,
						depbol.monto_moneda_deposito,
						depbol.id_usuario_reg,
						depbol.fecha_reg,
						depbol.usuario_ai,
						depbol.id_usuario_ai,
						depbol.fecha_mod,
						depbol.id_usuario_mod,
						usu1.cuenta as usr_reg,
						usu2.cuenta as usr_mod,
						bol.nro_boleto,
						monbol.codigo as moneda_boleto,
						dep.nro_deposito,
						mondep.codigo as moneda_deposito
						from obingresos.tdeposito_boleto depbol
						inner join segu.tusuario usu1 on usu1.id_usuario = depbol.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = depbol.id_usuario_mod
						inner join obingresos.tboleto bol on bol.id_boleto = depbol.id_boleto
						inner join obingresos.tdeposito dep on dep.id_deposito = depbol.id_deposito
						inner join param.tmoneda monbol on monbol.id_moneda = bol.id_moneda_boleto
						inner join param.tmoneda mondep on mondep.id_moneda = dep.id_moneda_deposito
						
				        where  ';
			
			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

			--Devuelve la respuesta
			return v_consulta;
						
		end;

	/*********************************    
 	#TRANSACCION:  'OBING_DEPBOL_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		jrivera	
 	#FECHA:		06-01-2016 22:42:31
	***********************************/

	elsif(p_transaccion='OBING_DEPBOL_CONT')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:='select count(id_deposito_boleto)
					    from obingresos.tdeposito_boleto depbol
					    inner join segu.tusuario usu1 on usu1.id_usuario = depbol.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = depbol.id_usuario_mod
						inner join obingresos.tboleto bol on bol.id_boleto = depbol.id_boleto
						inner join obingresos.tdeposito dep on dep.id_deposito = depbol.id_deposito
						inner join param.tmoneda monbol on monbol.id_moneda = bol.id_moneda_boleto
						inner join param.tmoneda mondep on mondep.id_moneda = dep.id_moneda_deposito
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
ALTER FUNCTION "obingresos"."ft_deposito_boleto_sel"(integer, integer, character varying, character varying) OWNER TO postgres;
