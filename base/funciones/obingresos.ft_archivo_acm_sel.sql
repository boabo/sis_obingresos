/**************************************************************************
 SISTEMA:		Ingresos
 FUNCION: 		obingresos.ft_archivo_acm_sel
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'obingresos.tarchivo_acm'
 AUTOR: 		 RZABALA
 FECHA:	        05-09-2018 20:09:45
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				05-09-2018 20:09:45								Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'obingresos.tarchivo_acm'
 #
 ***************************************************************************/

DECLARE

	v_consulta    		varchar;
	v_parametros  		record;
	v_nombre_funcion   	text;
	v_resp				varchar;

BEGIN

	v_nombre_funcion = 'obingresos.ft_archivo_acm_sel';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'OBING_taa_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		RZABALA
 	#FECHA:		05-09-2018 20:09:45
	***********************************/

	if(p_transaccion='OBING_taa_SEL')then

    	begin
    		--Sentencia de la consulta
			v_consulta:='select
						taa.id_archivo_acm,
						taa.estado_reg,
						taa.fecha_fin,
						taa.nombre,
						taa.fecha_ini,
						taa.usuario_ai,
						taa.fecha_reg,
						taa.id_usuario_reg,
						taa.id_usuario_ai,
						taa.id_usuario_mod,
						taa.fecha_mod,
                        taa.estado,
						usu1.cuenta as usr_reg,
						usu2.cuenta as usr_mod
						from obingresos.tarchivo_acm taa
						inner join segu.tusuario usu1 on usu1.id_usuario = taa.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = taa.id_usuario_mod
				        where  ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

			--Devuelve la respuesta
			return v_consulta;

		end;
        /*********************************
 	#TRANSACCION:  'OBING_REPORT_SEL'
 	#DESCRIPCION:	Listar Datos para el Reporte
 	#AUTOR:		RZABALA
 	#FECHA:		20-09-2018 12:24:32
	***********************************/

	elsif(p_transaccion='OBING_REPORT_SEL')then

    	begin
    		--Sentencia de la consulta



			v_consulta:='select
						taa.id_archivo_acm,
						taa.estado_reg,
						taa.fecha_fin,
						taa.nombre,
						taa.fecha_ini,
						taa.usuario_ai,
						taa.fecha_reg,
						taa.id_usuario_reg,
						taa.id_usuario_ai,
						taa.id_usuario_mod,
						taa.fecha_mod,
                        taa.estado,

                        archdet.porcentaje,
                        archdet.neto_total_mb,
                        archdet.neto_total_mt,
                        archdet.cant_bol_mb,
                        archdet.cant_bol_mt,
                        archdet.importe_total_mb,
                        archdet.importe_total_mt,
                        archdet.id_archivo_acm_det,

                        agen.nombre as agencia,
                        agen.codigo_int as office_id,

                        lu.codigo as cod_ciudad,
                        lu.nombre as estacion,

                        acm.numero as numero_acm

						from obingresos.tarchivo_acm taa
                        left join obingresos.tarchivo_acm_det archdet on archdet.id_archivo_acm = taa.id_archivo_acm
                        inner join obingresos.tagencia agen on agen.id_agencia = archdet.id_agencia
                        inner join param.tlugar lu on lu.id_lugar = agen.id_lugar
                        left join obingresos.tacm acm on acm.id_archivo_acm_det = archdet.id_archivo_acm_det

                        where taa.id_archivo_acm = '||v_parametros.id_archivo_acm;

			--Definicion de la respuesta
			v_consulta:=v_consulta||'ORDER BY
            			lu.codigo,
                        agen.nombre,
                        lu.nombre';
			--v_consulta:=v_consulta||' order by archdet.id_archivo_acm_det';

			--Devuelve la respuesta
			return v_consulta;

		end;

	/*********************************
 	#TRANSACCION:  'OBING_taa_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		RZABALA
 	#FECHA:		05-09-2018 20:09:45
	***********************************/

	elsif(p_transaccion='OBING_taa_CONT')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:='select count(id_archivo_acm)
					    from obingresos.tarchivo_acm taa
					    inner join segu.tusuario usu1 on usu1.id_usuario = taa.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = taa.id_usuario_mod
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