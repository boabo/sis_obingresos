CREATE OR REPLACE FUNCTION "obingresos"."ft_agencia_sel"(	
				p_administrador integer, p_id_usuario integer, p_tabla character varying, p_transaccion character varying)
RETURNS character varying AS
$BODY$
/**************************************************************************
 SISTEMA:		Ingresos
 FUNCION: 		obingresos.ft_agencia_sel
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'obingresos.tagencia'
 AUTOR: 		 (jrivera)
 FECHA:	        06-01-2016 21:30:12
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

	v_nombre_funcion = 'obingresos.ft_agencia_sel';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************    
 	#TRANSACCION:  'OBING_AGE_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		jrivera	
 	#FECHA:		06-01-2016 21:30:12
	***********************************/

	if(p_transaccion='OBING_AGE_SEL')then
     				
    	begin
    		--Sentencia de la consulta
			v_consulta:='select
						age.id_agencia,
						age.id_moneda_control,
						age.depositos_moneda_boleto,
						age.tipo_pago,
						age.nombre,
						age.monto_maximo_deuda,
						age.tipo_cambio,
						age.codigo_int,
						age.codigo,
						age.tipo_agencia,
						usu1.cuenta as usr_reg,
						usu2.cuenta as usr_mod	,
						mon.codigo_internacional as desc_moneda
						from obingresos.tagencia age
						inner join segu.tusuario usu1 on usu1.id_usuario = age.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = age.id_usuario_mod
						inner join param.tmoneda mon on mon.id_moneda = age.id_moneda_control
				        where  ';
			
			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

			--Devuelve la respuesta
			return v_consulta;
						
		end;

	/*********************************    
 	#TRANSACCION:  'OBING_AGE_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		jrivera	
 	#FECHA:		06-01-2016 21:30:12
	***********************************/

	elsif(p_transaccion='OBING_AGE_CONT')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:='select count(id_agencia)
					    from obingresos.tagencia age
					    inner join segu.tusuario usu1 on usu1.id_usuario = age.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = age.id_usuario_mod
						inner join param.tmoneda mon on mon.id_moneda = age.id_moneda_control
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
ALTER FUNCTION "obingresos"."ft_agencia_sel"(integer, integer, character varying, character varying) OWNER TO postgres;
