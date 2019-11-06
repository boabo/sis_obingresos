CREATE OR REPLACE FUNCTION obingresos.ft_forma_pago_pw_sel (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Ingresos
 FUNCION: 		obingresos.ft_forma_pago_pw_sel
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'obingresos.tforma_pago_pw'
 AUTOR: 		 (admin)
 FECHA:	        04-06-2019 21:58:00
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				04-06-2019 21:58:00								Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'obingresos.tforma_pago_pw'
 #
 ***************************************************************************/

DECLARE

	v_consulta    		varchar;
	v_parametros  		record;
	v_nombre_funcion   	text;
	v_resp				varchar;

BEGIN

	v_nombre_funcion = 'obingresos.ft_forma_pago_pw_sel';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'OBING_FPPW_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		admin
 	#FECHA:		04-06-2019 21:58:00
	***********************************/

	if(p_transaccion='OBING_FPPW_SEL')then

    	begin
    		--Sentencia de la consulta
			v_consulta:='select
						fppw.id_forma_pago_pw,
						fppw.estado_reg,
						fppw.name,
						fppw.country_code,
						fppw.erp_code,
						fppw.fop_code,
						fppw.manage_account,
						fppw.id_usuario_reg,
						fppw.fecha_reg,
						fppw.id_usuario_ai,
						fppw.usuario_ai,
						fppw.id_usuario_mod,
						fppw.fecha_mod,
						usu1.cuenta as usr_reg,
						usu2.cuenta as usr_mod
						from obingresos.tforma_pago_pw fppw
						inner join segu.tusuario usu1 on usu1.id_usuario = fppw.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = fppw.id_usuario_mod
				        where  ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

			--Devuelve la respuesta
			return v_consulta;

		end;

	/*********************************
 	#TRANSACCION:  'OBING_FPPW_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		admin
 	#FECHA:		04-06-2019 21:58:00
	***********************************/

	elsif(p_transaccion='OBING_FPPW_CONT')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:='select count(id_forma_pago_pw)
					    from obingresos.tforma_pago_pw fppw
					    inner join segu.tusuario usu1 on usu1.id_usuario = fppw.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = fppw.id_usuario_mod
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

ALTER FUNCTION obingresos.ft_forma_pago_pw_sel (p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
  OWNER TO postgres;
