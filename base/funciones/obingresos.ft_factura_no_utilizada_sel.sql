CREATE OR REPLACE FUNCTION obingresos.ft_factura_no_utilizada_sel (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Ingresos
 FUNCION: 		obingresos.ft_factura_no_utilizada_sel
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'obingresos.ft_factura_manual_sel'
 AUTOR: 		 Maylee Perez Pastor
 FECHA:	        08-05-2020 20:37:45
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

	v_nombre_funcion = 'obingresos.ft_factura_no_utilizada_sel';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'OBING_FACMAN_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		Maylee Perez Pastor
 	#FECHA:		08-05-2020 20:37:45
	***********************************/

	if(p_transaccion='OBING_FACMAN_SEL')then

    	begin
    		--Sentencia de la consulta
			v_consulta:='select
						fam.id_factura_no_utilizada,
                        fam.id_punto_venta,
                        fam.id_estado_factura,
                        fam.tipo_cambio,
                        fam.id_moneda,
                        fam.nombre,
                        fam.nit,
                        fam.observaciones,
                        fam.id_concepto_ingas,

                        fam.estado_reg,
                        fam.id_usuario_reg,
                        fam.fecha_reg,
                        fam.id_usuario_mod,
                        fam.fecha_mod,
                        usu1.cuenta as usr_reg,
                        usu2.cuenta as usr_mod,

                        pv.nombre::varchar as nom_punto_venta,
                        dos.id_dosificacion,
                        lu.codigo as estacion,
                        dos.tipo,
                        dos.nro_tramite,
                        dos.id_sucursal,
                        ''(''||su.codigo||'') ''|| su.nombre as nom_sucursal,
                        su.nombre as nombre_sucursal,
                        fam.fecha,
                        dos.fecha_dosificacion,
                        dos.nroaut,
                        fam.nro_inicial as inicial,
                        fam.nro_final as final,
                        mon.moneda as desc_moneda

                    from obingresos.tfactura_no_utilizada fam
                    left join vef.tdosificacion dos on dos.id_dosificacion = fam.id_dosificacion
                    inner join segu.tusuario usu1 on usu1.id_usuario = fam.id_usuario_reg
                    left join segu.tusuario usu2 on usu2.id_usuario = fam.id_usuario_mod
                    inner join vef.tsucursal su on su.id_sucursal = dos.id_sucursal
                    inner join param.tlugar lu on lu.id_lugar = su.id_lugar

                    left join param.tmoneda mon on mon.id_moneda = fam.id_moneda
                    left join vef.tpunto_venta pv on pv.id_punto_venta = fam.id_punto_venta

                    where ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

			--Devuelve la respuesta
			return v_consulta;

		end;

	/*********************************
 	#TRANSACCION:  'OBING_FACMAN_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		Maylee Perez Pastor
 	#FECHA:		08-05-2020 20:37:45
	***********************************/

	elsif(p_transaccion='OBING_FACMAN_CONT')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:='select count(fam.id_factura_no_utilizada)
					    from obingresos.tfactura_no_utilizada fam
                        left join vef.tdosificacion dos on dos.id_dosificacion = fam.id_dosificacion
                        inner join segu.tusuario usu1 on usu1.id_usuario = dos.id_usuario_reg
                        left join segu.tusuario usu2 on usu2.id_usuario = dos.id_usuario_mod
                        inner join vef.tsucursal su on su.id_sucursal = dos.id_sucursal
                        inner join param.tlugar lu on lu.id_lugar = su.id_lugar

                        left join param.tmoneda mon on mon.id_moneda = fam.id_moneda
                        left join vef.tpunto_venta pv on pv.id_punto_venta = fam.id_punto_venta

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