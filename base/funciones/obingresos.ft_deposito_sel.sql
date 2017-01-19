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
						mon.codigo_internacional as desc_moneda
						from obingresos.tdeposito dep
						inner join segu.tusuario usu1 on usu1.id_usuario = dep.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = dep.id_usuario_mod
						inner join param.tmoneda mon on mon.id_moneda = dep.id_moneda_deposito
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
                    a.codigo_int in (''56991266'',''57991334'',''78495104'',''91993436'',''55995671'')),
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
 	#TRANSACCION:  'OBING_REPRESVEW_SEL'
 	#DESCRIPCION:	Reporte Deposito
 	#AUTOR:		Gonzalo Sarmiento 
 	#FECHA:		06-12-2016
	***********************************/

    ELSIF(p_transaccion= 'OBING_REPRESVEW_SEL')then
      begin
        if(v_parametros.tipo = 'sin_boletos_web')then
          v_consulta = 'select b.nro_boleto as boleto_resiber,
                              dbw.billete as boleto_ventas_web,
                             bfp.numero_tarjeta,
                             b.fecha_emision as fecha,
                             b.total as monto_resiber,
                             dbw.importe as monto_ventas_web,
                             b.moneda
                      from obingresos.tboleto b
                           inner join obingresos.tboleto_forma_pago bfp on bfp.id_boleto = b.id_boleto
                           left join obingresos.tdetalle_boletos_web dbw on dbw.billete = b.nro_boleto
                           left join obingresos.tventa_web_modificaciones ree on ree.nro_boleto_reemision = b.nro_boleto
                           left join obingresos.tventa_web_modificaciones anu on anu.nro_boleto = b.nro_boleto
                      where b.fecha_emision >= '''||v_parametros.fecha_ini||'''::date and
                            b.fecha_emision < '''||v_parametros.fecha_fin||'''::date and
                            bfp.numero_tarjeta like ''%000000005555''  and
                            b.estado_reg = ''activo'' and
                            bfp.tarjeta = ''VI'' and voided = ''no'' and (dbw.billete is null and ree.nro_boleto is null and anu.nro_boleto is null )
                            group by b.nro_boleto, b.fecha_emision,
                                     dbw.billete, bfp.numero_tarjeta,
                                     dbw.importe, dbw.moneda,
                                   b.total, b.moneda
                            order by fecha';
        elsif(v_parametros.tipo='sin_boletos_resiber')then
          v_consulta = 'select b.nro_boleto as boleto_resiber,
                                    dbw.billete as boleto_ventas_web,
                                   bfp.numero_tarjeta,
                                   dbw.fecha as fecha,
                                   dbw.importe as monto_resiber,
                                   b.total as monto_ventas_web,       
                                   dbw.moneda
                            from obingresos.tdetalle_boletos_web dbw 
                                 left join obingresos.tboleto b on dbw.billete = b.nro_boleto     
                                 left join obingresos.tboleto_forma_pago bfp on bfp.id_boleto = b.id_boleto
                            where dbw.fecha >= '''||v_parametros.fecha_ini||'''::date and
                                  dbw.fecha < '''||v_parametros.fecha_fin||'''::date and
                                   b.nro_boleto is null and dbw.medio_pago != ''COMPLETAR-CC'' and 
                                  b.estado_reg =''activo''
                            group by b.nro_boleto, fecha, dbw.billete, bfp.numero_tarjeta,
                            dbw.importe, dbw.moneda, b.total, b.moneda
                            UNION ALL
                            select b.nro_boleto as boleto_resiber,
                                    dbw.billete as boleto_ventas_web,
                                   bfp.numero_tarjeta,
                                   b.fecha_emision as fecha,
                                   b.total as monto_resiber,
                                   dbw.importe as monto_ventas_web,
                                   dbw.moneda
                            from obingresos.tboleto b
                                 inner join obingresos.tdetalle_boletos_web dbw on dbw.billete = b.nro_boleto
                                 left join obingresos.tboleto_forma_pago bfp on bfp.id_boleto = b.id_boleto
                                 left join obingresos.tventa_web_modificaciones vwm on vwm.nro_boleto = b.nro_boleto
                            where b.fecha_emision >= '''||v_parametros.fecha_ini||'''::date and
                                  b.fecha_emision < '''||v_parametros.fecha_fin||'''::date and
                                  voided = ''si''  and  dbw.medio_pago != ''COMPLETAR-CC'' and 
                                  b.estado_reg =''activo'' and vwm.nro_boleto is null
                            order by fecha';
        else
          v_consulta='select b.nro_boleto as boleto_resiber,
                            dbw.billete as boleto_ventas_web,
                           bfp.numero_tarjeta,
                           b.fecha_emision as fecha,
                           b.total as monto_resiber,
                           dbw.importe as monto_ventas_web,
                           b.moneda
                    from obingresos.tboleto b
                         inner join obingresos.tboleto_forma_pago bfp on bfp.id_boleto = b.id_boleto
                         left join obingresos.tdetalle_boletos_web dbw on dbw.billete = b.nro_boleto
                    where b.fecha_emision >= '''||v_parametros.fecha_ini||'''::date and
                          b.fecha_emision < '''||v_parametros.fecha_fin||'''::date and
                          bfp.numero_tarjeta like ''%000000005555'' and
                          bfp.tarjeta = ''VI'' and
                          voided = ''no'' and
                          b.total!=dbw.importe and dbw.medio_pago != ''COMPLETAR-CC''
                    group by b.nro_boleto, b.fecha_emision, dbw.billete, bfp.numero_tarjeta,
                    dbw.importe, dbw.moneda, b.total, b.moneda
                    order by fecha';
        end if;
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
        v_consulta:='select count(id_deposito)
					    from obingresos.tdeposito dep
					    inner join segu.tusuario usu1 on usu1.id_usuario = dep.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = dep.id_usuario_mod
						inner join param.tmoneda mon on mon.id_moneda = dep.id_moneda_deposito
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