CREATE OR REPLACE FUNCTION obingresos.ft_archivo_acm_det_sel (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Ingresos
 FUNCION: 		obingresos.ft_archivo_acm_det_sel
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'obingresos.tarchivo_acm_det'
 AUTOR: 		 RZABALA
 FECHA:	        05-09-2018 20:36:49
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				05-09-2018 20:36:49								Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'obingresos.tarchivo_acm_det'
 #
 ***************************************************************************/

DECLARE

	v_consulta    		varchar;
	v_parametros  		record;
	v_nombre_funcion   	text;
	v_resp				varchar;

BEGIN

	v_nombre_funcion = 'obingresos.ft_archivo_acm_det_sel';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'OBING_AAD_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		RZABALA
 	#FECHA:		05-09-2018 20:36:49
	***********************************/

	if(p_transaccion='OBING_AAD_SEL')then

    	begin
    		--Sentencia de la consulta
			v_consulta:='select
						aad.id_archivo_acm_det,
						aad.id_archivo_acm,
						aad.importe_total_mb,
						aad.estado_reg,
						aad.porcentaje,
						aad.importe_total_mt,
						aad.id_agencia,
						aad.officce_id,
						aad.usuario_ai,
						aad.fecha_reg,
						aad.id_usuario_reg,
						aad.id_usuario_ai,
						aad.id_usuario_mod,
						aad.fecha_mod,
                        aad.neto_total_mb,
                        aad.cant_bol_mb,
                        aad.neto_total_mt,
                        aad.cant_bol_mt,
						usu1.cuenta as usr_reg,
						usu2.cuenta as usr_mod,
                        age.nombre as agencia,
                        age.tipo_agencia,
                        arch.estado,
                        aad.abonado
						from obingresos.tarchivo_acm_det aad
						inner join segu.tusuario usu1 on usu1.id_usuario = aad.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = aad.id_usuario_mod
                        left join obingresos.tagencia age on age.id_agencia = aad.id_agencia
                        left join obingresos.tarchivo_acm arch on arch.id_archivo_acm = aad.id_archivo_acm
				        where  ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

			--Devuelve la respuesta
			return v_consulta;

		end;


	/*********************************
 	#TRANSACCION:  'OBING_AAD_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		RZABALA
 	#FECHA:		05-09-2018 20:36:49
	***********************************/

	elsif(p_transaccion='OBING_AAD_CONT')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:='select count(id_archivo_acm_det),
            			sum(aad.importe_total_mb),
                        sum(aad.importe_total_mt),
                        sum(aad.neto_total_mb),
                        round (sum(aad.cant_bol_mb),0),
                        sum(aad.neto_total_mt),
                        round (sum(aad.cant_bol_mt),0)
					    from obingresos.tarchivo_acm_det aad
						inner join segu.tusuario usu1 on usu1.id_usuario = aad.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = aad.id_usuario_mod
                        left join obingresos.tagencia age on age.id_agencia = aad.id_agencia
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