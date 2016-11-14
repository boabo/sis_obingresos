CREATE OR REPLACE FUNCTION obingresos.ft_forma_pago_sel (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Ingresos
 FUNCION: 		obingresos.ft_forma_pago_sel
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'obingresos.tforma_pago'
 AUTOR: 		 (jrivera)
 FECHA:	        10-06-2016 20:37:45
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

	v_nombre_funcion = 'obingresos.ft_forma_pago_sel';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************    
 	#TRANSACCION:  'OBING_FOP_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		jrivera	
 	#FECHA:		10-06-2016 20:37:45
	***********************************/

	if(p_transaccion='OBING_FOP_SEL')then
     				
    	begin
    		--Sentencia de la consulta
			v_consulta:='select
						fop.id_forma_pago,						
						fop.codigo,						
						fop.nombre,
                        mon.codigo_internacional as moneda,                        
						lug.nombre as pais,
                        (fop.nombre || '' - '' || mon.codigo_internacional)::varchar as forma_pago
                        						
						from obingresos.tforma_pago fop
						inner join segu.tusuario usu1 on usu1.id_usuario = fop.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = fop.id_usuario_mod
						inner join param.tmoneda mon on mon.id_moneda = fop.id_moneda
						inner join param.tlugar lug on lug.id_lugar = fop.id_lugar
				        where  ';
			
			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

			--Devuelve la respuesta
			return v_consulta;
						
		end;

	/*********************************    
 	#TRANSACCION:  'OBING_FOP_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		jrivera	
 	#FECHA:		10-06-2016 20:37:45
	***********************************/

	elsif(p_transaccion='OBING_FOP_CONT')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:='select count(id_forma_pago)
					    from obingresos.tforma_pago fop
					    inner join segu.tusuario usu1 on usu1.id_usuario = fop.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = fop.id_usuario_mod
						inner join param.tmoneda mon on mon.id_moneda = fop.id_moneda
						inner join param.tlugar lug on lug.id_lugar = fop.id_lugar
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