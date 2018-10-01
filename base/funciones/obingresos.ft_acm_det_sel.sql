CREATE OR REPLACE FUNCTION obingresos.ft_acm_det_sel (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Ingresos
 FUNCION: 		obingresos.ft_acm_det_sel
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'obingresos.tacm_det'
 AUTOR: 		 (ivaldivia)
 FECHA:	        05-09-2018 20:52:05
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				05-09-2018 20:52:05								Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'obingresos.tacm_det'
 #
 ***************************************************************************/

DECLARE

	v_consulta    		varchar;
	v_parametros  		record;
	v_nombre_funcion   	text;
	v_resp				varchar;

BEGIN

	v_nombre_funcion = 'obingresos.ft_acm_det_sel';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'OBING_ACMDET_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		ivaldivia
 	#FECHA:		05-09-2018 20:52:05
	***********************************/

	if(p_transaccion='OBING_ACMDET_SEL')then

    	begin
    		--Sentencia de la consulta
			v_consulta:='select
						acmdet.id_acm_det,
						acmdet.id_acm,
						acmdet.id_detalle_boletos_web,
						acmdet.neto,
						acmdet.over_comision,
						acmdet.estado_reg,
						acmdet.id_usuario_ai,
						acmdet.usuario_ai,
						acmdet.fecha_reg,
						acmdet.id_usuario_reg,
						acmdet.id_usuario_mod,
						acmdet.fecha_mod,
                        acmdet.com_bsp,
                        acmdet.moneda,
                        acmdet.td,
                        acmdet.porcentaje_over,
						usu1.cuenta as usr_reg,
						usu2.cuenta as usr_mod,
                        bole.billete
						from obingresos.tacm_det acmdet
						inner join segu.tusuario usu1 on usu1.id_usuario = acmdet.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = acmdet.id_usuario_mod
                        inner join obingresos.tdetalle_boletos_web bole on bole.id_detalle_boletos_web = acmdet.id_detalle_boletos_web
				        where  ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

			--Devuelve la respuesta
			return v_consulta;

		end;

	/*********************************
 	#TRANSACCION:  'OBING_ACMDET_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		ivaldivia
 	#FECHA:		05-09-2018 20:52:05
	***********************************/

	elsif(p_transaccion='OBING_ACMDET_CONT')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:='select count(id_acm_det),
            			sum(acmdet.over_comision),
                        sum(acmdet.neto),
                        sum(acmdet.com_bsp)
					    from obingresos.tacm_det acmdet
						inner join segu.tusuario usu1 on usu1.id_usuario = acmdet.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = acmdet.id_usuario_mod
                        inner join obingresos.tdetalle_boletos_web bole on bole.id_detalle_boletos_web = acmdet.id_detalle_boletos_web
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
