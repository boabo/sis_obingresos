CREATE OR REPLACE FUNCTION obingresos.ft_acm_sel (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Ingresos
 FUNCION: 		obingresos.ft_acm_sel
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'obingresos.tacm'
 AUTOR: 		 (ivaldivia)
 FECHA:	        05-09-2018 20:34:32
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				05-09-2018 20:34:32								Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'obingresos.tacm'
 #
 ***************************************************************************/

DECLARE

	v_consulta    		varchar;
	v_parametros  		record;
	v_nombre_funcion   	text;
	v_resp				varchar;

BEGIN

	v_nombre_funcion = 'obingresos.ft_acm_sel';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'OBING_acm_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		ivaldivia
 	#FECHA:		05-09-2018 20:34:32
	***********************************/

	if(p_transaccion='OBING_acm_SEL')then

    	begin
    		--Sentencia de la consulta
			v_consulta:='select
						acm.id_acm,
						acm.id_moneda,
						acm.id_archivo_acm_det,
						acm.fecha,
						acm.numero,
						acm.ruta,
						acm.estado_reg,
						acm.importe,
						acm.id_usuario_ai,
						acm.id_usuario_reg,
						acm.fecha_reg,
						acm.usuario_ai,
						acm.id_usuario_mod,
						acm.fecha_mod,
						usu1.cuenta as usr_reg,
						usu2.cuenta as usr_mod,
                        mone.moneda as desc_moneda,
                        agen.nombre,
                        mone.codigo,
                        acm.id_movimiento_entidad,
                        archi.fecha_ini,
                        archi.fecha_fin,
                        acm.total_bsp,
                        acmdet.neto_total_mb,
                        acmdet.neto_total_mt,
                        acmdet.officce_id,
                        lugar.codigo_largo
						from obingresos.tacm acm
						inner join segu.tusuario usu1 on usu1.id_usuario = acm.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = acm.id_usuario_mod
                        inner join param.tmoneda mone on mone.id_moneda = acm.id_moneda
                        inner join obingresos.tarchivo_acm_det acmdet on acmdet.id_archivo_acm_det = acm.id_archivo_acm_det
                        inner join obingresos.tagencia agen on agen.id_agencia = acmdet.id_agencia
				      	inner join obingresos.tarchivo_acm archi on archi.id_archivo_acm = acmdet.id_archivo_acm
                        inner join param.tlugar lugar on lugar.id_lugar = agen.id_lugar
                        where  ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

			--Devuelve la respuesta
			return v_consulta;

		end;

        /*********************************
 	#TRANSACCION:  'OBING_REPOR_SEL'
 	#DESCRIPCION:	Listar Datos para el Reporte
 	#AUTOR:		ivaldivia
 	#FECHA:		05-09-2018 20:34:32
	***********************************/

	elsif(p_transaccion='OBING_REPOR_SEL')then

    	begin
    		--Sentencia de la consulta



			v_consulta:='select
						acm.id_acm,
						acm.id_moneda,
						acm.id_archivo_acm_det,
						acm.fecha,
						acm.numero,
						acm.ruta,
						acm.estado_reg,
						acm.importe,
						acm.id_usuario_ai,
						acm.id_usuario_reg,
						acm.fecha_reg,
						acm.usuario_ai,
						acm.id_usuario_mod,
						acm.fecha_mod,
                        acm.id_movimiento_entidad,

                        acd.over_comision,
                        acd.neto,
                        archdet.porcentaje,
                        archdet.officce_id,
                        archacm.fecha_ini,
                        archacm.fecha_fin,

                        mone.codigo,
                        agen.nombre,
                        bole.billete,

                        acd.com_bsp,
                        archdet.neto_total_mb,
                      	acm.total_bsp,
                        acd.td


                        from obingresos.tacm acm
                        inner join obingresos.tacm_det acd on acd.id_acm = acm.id_acm
                        inner join obingresos.tarchivo_acm_det archdet on archdet.id_archivo_acm_det = acm.id_archivo_acm_det
                        inner join obingresos.tarchivo_acm archacm on archacm.id_archivo_acm = archdet.id_archivo_acm
                        inner join param.tmoneda mone on mone.id_moneda = acm.id_moneda
                        inner join obingresos.tagencia agen on agen.id_agencia = archdet.id_agencia
                        inner join obingresos.tdetalle_boletos_web bole on bole.id_detalle_boletos_web = acd.id_detalle_boletos_web
				 		where acm.id_acm = '||v_parametros.id_acm;

			--Definicion de la respuesta
			v_consulta:=v_consulta||'ORDER BY bole.billete';
			--v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

			--Devuelve la respuesta
			return v_consulta;

		end;


	/*********************************
 	#TRANSACCION:  'OBING_acm_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		ivaldivia
 	#FECHA:		05-09-2018 20:34:32
	***********************************/

	elsif(p_transaccion='OBING_acm_CONT')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:='select count(id_acm)
					    from obingresos.tacm acm
						inner join segu.tusuario usu1 on usu1.id_usuario = acm.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = acm.id_usuario_mod
                        inner join param.tmoneda mone on mone.id_moneda = acm.id_moneda
                        inner join obingresos.tarchivo_acm_det acmdet on acmdet.id_archivo_acm_det = acm.id_archivo_acm_det
                        inner join obingresos.tagencia agen on agen.id_agencia = acmdet.id_agencia
				      	inner join obingresos.tarchivo_acm archi on archi.id_archivo_acm = acmdet.id_archivo_acm
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