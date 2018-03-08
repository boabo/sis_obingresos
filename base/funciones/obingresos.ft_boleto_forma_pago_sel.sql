CREATE OR REPLACE FUNCTION obingresos.ft_boleto_forma_pago_sel (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Ingresos
 FUNCION: 		obingresos.ft_boleto_forma_pago_sel
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'obingresos.tboleto_forma_pago'
 AUTOR: 		 (jrivera)
 FECHA:	        13-06-2016 20:42:15
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

	v_nombre_funcion = 'obingresos.ft_boleto_forma_pago_sel';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'OBING_BFP_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		jrivera
 	#FECHA:		13-06-2016 20:42:15
	***********************************/

	if(p_transaccion='OBING_BFP_SEL')then

    	begin
    		--Sentencia de la consulta
			v_consulta:='select
						bfp.id_boleto_forma_pago,
						bfp.tipo,
						bfp.id_forma_pago,
						bfp.id_boleto,
						bfp.estado_reg,
						bfp.tarjeta,
						bfp.ctacte,
						bfp.importe,
						bfp.numero_tarjeta,
						bfp.id_usuario_ai,
						bfp.id_usuario_reg,
						bfp.usuario_ai,
						bfp.fecha_reg,
						bfp.id_usuario_mod,
						bfp.fecha_mod,
						usu1.cuenta as usr_reg,
						usu2.cuenta as usr_mod,
                        (fp.nombre || '' - '' || coalesce(mon.codigo_internacional ,''''))::varchar as forma_pago,
                        fp.codigo as codigo_forma_pago,
                        mon.codigo_internacional as moneda,
                        aux.nombre_auxiliar,
                        bfp.codigo_tarjeta
						from obingresos.tboleto_forma_pago bfp
						inner join segu.tusuario usu1 on usu1.id_usuario = bfp.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = bfp.id_usuario_mod
				        inner join obingresos.tforma_pago fp on fp.id_forma_pago = bfp.id_forma_pago
                        left join param.tmoneda mon on mon.id_moneda = fp.id_moneda
                        left join conta.tauxiliar aux on aux.id_auxiliar=bfp.id_auxiliar
                        where  ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

			--Devuelve la respuesta
			return v_consulta;

		end;

    /*********************************
 	#TRANSACCION:  'OBING_BFPAMA_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		jrivera
 	#FECHA:		13-06-2016 20:42:15
	***********************************/

	elsif(p_transaccion='OBING_BFPAMA_SEL')then

    	begin
    		--Sentencia de la consulta
			v_consulta:='select
						bfp.id_boleto_amadeus_forma_pago,
						bfp.tipo,
						bfp.id_forma_pago,
						bfp.id_boleto_amadeus,
						bfp.estado_reg,
						bfp.tarjeta,
						bfp.ctacte,
						bfp.importe,
						bfp.numero_tarjeta,
						bfp.id_usuario_ai,
						bfp.id_usuario_reg,
						bfp.usuario_ai,
						bfp.fecha_reg,
						bfp.id_usuario_mod,
						bfp.fecha_mod,
						usu1.cuenta as usr_reg,
						usu2.cuenta as usr_mod,
                        (fp.nombre || '' - '' || coalesce(mon.codigo_internacional ,''''))::varchar as forma_pago,
                        fp.codigo as codigo_forma_pago,
                        mon.codigo_internacional as moneda,
                        aux.nombre_auxiliar,
                        bfp.codigo_tarjeta,
                        bfp.mco,
                        fp.codigo
                        from obingresos.tboleto_amadeus_forma_pago bfp
						inner join segu.tusuario usu1 on usu1.id_usuario = bfp.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = bfp.id_usuario_mod
				        inner join obingresos.tforma_pago fp on fp.id_forma_pago = bfp.id_forma_pago
                        left join param.tmoneda mon on mon.id_moneda = fp.id_moneda
                        left join conta.tauxiliar aux on aux.id_auxiliar=bfp.id_auxiliar
                        where  ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;
			raise notice '%',v_consulta;
			--Devuelve la respuesta
			return v_consulta;

		end;

	/*********************************
 	#TRANSACCION:  'OBING_BFP_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		jrivera
 	#FECHA:		13-06-2016 20:42:15
	***********************************/

	elsif(p_transaccion='OBING_BFP_CONT')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:='select count(id_boleto_forma_pago)
					    from obingresos.tboleto_forma_pago bfp
					    inner join segu.tusuario usu1 on usu1.id_usuario = bfp.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = bfp.id_usuario_mod
                        inner join obingresos.tforma_pago fp on fp.id_forma_pago = bfp.id_forma_pago
					    left join param.tmoneda mon on mon.id_moneda = fp.id_moneda
                        where ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;

			--Devuelve la respuesta
			return v_consulta;

		end;

    /*********************************
 	#TRANSACCION:  'OBING_BFPAMA_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		jrivera
 	#FECHA:		13-06-2016 20:42:15
	***********************************/

	elsif(p_transaccion='OBING_BFPAMA_CONT')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:='select count(id_boleto_amadeus_forma_pago)
					    from obingresos.tboleto_amadeus_forma_pago bfp
					    inner join segu.tusuario usu1 on usu1.id_usuario = bfp.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = bfp.id_usuario_mod
                        inner join obingresos.tforma_pago fp on fp.id_forma_pago = bfp.id_forma_pago
					    left join param.tmoneda mon on mon.id_moneda = fp.id_moneda
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