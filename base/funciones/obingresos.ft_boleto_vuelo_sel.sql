CREATE OR REPLACE FUNCTION obingresos.ft_boleto_vuelo_sel (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Ingresos
 FUNCION: 		obingresos.ft_boleto_vuelo_sel
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'obingresos.tboleto_vuelo'
 AUTOR: 		 (jrivera)
 FECHA:	        29-03-2017 10:59:33
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

	v_nombre_funcion = 'obingresos.ft_boleto_vuelo_sel';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************    
 	#TRANSACCION:  'OBING_BVU_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		jrivera	
 	#FECHA:		29-03-2017 10:59:33
	***********************************/

	if(p_transaccion='OBING_BVU_SEL')then
     				
    	begin
    		--Sentencia de la consulta
			v_consulta:='select
						bvu.id_boleto_vuelo,
						bvu.id_aeropuerto_destino,
						bvu.id_aeropuerto_origen,
						bvu.fecha_hora_origen,
						bvu.id_boleto_conjuncion,
						bvu.linea,
						bvu.estado_reg,
						bvu.vuelo,
						bvu.fecha,
						bvu.hora_destino,
						bvu.status,
						bvu.equipaje,
						bvu.hora_origen,
						bvu.retorno,
						bvu.fecha_hora_destino,
						bvu.tiempo_conexion,
						bvu.cupon,
						bvu.id_boleto,
						bvu.aeropuerto_origen,
						bvu.aeropuerto_destino,
						bvu.tarifa,
						bvu.usuario_ai,
						bvu.fecha_reg,
						bvu.id_usuario_reg,
						bvu.id_usuario_ai,
						bvu.id_usuario_mod,
						bvu.fecha_mod,
						usu1.cuenta as usr_reg,
						usu2.cuenta as usr_mod,
							(bvu.aeropuerto_origen || ''-'' || bvu.aeropuerto_destino)::varchar as boleto_vuelo
						from obingresos.tboleto_vuelo bvu
						inner join segu.tusuario usu1 on usu1.id_usuario = bvu.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = bvu.id_usuario_mod
				        where  ';
			
			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

			--Devuelve la respuesta
			return v_consulta;
						
		end;

	/*********************************    
 	#TRANSACCION:  'OBING_BVU_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		jrivera	
 	#FECHA:		29-03-2017 10:59:33
	***********************************/

	elsif(p_transaccion='OBING_BVU_CONT')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:='select count(id_boleto_vuelo)
					    from obingresos.tboleto_vuelo bvu
					    inner join segu.tusuario usu1 on usu1.id_usuario = bvu.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = bvu.id_usuario_mod
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