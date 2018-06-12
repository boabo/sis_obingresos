CREATE OR REPLACE FUNCTION "obingresos"."ft_reporte_cuenta_sel"(	
				p_administrador integer, p_id_usuario integer, p_tabla character varying, p_transaccion character varying)
RETURNS character varying AS
$BODY$
/**************************************************************************
 SISTEMA:		Ingresos
 FUNCION: 		obingresos.ft_reporte_cuenta_sel
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'obingresos.treporte_cuenta'
 AUTOR: 		 (miguel.mamani)
 FECHA:	        11-06-2018 15:14:58
 COMENTARIOS:	
***************************************************************************
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				11-06-2018 15:14:58								Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'obingresos.treporte_cuenta'	
 #
 ***************************************************************************/

DECLARE

	v_consulta    		varchar;
	v_parametros  		record;
	v_nombre_funcion   	text;
	v_resp				varchar;
			    
BEGIN

	v_nombre_funcion = 'obingresos.ft_reporte_cuenta_sel';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************    
 	#TRANSACCION:  'OBING_ent_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		miguel.mamani	
 	#FECHA:		11-06-2018 15:14:58
	***********************************/

	if(p_transaccion='OBING_ent_SEL')then
     				
    	begin
    		--Sentencia de la consulta
			v_consulta:='select
						ent.id_reporte,
						ent.nombre,
						ent.neto,
						ent.fecha,
						ent.comision,
						ent.autorizacion__nro_deposito,
						ent.id_agencia,
						ent.pnr,
						ent.billete,
						ent.importe,
						ent.tipo,
						usu1.cuenta as usr_reg,
						usu2.cuenta as usr_mod	
						from obingresos.treporte_cuenta ent
						inner join segu.tusuario usu1 on usu1.id_usuario = ent.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = ent.id_usuario_mod
				        where  ';
			
			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

			--Devuelve la respuesta
			return v_consulta;
						
		end;

	/*********************************    
 	#TRANSACCION:  'OBING_ent_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		miguel.mamani	
 	#FECHA:		11-06-2018 15:14:58
	***********************************/

	elsif(p_transaccion='OBING_ent_CONT')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:='select count(id_reporte)
					    from obingresos.treporte_cuenta ent
					    inner join segu.tusuario usu1 on usu1.id_usuario = ent.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = ent.id_usuario_mod
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
ALTER FUNCTION "obingresos"."ft_reporte_cuenta_sel"(integer, integer, character varying, character varying) OWNER TO postgres;
