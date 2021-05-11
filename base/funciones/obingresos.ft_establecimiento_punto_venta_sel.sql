CREATE OR REPLACE FUNCTION obingresos.ft_establecimiento_punto_venta_sel (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Ingresos
 FUNCION: 		obingresos.ft_establecimiento_punto_venta_sel
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'obingresos.testablecimiento_punto_venta'
 AUTOR: 		 (admin)
 FECHA:	        17-03-2021 11:14:41
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				17-03-2021 11:14:41								Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'obingresos.testablecimiento_punto_venta'
 #
 ***************************************************************************/

DECLARE

	v_consulta    		varchar;
	v_parametros  		record;
	v_nombre_funcion   	text;
	v_resp				varchar;

BEGIN

	v_nombre_funcion = 'obingresos.ft_establecimiento_punto_venta_sel';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'OBING_ESTPVEN_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		admin
 	#FECHA:		17-03-2021 11:14:41
	***********************************/

	if(p_transaccion='OBING_ESTPVEN_SEL')then

    	begin
    		--Sentencia de la consulta
			v_consulta:='select
						estpven.id_establecimiento_punto_venta,
						estpven.estado_reg,
						estpven.codigo_estable,
						estpven.nombre_estable,
						estpven.id_punto_venta,
						estpven.id_usuario_reg,
						estpven.fecha_reg,
						estpven.id_usuario_ai,
						estpven.usuario_ai,
						estpven.id_usuario_mod,
						estpven.fecha_mod,
						usu1.cuenta as usr_reg,
						usu2.cuenta as usr_mod,
                        (tpv.nombre ||'' ( ''||coalesce(tpv.office_id,'''')||'' )'')::varchar nombre_office,
                        estpven.tipo_estable,
                        estpven.comercio,
                        tl.nombre nombre_lugar,
                        spv.name_pv nombre_iata,
                        spv.iata_code

						from obingresos.testablecimiento_punto_venta estpven
                        left join vef.tstage_punto_venta spv on spv.id_stage_pv = estpven.id_stage_pv
                        left join param.tlugar tl on tl.id_lugar = estpven.id_lugar
                        left join vef.tpunto_venta tpv on tpv.id_punto_venta = estpven.id_punto_venta
						inner join segu.tusuario usu1 on usu1.id_usuario = estpven.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = estpven.id_usuario_mod
				        where  ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

			--Devuelve la respuesta
			return v_consulta;

		end;

	/*********************************
 	#TRANSACCION:  'OBING_ESTPVEN_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		admin
 	#FECHA:		17-03-2021 11:14:41
	***********************************/

	elsif(p_transaccion='OBING_ESTPVEN_CONT')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:='select count(id_establecimiento_punto_venta)
					    from obingresos.testablecimiento_punto_venta estpven
                        left join vef.tpunto_venta tpv on tpv.id_punto_venta = estpven.id_punto_venta
					    inner join segu.tusuario usu1 on usu1.id_usuario = estpven.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = estpven.id_usuario_mod
					    where ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;

			--Devuelve la respuesta
			return v_consulta;

		end;
    /*********************************
 	#TRANSACCION:  'OBING_ESTPVEN_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		admin
 	#FECHA:		17-03-2021 11:14:41
	***********************************/

	elsif(p_transaccion='OBING_PSTAGE_SEL')then

    	begin
    		--Sentencia de la consulta
			v_consulta:='select
            			  spv.id_stage_pv,
            			  spv.stage_id_pv,
                          spv.iata_area,
                          spv.iata_zone,
                          spv.iata_zone_name,
                          spv.country_code,
                          spv.country_name,
                          spv.city_code,
                          spv.city_name,
                          spv.accounting_station,
                          spv.sale_type,
                          spv.sale_channel,
                          spv.tipo_pos,
                          spv.iata_code,
                          spv.iata_status,
                          spv.osd,
                          spv.office_id,
                          spv.gds,
                          spv.nit,
                          spv.name_pv,
                          spv.address,
                          spv.phone_number

						from vef.tstage_punto_venta spv
						inner join segu.tusuario usu1 on usu1.id_usuario = spv.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = spv.id_usuario_mod
				        where  ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

			--Devuelve la respuesta
			return v_consulta;

		end;

	/*********************************
 	#TRANSACCION:  'OBING_ESTPVEN_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		admin
 	#FECHA:		17-03-2021 11:14:41
	***********************************/

	elsif(p_transaccion='OBING_PSTAGE_CONT')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:='select count(spv.id_stage_pv)
					    from vef.tstage_punto_venta spv
					    inner join segu.tusuario usu1 on usu1.id_usuario = spv.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = spv.id_usuario_mod
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

ALTER FUNCTION obingresos.ft_establecimiento_punto_venta_sel (p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
  OWNER TO postgres;