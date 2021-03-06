CREATE OR REPLACE FUNCTION obingresos.ft_boleto_impuesto_sel (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Ingresos
 FUNCION: 		obingresos.ft_boleto_impuesto_sel
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'obingresos.tboleto_impuesto'
 AUTOR: 		 (jrivera)
 FECHA:	        13-06-2016 20:42:17
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

	v_nombre_funcion = 'obingresos.ft_boleto_impuesto_sel';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************    
 	#TRANSACCION:  'OBING_BIT_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		jrivera	
 	#FECHA:		13-06-2016 20:42:17
	***********************************/

	if(p_transaccion='OBING_BIT_SEL')then
     				
    	begin
    		--Sentencia de la consulta
			v_consulta:='select
						bit.id_boleto_impuesto,
						bit.importe,
						bit.id_impuesto,
						bit.id_boleto,
						bit.estado_reg,
						bit.id_usuario_ai,
						bit.usuario_ai,
						bit.fecha_reg,
						bit.id_usuario_reg,
						bit.fecha_mod,
						bit.id_usuario_mod,
						usu1.cuenta as usr_reg,
						usu2.cuenta as usr_mod,
                        imp.codigo as codigo_impuesto,
                        imp.nombre as nombre_impuesto	
						from obingresos.tboleto_impuesto bit
						inner join segu.tusuario usu1 on usu1.id_usuario = bit.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = bit.id_usuario_mod
				        inner join obingresos.timpuesto imp on imp.id_impuesto = bit.id_impuesto
                        where  ';
			
			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

			--Devuelve la respuesta
			return v_consulta;
						
		end;

	/*********************************    
 	#TRANSACCION:  'OBING_BIT_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		jrivera	
 	#FECHA:		13-06-2016 20:42:17
	***********************************/

	elsif(p_transaccion='OBING_BIT_CONT')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:='select count(id_boleto_impuesto)
					    from obingresos.tboleto_impuesto bit
					    inner join segu.tusuario usu1 on usu1.id_usuario = bit.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = bit.id_usuario_mod
                        inner join obingresos.timpuesto imp on imp.id_impuesto = bit.id_impuesto
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
$body$
LANGUAGE 'plpgsql'
VOLATILE
CALLED ON NULL INPUT
SECURITY INVOKER
COST 100;