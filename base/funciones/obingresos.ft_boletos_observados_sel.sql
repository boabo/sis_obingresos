CREATE OR REPLACE FUNCTION obingresos.ft_boletos_observados_sel (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Ingresos
 FUNCION: 		obingresos.ft_boletos_observados_sel
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'obingresos.tboletos_observados'
 AUTOR: 		 (admin)
 FECHA:	        04-06-2019 19:39:16
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				04-06-2019 19:39:16								Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'obingresos.tboletos_observados'
 #
 ***************************************************************************/

DECLARE

	v_consulta    		varchar;
	v_parametros  		record;
	v_nombre_funcion   	text;
	v_resp				varchar;

BEGIN

	v_nombre_funcion = 'obingresos.ft_boletos_observados_sel';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'OBING_BOBS_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		admin
 	#FECHA:		04-06-2019 19:39:16
	***********************************/

	if(p_transaccion='OBING_BOBS_SEL')then

    	begin
    		--Sentencia de la consulta
			v_consulta:='select
						bobs.id_boletos_observados,
						bobs.estado_reg,
						bobs.pnr,
						bobs.nro_autorizacion,
						bobs.moneda,
						bobs.importe_total,
						bobs.fecha_emision,
						bobs.estado_p,
						bobs.forma_pago,
						bobs.medio_pago,
						bobs.instancia_pago,
						bobs.office_id_emisor,
						bobs.pnr_prov,
						bobs.nro_autorizacion_prov,
						bobs.office_id_emisor_prov,
						bobs.importe_prov,
						bobs.moneda_prov,
						bobs.estado_prov,
						bobs.fecha_autorizacion_prov,
						bobs.tipo_error,
						bobs.tipo_validacion,
						bobs.prov_informacion,
						--bobs.id_instancia_pago,
						bobs.id_usuario_reg,
						bobs.fecha_reg,
						bobs.id_usuario_ai,
						bobs.usuario_ai,
						bobs.id_usuario_mod,
						bobs.fecha_mod,
						usu1.cuenta as usr_reg,
						usu2.cuenta as usr_mod
						from obingresos.tboletos_observados bobs
						inner join segu.tusuario usu1 on usu1.id_usuario = bobs.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = bobs.id_usuario_mod
				        where  ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;
raise notice 'v_consulta: %', v_consulta;
			--Devuelve la respuesta
			return v_consulta;

		end;

	/*********************************
 	#TRANSACCION:  'OBING_BOBS_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		admin
 	#FECHA:		04-06-2019 19:39:16
	***********************************/

	elsif(p_transaccion='OBING_BOBS_CONT')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:='select count(id_boletos_observados)
					    from obingresos.tboletos_observados bobs
					    inner join segu.tusuario usu1 on usu1.id_usuario = bobs.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = bobs.id_usuario_mod
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

ALTER FUNCTION obingresos.ft_boletos_observados_sel (p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
  OWNER TO postgres;
