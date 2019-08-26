CREATE OR REPLACE FUNCTION obingresos.ft_deposito_sel (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Ingresos
 FUNCION: 		obingresos.ft_deposito_sel
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'obingresos.tdeposito'
 AUTOR: 		 (jrivera)
 FECHA:	        06-01-2016 22:42:28
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
    v_deposito			record;
	v_fecha_in				date;
    v_fecha_fi				date;
    v_id_deposito			integer;

BEGIN

	v_nombre_funcion = 'obingresos.ft_deposito_sel';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'OBING_DEP_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		jrivera
 	#FECHA:		06-01-2016 22:42:28
	***********************************/

	if(p_transaccion='OBING_DEP_SEL')then

    	begin
    		--Sentencia de la consulta
			v_consulta:='select
						dep.id_deposito,
						dep.estado_reg,
						dep.nro_deposito,
                        dep.nro_deposito_boa,
						dep.monto_deposito,
						dep.id_moneda_deposito,
						dep.id_agencia,
						dep.fecha,
						dep.saldo,
						dep.id_usuario_reg,
						dep.fecha_reg,
						dep.id_usuario_ai,
						dep.usuario_ai,
						dep.id_usuario_mod,
						dep.fecha_mod,
						usu1.cuenta as usr_reg,
						usu2.cuenta as usr_mod,
						mon.codigo_internacional as desc_moneda,
                        dep.agt,
                        dep.fecha_venta,
                        dep.monto_total,
                        (age.codigo_int || '' - '' || age.nombre)::varchar as nombre_agencia,
                        (to_char(pv.fecha_ini,''DD/MM/YYYY'') || ''-'' || to_char(pv.fecha_fin,''DD/MM/YYYY'') || '' '' ||
                        tp.tipo_cc)::text as desc_periodo,
                        dep.estado,
                        dep.id_apertura_cierre_caja

						from obingresos.tdeposito dep
						inner join segu.tusuario usu1 on usu1.id_usuario = dep.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = dep.id_usuario_mod
						inner join param.tmoneda mon on mon.id_moneda = dep.id_moneda_deposito

                        left join obingresos.tagencia age on age.id_agencia = dep.id_agencia
                        left join obingresos.tperiodo_venta pv on pv.id_periodo_venta = dep.id_periodo_venta
                        left join obingresos.ttipo_periodo tp on tp.id_tipo_periodo = pv.id_tipo_periodo

                        /************RECUPERANDO*************/
                        left join vef.tapertura_cierre_caja_asociada aper on aper.id_deposito = dep.id_deposito
                        left join vef.tapertura_cierre_caja caja on caja.id_apertura_cierre_caja = aper.id_apertura_cierre_caja
                        /***********************************/

				        where  ';


			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

			--Devuelve la respuesta
			return v_consulta;

		end;

  	/*********************************
 	#TRANSACCION:  'OBING_DEREP_SEL'
 	#DESCRIPCION:	Reporte Deposito
 	#AUTOR:		mam
 	#FECHA:		23-11-2016 22:42:28
	***********************************/

    ELSIF(p_transaccion= 'OBING_DEREP_SEL')then
		begin

        	if (v_parametros.por = 'boleto') then
            	v_consulta = 'with boletos as (
                    select b.id_boleto,b.fecha_emision,b.localizador,bfp.importe, m.codigo_internacional as moneda,b.nro_boleto,bfp.numero_tarjeta,a.codigo_int as agencia

                    from obingresos.tboleto b
                    inner join obingresos.tboleto_forma_pago bfp on bfp.id_boleto = b.id_boleto
                    inner join obingresos.tforma_pago fp on fp.id_forma_pago = bfp.id_forma_pago
                    inner join obingresos.tagencia a on a.id_agencia = b.id_agencia
                    inner join param.tmoneda m on m.id_moneda = fp.id_moneda
                    where b.fecha_emision >= ''' || v_parametros.fecha_ini || '''::date and b.fecha_emision <=''' || v_parametros.fecha_fin || '''::date and
                    b.estado_reg = ''activo'' and b.voided = ''no'' and fp.codigo like ''CC%'' and bfp.numero_tarjeta not like ''%00005555'' and
                    a.codigo_int in (''56991266'',''57991334'',''78495104'',''91993436'',''55995671'')),
                    depositos as (SELECT pxp.list(d.nro_deposito) as nro_deposito,pxp.list(to_char(d.fecha,''DD/MM/YYYY'')) as fecha_deposito,
                                                    sum(d.monto_deposito) as monto_deposito,
                                                    pxp.list(d.moneda) as moneda,
                                                    pxp.list(d.observaciones) as numero_tarjeta_deposito,
                                                    d.pnr
                                        from obingresos.tdeposito  d
                                        where d.fecha >= (''' || v_parametros.fecha_ini || '''::date - interval ''5 days'') and
                                            d.fecha <= (''' || v_parametros.fecha_fin || '''::date + interval ''5 days'') and d.tipo = ''' || v_parametros.tipo_deposito || '''
                                        group by d.pnr)
                    SELECT d.nro_deposito::varchar,d.fecha_deposito::varchar,d.pnr::varchar,
                                                    d.monto_deposito,
                                                    d.moneda::varchar,
                                                    d.numero_tarjeta_deposito::varchar,

                                                    sum(b.importe) as total_boletos,
                                                    pxp.list(b.nro_boleto) as nro_boletos,
                                                     pxp.list(to_char(b.fecha_emision,''DD/MM/YYYY'')) as fecha_boletos,
                                                     pxp.list(b.numero_tarjeta) as numero_tarjeta,
                                                     pxp.list(b.nro_boleto || ''|'' || to_char(b.fecha_emision,''DD/MM/YYYY'') ||
                                                        ''|'' || b.importe || ''|'' || b.moneda || ''|'' || b.agencia || ''|'' || b.localizador || ''|'' || b.numero_tarjeta ORDER BY b.fecha_emision ASC) as detalle_boletos


                                            FROM boletos b
                                            LEFT  JOIN depositos d on upper(d.pnr) = upper(b.localizador)
                                            group by d.nro_deposito,d.fecha_deposito,
                                                    d.monto_deposito,
                                                    d.moneda,
                                                    d.pnr,
                                                    d.numero_tarjeta_deposito
                                            having sum(b.importe) != d.monto_deposito or d.monto_deposito is null
                                            order by d.nro_deposito';

            else
            	v_consulta = 'with boletos as (
                    select b.id_boleto,b.fecha_emision,b.localizador,bfp.importe, m.codigo_internacional as moneda,b.nro_boleto,bfp.numero_tarjeta,a.codigo_int as agencia

                    from obingresos.tboleto b
                    inner join obingresos.tboleto_forma_pago bfp on bfp.id_boleto = b.id_boleto
                    inner join obingresos.tforma_pago fp on fp.id_forma_pago = bfp.id_forma_pago
                    inner join obingresos.tagencia a on a.id_agencia = b.id_agencia
                    inner join param.tmoneda m on m.id_moneda = fp.id_moneda
                    where b.fecha_emision >= (''' || v_parametros.fecha_ini || '''::date - interval ''10 days'') and b.fecha_emision <=(''' || v_parametros.fecha_fin || '''::date + interval ''10 days'') and
                    b.estado_reg = ''activo'' and b.voided = ''no'' and fp.codigo like ''CC%'' and bfp.numero_tarjeta not like ''%00005555'' and
                    a.codigo_int in (''56991266'',''57991334'',''78495104'',''91993436'',''55995671'',''CBBOB08AA'')),
                    depositos as (SELECT pxp.list(d.nro_deposito) as nro_deposito,pxp.list(to_char(d.fecha,''DD/MM/YYYY'')) as fecha_deposito,
                                                    sum(d.monto_deposito) as monto_deposito,
                                                    pxp.list(d.moneda) as moneda,
                                                    pxp.list(d.observaciones) as numero_tarjeta_deposito,
                                                    d.pnr
                                        from obingresos.tdeposito  d
                                        where d.fecha >= ''' || v_parametros.fecha_ini || '''::date  and
                                            d.fecha <= ''' || v_parametros.fecha_fin || '''::date and d.tipo = ''' || v_parametros.tipo_deposito || '''
                                        group by d.pnr)
                    SELECT d.nro_deposito::varchar,d.fecha_deposito::varchar,d.pnr::varchar,
                                                    d.monto_deposito,
                                                    d.moneda::varchar,
                                                     d.numero_tarjeta_deposito::varchar,
                                                    sum(b.importe) as total_boletos,
                                                    pxp.list(b.nro_boleto) as nro_boletos,
                                                     pxp.list(to_char(b.fecha_emision,''DD/MM/YYYY'')) as fecha_boletos,
                                                     pxp.list(b.numero_tarjeta) as numero_tarjeta,
                                                     ''''::text


                                            FROM depositos d
                                            LEFT  JOIN boletos b on upper(d.pnr) = upper(b.localizador)
                                            group by d.nro_deposito,d.fecha_deposito,
                                                    d.monto_deposito,
                                                    d.moneda,
                                                    d.pnr,
                                                    d.numero_tarjeta_deposito
                                            having sum(b.importe) != d.monto_deposito or sum(b.importe) is null
                                            order by d.nro_deposito';

            end if;

            raise notice '%',v_consulta;

		 --Devuelve la respuesta
			return v_consulta;

    end;
	/*********************************
 	#TRANSACCION:  'OBING_DEP_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		jrivera
 	#FECHA:		06-01-2016 22:42:28
	***********************************/

	elsif(p_transaccion='OBING_DEP_CONT')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:='select count(dep.id_deposito),sum(dep.monto_deposito)
					    from obingresos.tdeposito dep
					    inner join segu.tusuario usu1 on usu1.id_usuario = dep.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = dep.id_usuario_mod
						inner join param.tmoneda mon on mon.id_moneda = dep.id_moneda_deposito
					    left join obingresos.tperiodo_venta pv on pv.id_periodo_venta = dep.id_periodo_venta
                        left join obingresos.tagencia age on age.id_agencia = dep.id_agencia
                        left join obingresos.ttipo_periodo tp on tp.id_tipo_periodo = pv.id_tipo_periodo

                        /*Recuperando*/
                        left join vef.tapertura_cierre_caja_asociada aper on aper.id_deposito = dep.id_deposito
                        left join vef.tapertura_cierre_caja caja on caja.id_apertura_cierre_caja = aper.id_apertura_cierre_caja
                        /*************/


                        where ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;

			--Devuelve la respuesta
			return v_consulta;

		end;
    /*********************************
 	#TRANSACCION:  'OBING_DEPBIN_SEL'
 	#DESCRIPCION:	Reporte de depositos banca por internet
 	#AUTOR:		jrivera
 	#FECHA:		06-01-2016 22:42:28
	***********************************/

	elsif(p_transaccion='OBING_DEPBIN_SEL')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:='select to_char(dep.fecha_venta,''DD/MM/YYYY'')::varchar, dep.agt,sum(dep.monto_deposito)
					    from obingresos.tdeposito dep

					    where dep.tipo = ''banca'' and dep.estado_reg = ''activo'' and
                        dep.fecha_venta >= ''' || v_parametros.fecha_ini || ''' and dep.fecha_venta <= ''' || v_parametros.fecha_fin || ''' and
                        dep.id_moneda_deposito = '||v_parametros.id_moneda || '
                        group by to_char(dep.fecha_venta,''DD/MM/YYYY''),dep.agt
                        order by 1,2';


			--Devuelve la respuesta
			return v_consulta;

		end;
    /*********************************
 	#TRANSACCION:  'OBING_DEPBINARC_SEL'
 	#DESCRIPCION:	Reporte depositos banca por internet acrhivos FTP
 	#AUTOR:		jrivera
 	#FECHA:		06-01-2016 22:42:28
	***********************************/

	elsif(p_transaccion='OBING_DEPBINARC_SEL')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:='select to_char(a.fecha,''DD/MM/YYYY'')::varchar, a.banco,sum(ad.total_amount)
					    from obingresos.tskybiz_archivo_detalle ad
                        inner join obingresos.tskybiz_archivo a on a.id_skybiz_archivo = ad.id_skybiz_archivo
                        inner join param.tmoneda m on m.codigo_internacional = a.moneda

					    where  ad.estado_reg = ''activo'' and
                        a.fecha >= ''' || v_parametros.fecha_ini || ''' and a.fecha <= ''' || v_parametros.fecha_fin || ''' and
                        m.id_moneda = '||v_parametros.id_moneda || '
                        group by a.fecha,a.banco
                        order by 1,2';


			--Devuelve la respuesta
			return v_consulta;

		end;

    /*********************************
 	#TRANSACCION:  'OBING_DEPAG_SEL'
 	#DESCRIPCION:	Listar depositos
 	#AUTOR:		ivaldivia
 	#FECHA:		16-08-2019 08:43:28
	***********************************/

	elsif(p_transaccion='OBING_DEPAG_SEL')then

		begin
    		--Sentencia de la consulta
			v_consulta:='select dep.id_deposito,
                                dep.estado_reg,
                                dep.nro_deposito,
                                dep.nro_deposito_boa,
                                dep.monto_deposito,
                                dep.id_moneda_deposito,
                                dep.id_agencia,
                                dep.fecha,
                                dep.saldo,
                                dep.id_usuario_reg,
                                dep.fecha_reg,
                                dep.id_usuario_ai,
                                dep.usuario_ai,
                                dep.id_usuario_mod,
                                dep.fecha_mod,
                                dep.usr_reg,
                                dep.usr_mod,
                                dep.desc_moneda,
                                dep.agt,
                                dep.fecha_venta,
                                dep.monto_total,
                                dep.nombre_agencia,
                                dep.desc_periodo,
                                dep.estado,
                                dep.id_apertura_cierre_caja,
                                dep.nro_cuenta,
                                dep.monto_total_ml,
                                dep.monto_total_me,
                                dep.diferencia_ml,
                                dep.diferencia_me
            			 from vef.vdepositos_agrupados dep

                        where dep.id_usuario_reg = '||p_id_usuario||' and ';


			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;



			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

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

ALTER FUNCTION obingresos.ft_deposito_sel (p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
  OWNER TO postgres;
