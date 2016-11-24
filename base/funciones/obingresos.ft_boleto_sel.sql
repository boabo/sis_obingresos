CREATE OR REPLACE FUNCTION obingresos.ft_boleto_sel (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
  RETURNS varchar AS
  $body$
  /**************************************************************************
   SISTEMA:		Ingresos
   FUNCION: 		obingresos.ft_boleto_sel
   DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'obingresos.tboleto'
   AUTOR: 		 (jrivera)
   FECHA:	        06-01-2016 22:42:25
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

    v_nombre_funcion = 'obingresos.ft_boleto_sel';
    v_parametros = pxp.f_get_record(p_tabla);

    /*********************************
     #TRANSACCION:  'OBING_BOL_SEL'
     #DESCRIPCION:	Consulta de datos
     #AUTOR:		jrivera
     #FECHA:		06-01-2016 22:42:25
    ***********************************/

    if(p_transaccion='OBING_BOL_SEL')then

      begin
        --Sentencia de la consulta
        v_consulta:='with forma_pago_temporal as(
					    	select count(*)as cantidad_forma_pago,bfp.id_boleto,
					        	array_agg(fp.id_forma_pago) as id_forma_pago, array_agg(fp.nombre || '' - '' || mon.codigo_internacional) as forma_pago,
                                array_agg(bfp.importe) as monto_forma_pago,array_agg(fp.codigo) as codigo_forma_pago,
                                array_agg(bfp.numero_tarjeta) as numero_tarjeta,array_agg(bfp.codigo_tarjeta) as codigo_tarjeta,
                                array_agg(bfp.ctacte) as ctacte,
                                array_agg(mon.codigo_internacional) as moneda_fp  
                                
					        from obingresos.tboleto_forma_pago bfp
					        inner join obingresos.tforma_pago fp on fp.id_forma_pago = bfp.id_forma_pago
                            inner join obingresos.tboleto bol on bol.id_boleto = bfp.id_boleto
                            inner join param.tmoneda mon on mon.id_moneda = fp.id_moneda
                            where ' || v_parametros.filtro || ' 
					        group by bfp.id_boleto        
					    )
            		select
						bol.id_boleto,
						bol.fecha_emision,
						age.codigo_noiata as codigo_noiata,
						bol.cupones,
						bol.ruta,
						bol.estado,
						bol.id_agencia,
						bol.moneda,
						bol.total,
						bol.pasajero,
						bol.id_moneda_boleto,
						bol.estado_reg,
						bol.gds,
						bol.comision,
						age.codigo as codigo_agencia,
						bol.neto,
						bol.tipopax,
						bol.origen,
						bol.destino,
						bol.retbsp,
						bol.monto_pagado_moneda_boleto,
						bol.tipdoc,
						bol.liquido,
						substring(bol.nro_boleto from 4)::varchar,
						bol.id_usuario_ai,
						bol.id_usuario_reg,
						bol.fecha_reg,
						bol.usuario_ai,
						bol.id_usuario_mod,
						bol.fecha_mod,
						usu1.cuenta as usr_reg,
						usu2.cuenta as usr_mod,
                        age.nombre	as nombre_agencia,
                        (case when (forpa.cantidad_forma_pago > 2) then
                        	0::integer
                        else
                        	forpa.id_forma_pago[1]::integer
                        end) as id_forma_pago,
                        (case when (forpa.cantidad_forma_pago > 2) then
                        	''DIVIDIDO''::varchar
                        else
                        	forpa.forma_pago[1]::varchar
                        end) as forma_pago,
                        (case when (forpa.cantidad_forma_pago > 2) then
                        	0::numeric
                        else
                        	forpa.monto_forma_pago[1]::numeric
                        end) as monto_forma_pago,
                        codigo_forma_pago[1],
                        forpa.numero_tarjeta[1],
                        forpa.codigo_tarjeta[1],
                        forpa.ctacte[1],
                        forpa.moneda_fp[1],
                        
                        (case when (forpa.cantidad_forma_pago <> 2) then
                        	0::integer
                        else
                        	forpa.id_forma_pago[2]::integer
                        end) as id_forma_pago2,
                        (case when (forpa.cantidad_forma_pago > 2) then
                        	''DIVIDIDO''::varchar
                        when (forpa.cantidad_forma_pago < 2) then
                        	''NINGUNO''::varchar
                        else
                        	forpa.forma_pago[2]::varchar
                        end) as forma_pago2,
                        (case when (forpa.cantidad_forma_pago <> 2) then
                        	0::numeric
                        else
                        	forpa.monto_forma_pago[2]::numeric
                        end) as monto_forma_pago2,
                        codigo_forma_pago[2] as codigo_forma_pago2,
                        forpa.numero_tarjeta[2] as numero_tarjeta2,
                        forpa.codigo_tarjeta[2] as codigo_tarjeta2,
                        forpa.ctacte[2] as ctacte2,
                        forpa.moneda_fp[2] as moneda_fp2,
                                                
                        bol.tc,
                        bol.moneda_sucursal,
                        bol.ruta_completa
						from obingresos.tboleto bol
                        inner join obingresos.tagencia age on age.id_agencia = bol.id_agencia
                        left join forma_pago_temporal forpa on forpa.id_boleto = bol.id_boleto
                        inner join param.tmoneda mon on mon.id_moneda = bol.id_moneda_boleto
						inner join segu.tusuario usu1 on usu1.id_usuario = bol.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = bol.id_usuario_mod
				        where bol.estado_reg = ''activo'' and ';

        --Definicion de la respuesta
        v_consulta:=v_consulta||v_parametros.filtro;
        v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;
        --raise exception '%', v_consulta;
        --Devuelve la respuesta
        return v_consulta;

      end;

    /*********************************
     #TRANSACCION:  'OBING_BOL_CONT'
     #DESCRIPCION:	Conteo de registros
     #AUTOR:		jrivera
     #FECHA:		06-01-2016 22:42:25
    ***********************************/

    elsif(p_transaccion='OBING_BOL_CONT')then

      begin
        --Sentencia de la consulta de conteo de registros
        v_consulta:='select count(id_boleto)
					    from obingresos.tboleto bol
                        inner join obingresos.tagencia age on age.id_agencia = bol.id_agencia
                        inner join param.tmoneda mon on mon.id_moneda = bol.id_moneda_boleto
					    inner join segu.tusuario usu1 on usu1.id_usuario = bol.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = bol.id_usuario_mod
					    where  bol.estado_reg = ''activo'' and ';

        --Definicion de la respuesta
        v_consulta:=v_consulta||v_parametros.filtro;

        --Devuelve la respuesta
        return v_consulta;

      end;

    /*********************************    
 	#TRANSACCION:  'OBING_BOLFAC_SEL'
 	#DESCRIPCION:	Reporte de Boleto
 	#AUTOR:		jrivera	
 	#FECHA:		06-01-2016 22:42:25
	***********************************/

    elsif(p_transaccion='OBING_BOLFAC_SEL')then

      begin
        --Sentencia de la consulta de conteo de registros
        v_consulta:='
    with 
    origen as (select bv.id_boleto, l.codigo as pais
				from obingresos.tboleto_vuelo bv
                inner join obingresos.taeropuerto a on a.id_aeropuerto = bv.id_aeropuerto_origen
                inner join param.tlugar l on l.id_lugar = param.f_get_id_lugar_pais(a.id_lugar)
                where bv.id_boleto =  ' || v_parametros.id_boleto|| '  
                order by bv.id_boleto_vuelo ASC limit 1 offset 0) ,
    tasas as (select bt.id_boleto, sum(bt.importe) as importe
            from obingresos.tboleto_impuesto bt
            where bt.id_boleto = ' || v_parametros.id_boleto|| ' and bt.calculo_tarifa = ''no''
            group by id_boleto),
	 sujeto as (
     		select bi.id_boleto, sum(bi.importe) as importe, pxp.list((bi.importe || '' '' || i.codigo)::varchar)::varchar as impuesto
            from obingresos.tboleto_impuesto bi 
            inner join obingresos.timpuesto i on i.id_impuesto = bi.id_impuesto and
                        			i.codigo in (''BO'', ''QM'')
            where bi.id_boleto = ' || v_parametros.id_boleto|| '
            group by id_boleto),
     
     forma_pago as (
     		select bfp.id_boleto, pxp.list((case when fp.codigo = ''CA'' then ''CASH'' else fp.codigo end) || '' '' || fpmon.codigo_internacional || '' '' || bfp.importe)::varchar as forma_pago
            from obingresos.tboleto_forma_pago bfp
            inner join vef.tforma_pago fp on fp.id_forma_pago = bfp.id_forma_pago
            inner join param.tmoneda fpmon on fpmon.id_moneda = fp.id_moneda
            where bfp.id_boleto = ' || v_parametros.id_boleto|| '
            group by id_boleto
            )           
            
            
            select  e.nit,
            			to_char (b.fecha_emision,''DD MON YYYY'')::varchar as fecha_emision,
                        pv.codigo as codigo_pv,
                        pv.nombre as punto_venta,
                        (substr(b.nro_boleto,1,3) || ''-'' || substr(b.nro_boleto,4) ||
                        	coalesce(''|'' || (	select substr(nro_boleto,12) 
                        						from obingresos.tboleto
                                                where id_boleto_conjuncion = ' || v_parametros.id_boleto|| '),''''))::varchar as billete ,
                        b.localizador,
                        b.endoso,
                        (mon.codigo_internacional || '' '' || b.neto)::varchar as neto,
                        (mon.codigo_internacional || '' '' || coalesce(su.importe,0) + b.neto || ''('' || b.neto || '' D. TARIFA, '' || coalesce(su.impuesto, '''') || '')'')::varchar as sujeto,
                        (mon.codigo_internacional || '' '' || coalesce(t.importe,0) + coalesce(b.xt,0))::varchar as tasas,
                        (mon.codigo_internacional || '' '' || b.total)::varchar as total,
                        fp.forma_pago,
                        
                        b.pasajero,
                        (case when substr(b.identificacion,1,2) = ''NI'' then
                        	''DNI / NATIONAL ID''
                        when substr(b.identificacion,1,2) = ''PA'' or substr(b.identificacion,1,2) = ''PP'' then
                        	''PASAPORTE / PASSPORT''
                        else
                        	''''
                        end)::varchar as tipo_identificacion,
                        substr(b.identificacion,3)::varchar as identificacion,
                        pais.codigo::varchar as pais,
                        ori.pais as origen
                                                
                        from  obingresos.tboleto b
                        inner join vef.tpunto_venta pv on b.id_punto_venta = pv.id_punto_venta
                        inner join vef.tsucursal s on s.id_sucursal = pv.id_sucursal
                        inner join param.tlugar pais on pais.id_lugar = param.f_get_id_lugar_pais(s.id_lugar)
                        inner join param.tentidad e on e.id_entidad = s.id_entidad
                        inner join param.tmoneda mon on mon.id_moneda = b.id_moneda_boleto
                        left join sujeto as su on su.id_boleto = b.id_boleto
                        left join tasas t on t.id_boleto = b.id_boleto
                        left join forma_pago fp on fp.id_boleto = b.id_boleto 
                        left join origen ori on ori.id_boleto = b.id_boleto                       
                        where b.id_boleto =  ' || v_parametros.id_boleto;

        raise notice '%',v_consulta;
        --Devuelve la respuesta
        return v_consulta;

      end;

    /*********************************
  #TRANSACCION:  'OBING_BOLFACDET_SEL'
  #DESCRIPCION:	Detalle de vuelos por boleto
  #AUTOR:		jrivera
  #FECHA:		06-01-2016 22:42:25
 ***********************************/

    elsif(p_transaccion='OBING_BOLFACDET_SEL')then

      begin
        --Sentencia de la consulta de conteo de registros
        v_consulta:='select to_char(bv.fecha,''DD MON YYYY'')::varchar as fecha,
                          bv.vuelo,(lo.nombre || '' ('' || ao.codigo || '')'')::varchar as desde, (ld.nombre || '' ('' || ad.codigo || '')'')::varchar as hacia,
                          to_char(bv.hora_origen,''HH:MI'')::varchar as hora_origen,to_char(bv.hora_destino,''HH:MI'')::varchar as hora_destino,
                          bv.tarifa, bv.equipaje
                          from obingresos.tboleto_vuelo bv
                          inner join obingresos.taeropuerto ao on bv.id_aeropuerto_origen = ao.id_aeropuerto
                          inner join obingresos.taeropuerto ad on bv.id_aeropuerto_destino = ad.id_aeropuerto
                          inner join param.tlugar lo on lo.id_lugar = ao.id_lugar
                          inner join param.tlugar ld on ld.id_lugar = ad.id_lugar
                          where bv.id_boleto = ' || v_parametros.id_boleto|| ' or bv.id_boleto_conjuncion = ' || v_parametros.id_boleto|| ' 
                          order by bv.id_boleto_vuelo ASC';


        --Devuelve la respuesta
        return v_consulta;

      end;
    /*********************************
  #TRANSACCION:  'OBING_BOLSERV_SEL'
  #DESCRIPCION:	DDevuelve un boleto en base al parametro enviado (para servicio)
  #AUTOR:		jrivera
  #FECHA:		06-01-2016 22:42:25
 ***********************************/

    elsif(p_transaccion='OBING_BOLSERV_SEL')then

      begin

        if (exists (select 1 from obingresos.tboleto
        where nro_boleto = v_parametros.nro_boleto and voided = 'si')) then
          raise exception 'El boleto ha sido anulado';
        end if;
        --Sentencia de la consulta de conteo de registros
        v_consulta:='	with boleto as (
            				select b.id_boleto,b.nro_boleto,b.pasajero,to_char(b.fecha_emision,''DD/MM/YYYY''),b.moneda,b.total,b.neto,b.nit,b.razon,b.localizador
            				from obingresos.tboleto b
                            where b.estado_reg = ''activo'' and b.nro_boleto = ''' || v_parametros.nro_boleto || '''
                            ),
                            boleto_vuelo as (
                            select b.id_boleto,array_to_json(array_agg(bv.*)) as detalle
                            from obingresos.tboleto_vuelo bv
                            inner join obingresos.tboleto b on b.id_boleto = bv.id_boleto
                            where b.estado_reg = ''activo'' and b.nro_boleto = ''' || v_parametros.nro_boleto || '''
                            group by b.id_boleto) 
                            select row_to_json(b.*) as boleto,bv.detalle
                            from boleto b 
                            inner join boleto_vuelo bv on bv.id_boleto = b.id_boleto
                            ';


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