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
    v_conexion			varchar;
	--variables boleto exchange
    v_record_json		jsonb;

    v_record_json_aux	jsonb;
	v_ciudad_o			record;
    v_ciudad_d			record;
    v_record 			record;
    v_contador_id		integer=1;

    v_array_json		jsonb[];
    v_oficina			record;
    v_calculo_tarifa	varchar = '';
    v_tasa				varchar = '';
    v_importe			numeric = 0;
    v_tipo_cambio		numeric;
    v_cadena			varchar[];
    v_exch_sel			integer[];
    v_cont_nacional		integer = 0;
    v_cont_regional		integer = 0;
    v_cont_inter		integer = 0;
    v_tipo_emision		varchar='F';
    v_contador_exch		integer = 0;
    v_record_exch		record;
    v_max_exch			varchar;
    v_nro_boleto		varchar;
    v_code_exch			varchar;

    --codigo tarifa
    v_codigo_tarifa		varchar;
    v_tam_cod_tarifa	integer;

    v_posicion			integer;
    v_pasajero			varchar;
    v_numero_billete  	varchar;

    v_fecha_emision 	varchar;
    v_numero_tkt      	varchar;
    v_tasa_valida		varchar = 'reject';
    v_pais_o		  	varchar;
    v_pais_d		  	varchar;

    v_tipo_19			varchar;
    v_cont_pasajero   	integer;
    v_pasajero_aux		varchar;
    v_datos				record;

    v_contador			integer;
    v_id_usuario		integer;

    v_agente_venta		varchar;
    v_puntos_venta		INTEGER[];
    v_puntos_venta_counter	record;
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
                                array_agg(mon.codigo_internacional) as moneda_fp,
                                sum(param.f_convertir_moneda(fp.id_moneda,bol.id_moneda_boleto,bfp.importe,bol.fecha_emision,''O'',2)) as monto_total_fp

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
                        bol.ruta_completa,
                        bol.voided,
                        forpa.monto_total_fp,
                        bol.mensaje_error,
                        bv.id_boleto_vuelo,
                        (bv.aeropuerto_origen || ''-'' || bv.aeropuerto_destino)::varchar as vuelo_retorno,
                        bol.localizador
						from obingresos.tboleto bol
                        left join obingresos.tagencia age on age.id_agencia = bol.id_agencia
                        left join obingresos.tboleto_vuelo bv on bv.id_boleto = bol.id_boleto and bv.retorno = ''si''
                        left join forma_pago_temporal forpa on forpa.id_boleto = bol.id_boleto
                        inner join param.tmoneda mon on mon.id_moneda = bol.id_moneda_boleto
						inner join segu.tusuario usu1 on usu1.id_usuario = bol.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = bol.id_usuario_mod
				        where bol.estado_reg = ''activo'' and ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
        v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;
        raise notice '%', v_consulta;
			--Devuelve la respuesta
			return v_consulta;

		end;


	/*********************************
     #TRANSACCION:  'OBING_PNRBOL_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		Gonzalo Sarmiento
 	#FECHA:		14-07-2017
	***********************************/

    elsif(p_transaccion='OBING_PNRBOL_SEL')then

		begin
			v_consulta:='with formas_pago as(select localizador,
                               array_agg(fp.id_forma_pago) as ids_forma_pago,
                               array_agg(fp.nombre||'' - ''||mon.codigo_internacional)::varchar[] as formas_pago,
                               array_agg(pfp.importe) as importes
                        from obingresos.vpnr pn
                        inner join obingresos.tpnr_forma_pago pfp on pfp.pnr = pn.localizador
                        inner join obingresos.tforma_pago fp on fp.id_forma_pago=pfp.id_forma_pago
                        inner join param.tmoneda mon on mon.id_moneda=fp.id_moneda
                        group by localizador)
                        select
                        nr.localizador,
                               nr.total,
                               nr.comision,
                               nr.liquido,
                               nr.id_moneda_boleto,
                               nr.moneda,
                               nr.neto,
                               nr.origen,
                               nr.destino,
                               nr.fecha_emision,
                               nr.boletos,
                               nr.pasajeros,
                               fpo.ids_forma_pago[1] as id_forma_pago,
                               fpo.formas_pago[1] as forma_pago,
                               fpo.importes[1] as monto_forma_pago,
                               fpo.ids_forma_pago[2] as id_forma_pago2,
                               fpo.formas_pago[2] as forma_pago2,
                               fpo.importes[2] as monto_forma_pago2
                        from obingresos.vpnr nr
                        left join formas_pago fpo on fpo.localizador=nr.localizador
                        where ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
            v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;
			raise notice 'v_consulta %',v_consulta;
			--Devuelve la respuesta
			return v_consulta;

		end;

    /*********************************
     #TRANSACCION:  'OBING_BOLEMI_SEL'
 	#DESCRIPCION:	Consulta de datos boletos emitidos de amadeus
 	#AUTOR:		Gonzalo Sarmiento
 	#FECHA:		26-09-2017
	***********************************/

    elsif(p_transaccion='OBING_BOLEMI_SEL')then

		begin
        --raise exception 'id: %',v_parametros.filtro;
        	v_consulta:='with forma_pago_temporal as(
                                          select bol.id_boleto_amadeus,
                                                 array_agg(fp.id_forma_pago) as id_forma_pago,
                                                 array_agg(mon.codigo_internacional) as moneda_fp,
                                                 array_agg(fp.nombre || '' - '' || mon.codigo_internacional) as forma_pago,
                                                 array_agg(fp.codigo) as codigo_forma_pago,
                                                 array_agg(bfp.numero_tarjeta) as numero_tarjeta,
                                                 array_agg(bfp.mco) as mco,
                                                 array_agg(bfp.codigo_tarjeta) as codigo_tarjeta,
                                                 array_agg(bfp.id_auxiliar) as id_auxiliar,
                                                 array_agg(aux.nombre_auxiliar) as nombre_auxiliar,
                                                 array_agg(bfp.importe) as monto_forma_pago
                                          from obingresos.tboleto_amadeus bol
                                               left join obingresos.tboleto_amadeus_forma_pago bfp on bfp.id_boleto_amadeus=bol.id_boleto_amadeus
                                               left join obingresos.tforma_pago fp on fp.id_forma_pago = bfp.id_forma_pago
                                               left join param.tmoneda mon on mon.id_moneda = fp.id_moneda
                                               left join conta.tauxiliar aux on aux.id_auxiliar=bfp.id_auxiliar
                                          where ' || v_parametros.filtro || '
                                          group by bol.id_boleto_amadeus)
                          select nr.id_boleto_amadeus,
                          		 nr.localizador,
                                 nr.total,
                                 case when nr.id_moneda_boleto=suc.id_moneda then round(nr.total/nr.tc,2)
                                 else nr.total end as total_moneda_extranjera,
                                 nr.liquido,
                                 nr.id_moneda_boleto,
                                 nr.moneda,
                                 mon.codigo_internacional as moneda_sucursal,
                                 nr.tc,
                                 nr.neto,
                                 nr.comision,
                                 nr.fecha_emision,
                                 nr.tipo_comision,
                                 substring(nr.nro_boleto from 4)::varchar as nro_boleto,
                                 nr.pasajero,
                                 nr.voided,
                                 nr.estado,
                                 usu.desc_persona::varchar as agente_venta,
                                 nr.agente_venta as codigo_agente,
                                 nr.forma_pago as forma_pago_amadeus,
                                 fpo.id_forma_pago [ 1 ]::integer as id_forma_pago,
                                 fpo.moneda_fp [ 1 ]::varchar as moneda_fp1,
                                 fpo.forma_pago [ 1 ]::varchar as forma_pago,
                                 fpo.codigo_forma_pago [ 1 ]::varchar as codigo_forma_pago,
                                 fpo.numero_tarjeta [ 1 ]::varchar as numero_tarjeta,
                                 fpo.codigo_tarjeta [ 1 ]::varchar as codigo_tarjeta,
                                 fpo.mco [ 1 ]::varchar as mco,
                                 fpo.id_auxiliar [ 1 ]::integer as id_auxiliar,
                                 fpo.nombre_auxiliar [ 1 ]::varchar as nombre_auxiliar,
                                 fpo.monto_forma_pago [ 1 ]::numeric as monto_forma_pago,
                                 fpo.id_forma_pago [ 2 ]::integer as id_forma_pago2,
                                 fpo.moneda_fp [ 2 ]::varchar as moneda_fp2,
                                 fpo.forma_pago [ 2 ]::varchar as forma_pago2,
                                 fpo.codigo_forma_pago [ 2 ]::varchar as codigo_forma_pago2,
                                 fpo.numero_tarjeta [ 2 ]::varchar as numero_tarjeta2,
                                 fpo.codigo_tarjeta [ 2 ]::varchar as codigo_tarjeta2,
                                 fpo.mco [ 2 ]::varchar as mco2,
                                 fpo.id_auxiliar [ 2 ]::integer as id_auxiliar2,
                                 fpo.nombre_auxiliar [ 2 ]::varchar as nombre_auxiliar2,
                                 fpo.monto_forma_pago [ 2 ]::numeric as monto_forma_pago2,
                                 cvf.ffid as ffid_consul,
                                 substring(cvf.voucher_code from 9)::varchar as voucher_consul,

                                 nr.trans_code,
                         		 nr.trans_issue_indicator,
                         		 pv.nombre as punto_venta,
                                 nr.trans_code_exch,
                                 nr.impreso
                          from obingresos.tboleto_amadeus nr
                          inner join vef.tpunto_venta pv on pv.id_punto_venta=nr.id_punto_venta
                          inner join vef.tsucursal_moneda suc on suc.id_sucursal=pv.id_sucursal and suc.tipo_moneda=''moneda_base''
                          inner join param.tmoneda  mon on mon.id_moneda=suc.id_moneda
                          inner join forma_pago_temporal fpo on fpo.id_boleto_amadeus=nr.id_boleto_amadeus
                          left join segu.tusuario_externo usuex on usuex.usuario_externo=nr.agente_venta
                          left join segu.vusuario usu on usu.id_usuario=usuex.id_usuario
                          left join obingresos.tviajero_frecuente v on v.id_boleto_amadeus = nr.id_boleto_amadeus
                          left join obingresos.tconsulta_viajero_frecuente cvf on cvf.nro_boleto = nr.nro_boleto';

            --Definicion de la respuesta
			--v_consulta:=v_consulta||v_parametros.filtro;
        	v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;
        	raise notice 'v_consulta %', v_consulta;
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
					    where  bol.estado_reg = ''activo'' and  ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			raise notice 'v_consulta %', v_consulta;
			--Devuelve la respuesta
			return v_consulta;

		end;

    /*********************************
     #TRANSACCION:  'OBING_PNRBOL_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		Gonzalo Sarmiento
 	#FECHA:		14-07-2017
	***********************************/

    elsif(p_transaccion='OBING_PNRBOL_CONT')then

		begin
			--Sentencia de la consulta de conteo de registros
            v_consulta:='select count(localizador)
						from obingresos.vpnr nr
                        where ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			raise notice 'v_consulta %',v_consulta;
			--Devuelve la respuesta
			return v_consulta;

		end;

    /*********************************
     #TRANSACCION:  'OBING_BOLEMI_CONT'
 	#DESCRIPCION:	Conteo de registros boletos emitidos amadeus
 	#AUTOR:		Gonzalo Sarmiento
 	#FECHA:		26-09-2017
	***********************************/

    elsif(p_transaccion='OBING_BOLEMI_CONT')then

		begin
			--Sentencia de la consulta de conteo de registros
            v_consulta:='select count(bol.id_boleto_amadeus)
						from obingresos.tboleto_amadeus bol
                        where ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			raise notice 'v_consulta %',v_consulta;
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
        select ((sum(COALESCE(bv.tiempo_conexion,0)) /60 ) || 'h' || (sum(COALESCE(bv.tiempo_conexion,0)) % 60 ) || 'm')::varchar as conexion
        	into v_conexion
		from obingresos.tboleto_vuelo bv
        where (bv.id_boleto = v_parametros.id_boleto or bv.id_boleto_conjuncion = v_parametros.id_boleto) and cupon != 1 and retorno != 'si';
        if (v_conexion is null) then
        	v_conexion = '';
        end if;
        	--Sentencia de la consulta de conteo de registros
        v_consulta:='
    with
    origen as (select bv.id_boleto, l.codigo as pais
				from obingresos.tboleto_vuelo bv
                inner join obingresos.taeropuerto a on a.id_aeropuerto = bv.id_aeropuerto_origen
                inner join param.tlugar l on l.id_lugar = param.f_get_id_lugar_pais(a.id_lugar)
                where bv.id_boleto =  ' || v_parametros.id_boleto|| '
                order by bv.id_boleto_vuelo ASC limit 1 offset 0) ,
    tasas as (select bt.id_boleto, sum(bt.importe) as importe,pxp.list((bt.importe || '' '' || t.codigo)::varchar)::varchar as tasas
            from obingresos.tboleto_impuesto bt
            inner join obingresos.timpuesto t on t.id_impuesto = bt.id_impuesto
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
                        b.identificacion::varchar as identificacion,
                        pais.codigo::varchar as pais,
                        ori.pais as origen,
                        s.direccion,
                        s.telefono,
                        b.fare_calc,
                        (t.tasas ||  (case when b.xt is null or b.xt = 0
                        						then ''''
                        						else '','' || b.xt || '' XT''
                                                end))::varchar as detalle_tasas,
                        ''' || v_conexion || '''::varchar as conexion

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
        v_consulta:='select to_char(bv.fecha_hora_origen,''DDMON'')::varchar as fecha_origen,
        					to_char(bv.fecha_hora_destino,''DDMON'')::varchar as fecha_origen,
                          (bv.linea || bv.vuelo)::varchar,
                          (lo.nombre || '' ('' || ao.codigo || '')'')::varchar as desde,
                          (ld.nombre || '' ('' || ad.codigo || '')'')::varchar as hacia,
                          to_char(bv.fecha_hora_origen,''HH24MI'')::varchar as hora_origen,
                          to_char(bv.fecha_hora_destino,''HH24MI'')::varchar as hora_destino,
                          bv.tarifa,
                          bv.equipaje,
                          bv.clase,
                          bv.cupon,
                          bv.flight_status,
                          ((bv.tiempo_conexion /60 ) || ''h'' || (bv.tiempo_conexion % 60 ) || ''m'')::varchar as conexion,
                          bv.retorno,
                          bv.validez_tarifa,
                          po.codigo as pais_origen,
                          pd.codigo as pais_destino
                          from obingresos.tboleto_vuelo bv
                          inner join obingresos.taeropuerto ao on bv.id_aeropuerto_origen = ao.id_aeropuerto
                          inner join obingresos.taeropuerto ad on bv.id_aeropuerto_destino = ad.id_aeropuerto
                          inner join param.tlugar lo on lo.id_lugar = ao.id_lugar
                          inner join param.tlugar ld on ld.id_lugar = ad.id_lugar
                          inner join param.tlugar po on po.id_lugar = param.f_get_id_lugar_pais(lo.id_lugar)
                          inner join param.tlugar pd on pd.id_lugar = param.f_get_id_lugar_pais(ld.id_lugar)
                          where bv.id_boleto = ' || v_parametros.id_boleto|| ' or bv.id_boleto_conjuncion = ' || v_parametros.id_boleto|| '
                          order by bv.cupon ASC';


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
            				select b.id_boleto,b.nro_boleto,b.pasajero,to_char(b.fecha_emision,''DD/MM/YYYY'') as fecha_emision,b.moneda,b.total,b.neto,b.nit,b.razon,b.localizador
            				from obingresos.tboleto b
                            where b.estado_reg = ''activo'' and b.nro_boleto = ''' || v_parametros.nro_boleto || '''
                            ),
                            boleto_vuelo as (
                              select b.id_boleto,array_to_json(array_agg(bv.*)) as detalle
                              from obingresos.tboleto_vuelo bv
                              inner join obingresos.tboleto b on b.id_boleto = bv.id_boleto
                              where b.estado_reg = ''activo'' and b.nro_boleto = ''' || v_parametros.nro_boleto || '''
                              group by b.id_boleto),
                            boleto_fp as (
                            	select b.id_boleto,bfp.importe,fp.codigo as codigo_forma_pago,fp.nombre as forma_pago,m.codigo_internacional as moneda
                                from obingresos.tboleto_forma_pago bfp
                                inner join obingresos.tboleto b on b.id_boleto = bfp.id_boleto
                                inner join obingresos.tforma_pago fp on fp.id_forma_pago = bfp.id_forma_pago
                                inner join param.tmoneda m on m.id_moneda =  fp.id_moneda
                                where b.estado_reg = ''activo'' and b.nro_boleto = ''' || v_parametros.nro_boleto || '''
                            ),
                            boleto_fp_2 as (
                            	select bfp.id_boleto,array_to_json(array_agg(bfp.*)) as pagos
                                from boleto_fp bfp
                                group by bfp.id_boleto
                            )

                            select row_to_json(b.*) as boleto,bv.detalle,bfp.pagos
                            from boleto b
                            inner join boleto_vuelo bv on bv.id_boleto = b.id_boleto
                            inner join boleto_fp_2 bfp on bfp.id_boleto = b.id_boleto
                            ';


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
      #TRANSACCION: 'OBING_BOL_EXCH_SEL'
      #DESCRIPCION:	Crea la estructura de factura Exchange
      #AUTOR:		franklin.espinoza
      #FECHA:		01-10-2018 12:42:25
     ***********************************/
    elsif(p_transaccion='OBING_BOL_EXCH_SEL')then

      begin
          if v_parametros.tipo = 'exchange' then

            select tag.nombre, tag.codigo, ts.telefono, ts.direccion
            into v_oficina
            from obingresos.tagencia tag
            inner join vef.tpunto_venta tpv on tpv.codigo = tag.codigo
            inner join vef.tsucursal ts on ts.id_sucursal = tpv.id_sucursal
            where tag.codigo_int = v_parametros.localizador->>'pv' and tag.estado_reg = 'activo';


            select tba.nro_boleto
            into v_numero_billete
            from obingresos.tboleto_amadeus tba
            where tba.id_boleto_amadeus = v_parametros.id_boletos_amadeus::integer;

            v_exch_sel = string_to_array(v_parametros.id_boletos_amadeus,',');

            select tba.*
            into v_record_exch
            from obingresos.tboleto_amadeus tba
            where tba.id_boleto_amadeus = any(v_exch_sel);

            create temp table ttpasajero(
                  id_pasajero		integer,
                  nombre			varchar,
                  importe			numeric,
                  moneda			varchar,
                  posicion			integer,
                  numero_tkt		varchar,
                  tipo_19			varchar
            )on commit drop;

			select tba.pasajero
            into v_pasajero_aux
            from obingresos.tboleto_amadeus tba
            where tba.id_boleto_amadeus = v_parametros.id_boletos_amadeus::integer;


            if v_pasajero_aux is null then v_pasajero_aux = 'ISMAEL ANTHONY/MATEO DAV(CHD)'; end if;

            --mas de un pasajero
			if jsonb_typeof(v_parametros.pasajeros->'pasajeroDR') = 'array' then
           		for v_record_json in SELECT * FROM jsonb_array_elements(v_parametros.pasajeros->'pasajeroDR')  loop

					if similarity_dist(v_pasajero_aux, v_record_json->>'apdos_nombre') = 0 or similarity_dist(v_pasajero_aux, v_record_json->>'apdos_nombre') <= 0.37 then
                      insert into ttpasajero(
                        id_pasajero,
                        nombre,
                        importe,
                        moneda,
                        posicion,
                        numero_tkt,
                        tipo_19
                      )values (
                          v_contador_id,
                          v_record_json->>'apdos_nombre',
                          (v_record_json->'pago'->>'importe')::numeric,
                          v_record_json->'pago'->>'moneda',
                          (v_record_json->'posicion'->>'numLinea')::integer,
                          (v_record_json->'Tkts'->>'string')::varchar,
                          (v_record_json->>'tipo_19')::varchar
                      );
                      v_contador_id = v_contador_id + 1;
                    end if;
            	end loop;
            else
              insert into ttpasajero(
                    id_pasajero,
                    nombre,
                    importe,
                    moneda,
                    posicion,
                    numero_tkt,
                    tipo_19
              )values (
                  v_contador_id,
                  v_parametros.pasajeros->'pasajeroDR'->>'apdos_nombre',
                  (v_parametros.pasajeros->'pasajeroDR'->'pago'->>'importe')::numeric,
                  v_parametros.pasajeros->'pasajeroDR'->'pago'->>'moneda',
                  (v_parametros.pasajeros->'pasajeroDR'->'posicion'->>'numLinea')::integer,
                  (v_parametros.pasajeros->'pasajeroDR'->'Tkts'->>'string')::varchar,
                  (v_parametros.pasajeros->'pasajeroDR'->>'tipo_19')::varchar
              );
            end if;

            /*select tba.nro_boleto
            into v_numero_tkt
            from obingresos.tboleto_amadeus tba
            where tba.id_boleto_amadeus = v_parametros.id_boletos_amadeus::integer;*/

           /* select tba.pasajero
            into v_pasajero_aux
            from obingresos.tboleto_amadeus tba
            where tba.id_boleto_amadeus = v_parametros.id_boletos_amadeus::integer;

            if v_pasajero_aux is null then v_pasajero_aux = 'ISMAEL ANTHONY/MATEO DAV(CHD)'; end if;*/

/*select count(tp.nombre)::varchar into v_pasajero
from ttpasajero tp;*/
--where id_pasajero = v_contador_id;
			--raise 'v_pasajero: %, %', v_pasajero, jsonb_typeof(v_parametros.pasajeros->'pasajeroDR');

			if jsonb_typeof(v_parametros.pasajeros->'pasajeroDR') = 'array' then

            	select tp.posicion, tp.nombre, tp.tipo_19, tp.id_pasajero
                into v_posicion, v_pasajero, v_tipo_19, v_contador_exch
                from ttpasajero tp
                where similarity_dist(v_pasajero_aux, tp.nombre) = 0;

                if v_pasajero is null then
                  select tp.posicion, tp.nombre, tp.tipo_19, tp.id_pasajero
                  into v_posicion, v_pasajero, v_tipo_19, v_contador_exch
                  from ttpasajero tp
                  where similarity_dist(v_pasajero_aux, tp.nombre) < 0.37;
                end if;
                 --raise 'a: %, b: %, c: %',v_posicion, v_pasajero, v_tipo_19;
            else
            	select tp.posicion, tp.nombre, tp.tipo_19
                into v_posicion, v_pasajero, v_tipo_19
                from ttpasajero tp
                where tp.nombre = v_parametros.pasajeros->'pasajeroDR'->>'apdos_nombre';
            end if;

            --'%'||v_pasajero||'%' ilike '%'||tp.nombre||'%' or '%'||tp.nombre||'%' ilike '%'||v_pasajero||'%';
            --or (string_to_array(v_pasajero,'/'))[1] ilike (string_to_array(tp.nombre,'/'))[1];
--raise 'AAA v_posicion %, v_pasajero %, v_tipo_19 %', v_posicion, v_pasajero, v_tipo_19;

            /*if strpos(v_pasajero_aux,'(') is not null and strpos(v_pasajero_aux,'(') is not null and v_pasajero is null then
                select tp.posicion, tp.nombre, tp.tipo_19
                into v_posicion, v_pasajero, v_tipo_19
                from ttpasajero tp
                where --word_similarity(v_pasajero, tp.nombre) > 0.8;
                      --similarity_dist('VALDA MORALES/JOAQ(CHD)(UMNR)', tp.nombre) < 0.37;VALDA MORALES/JOAQ(CHD)(UMNR)
                      similarity_dist(substr(v_pasajero_aux, 1, (POSITION('(' in v_pasajero_aux)-1)), tp.nombre) < 0.45;
            end if;*/

            --raise 'BBB v_posicion %, v_pasajero %, v_tipo_19 %, v_contador_exch: %', v_posicion, v_pasajero, v_tipo_19, v_contador_exch;

            v_contador_id = 1;
            create temp table ttasa(
                id_tasa			integer,
                calculo_tarifa	varchar,
                tasa			varchar,
                rc_iva			numeric,
                moneda_total	varchar,
                importe_total	varchar,
                moneda_tarifa   varchar,
                importe_tarifa	varchar,
                codigo_tarifa 	varchar,
                tipo_emision	varchar,
                tipo_tarifa		varchar,
                tipo_total		varchar,
                num_pax			varchar,
                inf				varchar,
                pasajero varchar
            )on commit drop;
--raise 'tipo: %', jsonb_typeof(v_parametros.fn_V2);
            if jsonb_typeof(v_parametros.fn_V2) = 'object' then
              v_importe = (v_parametros.importes->>'importe_total');

              if jsonb_typeof(v_parametros.tasa) = 'array' then
                for v_record_json in SELECT * FROM jsonb_array_elements(v_parametros.tasa)  loop
                    --raise exception 'tasa: %, %, %', v_record_json->>'tipo_tasa', v_record_json->>'importe_tasa',v_record_json->>'codigo_tasa';
                    if v_record_json->>'tipo_tasa' != 'X' then
                        v_calculo_tarifa = v_calculo_tarifa ||(v_record_json->>'importe_tasa')::varchar||(v_record_json->>'codigo_tasa')::varchar;
                    end if;

                    if v_record_json->>'tipo_tasa' = 'X' then
                        if v_record_json->>'codigo_tasa' not in ('QM', 'BO') then
                            v_importe = v_importe - (v_record_json->>'importe_tasa')::numeric;
                        end if;
                    end if;

                    v_tasa = v_tasa ||(v_record_json->>'moneda_tasa')::varchar||' '||case when v_record_json->>'tipo_tasa'= 'O' then 'PD '::varchar else ' '::varchar end ||(v_record_json->>'importe_tasa')::varchar||(v_record_json->>'codigo_tasa')::varchar||'		';
                end loop;
              else
              	if v_parametros.tasa->>'tipo_tasa' != 'X' then
                	v_calculo_tarifa = v_calculo_tarifa ||(v_parametros.tasa->>'importe_tasa')::varchar||(v_parametros.tasa->>'codigo_tasa')::varchar;
                end if;
                if v_parametros.tasa->>'tipo_tasa' = 'X' then
                    if v_parametros.tasa->>'codigo_tasa' not in ('QM', 'BO') then
                        v_importe = v_importe - (v_parametros.tasa->>'importe_tasa')::numeric;
                    end if;
                end if;
                v_tasa = v_tasa ||(v_parametros.tasa->>'moneda_tasa')::varchar||' '||case when v_parametros.tasa->>'tipo_tasa'= 'O' then 'PD '::varchar else ' '::varchar end ||(v_parametros.tasa->>'importe_tasa')::varchar||(v_parametros.tasa->>'codigo_tasa')::varchar||'		';
              end if;

              v_calculo_tarifa = ' PDXT '||v_calculo_tarifa;

              if jsonb_typeof(v_parametros.importes->'codigo_tarifa'->'string') = 'object' or jsonb_typeof(v_parametros.importes->'codigo_tarifa'->'string') = 'array' then
                	v_codigo_tarifa = v_parametros.fn_V2->>'codigo_tarifa';
              else
                	v_codigo_tarifa = v_parametros.importes->'codigo_tarifa'->'string';
              end if;

              insert into ttasa(
                  id_tasa,
                  calculo_tarifa,
                  tasa,
                  rc_iva,
                  moneda_total,
                  importe_total,
                  moneda_tarifa,
                  importe_tarifa,
                  codigo_tarifa,
                  tipo_emision,
                  tipo_tarifa,
                  tipo_total,
                  num_pax,
                  inf,
                  pasajero
              )values (
                v_contador_id,
                v_calculo_tarifa,
                v_tasa,
                v_importe,
                v_parametros.fn_V2->>'moneda_total',
                v_parametros.fn_V2->>'importe_total',
                v_parametros.fn_V2->>'moneda_tarifa',
                v_parametros.fn_V2->>'importe_tarifa',
                v_codigo_tarifa,--v_parametros.fn_V2->>'codigo_tarifa',
                v_parametros.fn_V2->>'tipo_emision',
                v_parametros.fn_V2->>'tipo_tarifa',
                v_parametros.fn_V2->>'tipo_total',
                v_parametros.fn_V2->>'num_pax',
                v_parametros.fn_V2->>'inf',
                v_pasajero
              );
			        v_tipo_emision = v_parametros.fn_V2->>'tipo_emision';
            else --raise 'tipo: %', jsonb_typeof(v_parametros.fn_V2);

                for v_record_json in SELECT * FROM jsonb_array_elements(v_parametros.fn_V2)  loop raise notice 'num_pax: %, %',v_record_json->>'num_pax',v_posicion;
                  if (v_record_json->>'num_pax')::integer=v_posicion and v_record_json->>'tipo_emision' = 'R' then
                  	--raise 'num_pax: %, %',v_record_json->>'num_pax',v_posicion;

                      if v_tipo_19 in ('INF') and v_record_json->>'inf' = 'N' then raise 'a';
                    	  continue;
                      elsif v_tipo_19 in ('CHD','ADT','SNN') and v_record_json->>'inf' = 'Y' then
                    	  continue;  raise 'b';
                      end if;

                      v_importe = (v_record_json->>'importe_total'); --raise 'v_importe: %',v_importe;
                      if jsonb_typeof(v_record_json->'Fntaxs'->'tasa') = 'array' then
                        for v_record_json_aux in SELECT * FROM jsonb_array_elements(v_record_json->'Fntaxs'->'tasa')  loop

                            if v_record_json_aux->>'tipo_tasa' != 'X' then
                                v_calculo_tarifa = v_calculo_tarifa ||(v_record_json_aux->>'importe_tasa')::varchar||(v_record_json_aux->>'codigo_tasa')::varchar;
                            end if;

                            if v_record_json_aux->>'tipo_tasa' = 'X' then
                                if v_record_json_aux->>'codigo_tasa' not in ('QM', 'BO') then
                                    v_importe = v_importe - (v_record_json_aux->>'importe_tasa')::numeric;
                                end if;
                            end if;

                            v_tasa = v_tasa ||(v_record_json_aux->>'moneda_tasa')::varchar||' '||case when v_record_json_aux->>'tipo_tasa'= 'O' then 'PD '::varchar else ' '::varchar end ||(v_record_json_aux->>'importe_tasa')::varchar||(v_record_json_aux->>'codigo_tasa')::varchar||'		';
                        end loop;
                        --raise 'v_calculo_tarifa: %, v_importe: %, v_tasa: %', v_calculo_tarifa,v_importe ,v_tasa;
                      else
                        if v_record_json->'Fntaxs'->'tasa'->>'tipo_tasa' != 'X' then
                            v_calculo_tarifa = v_calculo_tarifa ||(v_record_json->'Fntaxs'->'tasa'->>'importe_tasa')::varchar||(v_record_json->'Fntaxs'->'tasa'->>'codigo_tasa')::varchar;
                        end if;

                        if v_record_json->'Fntaxs'->'tasa'->>'tipo_tasa' = 'X' then
                            if v_record_json->'Fntaxs'->'tasa'->>'codigo_tasa' not in ('QM', 'BO') then
                                v_importe = v_importe - (v_record_json->'Fntaxs'->'tasa'->>'importe_tasa')::numeric;
                            end if;
                        end if;
                        v_tasa = v_tasa ||(v_record_json->'Fntaxs'->'tasa'->>'moneda_tasa')::varchar||' '||case when v_record_json->'Fntaxs'->'tasa'->>'tipo_tasa'= 'O' then 'PD '::varchar else ' '::varchar end ||(v_record_json->'Fntaxs'->'tasa'->>'importe_tasa')::varchar||(v_record_json->'Fntaxs'->'tasa'->>'codigo_tasa')::varchar||'		';
                      end if;
                      v_calculo_tarifa = ' PDXT '||v_calculo_tarifa;

                      IF jsonb_typeof(v_record_json->'codigo_tarifa'->'string') = 'string' then
                      	v_codigo_tarifa = v_record_json->'codigo_tarifa'->>'string';
                      else
                        SELECT array_agg(value)
                        into v_cadena
                        FROM jsonb_array_elements_text(v_record_json->'codigo_tarifa'->'string');
                        v_codigo_tarifa = v_cadena[v_contador_id];
                      end if;

                      select count(ta.id_tasa)
                      into v_cont_pasajero
                      from ttasa ta
                      where ta.pasajero = v_pasajero; --raise 'contador: %, %', v_cont_pasajero,v_codigo_tarifa;
                      --if v_tasa_valida = 'resolve' then
                      if /*v_importe > 0 and*/ v_cont_pasajero = 0 /*or ((v_record_json->>'importe_total')::numeric = 0 and v_cont_pasajero = 0)*/ then
                        insert into ttasa(
                          id_tasa,
                        calculo_tarifa,
                        tasa,
                        rc_iva,
                            moneda_total,
                            importe_total,
                            moneda_tarifa,
                            importe_tarifa,
                            codigo_tarifa,
                            tipo_emision,
                            tipo_tarifa,
                            tipo_total,
                            num_pax,
                            inf,
                            pasajero
                        )values (
                          v_contador_id,
                          v_calculo_tarifa,
                          v_tasa,
                          v_importe,
                          v_record_json->>'moneda_total',
                          v_record_json->>'importe_total',
                          v_record_json->>'moneda_tarifa',
                          v_record_json->>'importe_tarifa',
                          v_codigo_tarifa,--v_cadena[v_contador_id],
                          v_record_json->>'tipo_emision',
                          v_record_json->>'tipo_tarifa',
                          v_record_json->>'tipo_total',
                          v_record_json->>'num_pax',
                          v_record_json->>'inf',
                          v_pasajero
                        );
                        v_tipo_emision = v_record_json->>'tipo_emision';
                        v_contador_id = v_contador_id + 1;

                      else
                        v_tasa = '';
                      end if;
                  else
                    continue;
                  end if;
                end loop;
            end if;

/*select tt.*
into v_record
from public.ttasa tt;
raise 'ttasa: %', v_record;*/
            select max(tba.fecha_emision)::varchar
            into v_fecha_emision
            from obingresos.tboleto_amadeus tba
            where tba.localizador = v_parametros.pnr;

            v_contador_id = 1;
            create temp table tvuelos(
                id_vuelo		integer,
                clase 			varchar,
                linea 			varchar,
                estado 			varchar,
                origen 			varchar,
                destino 		varchar,
                num_vuelo 		varchar,
                hora_salida		varchar,
                fecha_salida	varchar,
                hora_llegada 	varchar,
                codigo_tarifa	varchar,
                origen_cod		varchar,
                destino_cod		varchar
            )on commit drop;


          if jsonb_typeof(v_parametros.vuelo) = 'array' and jsonb_typeof(v_parametros.fn_V2) = 'object' then

            if jsonb_typeof(v_parametros.importes->'codigo_tarifa'->'string') = 'object' or jsonb_typeof(v_parametros.importes->'codigo_tarifa'->'string') = 'array' then
   				    SELECT array_agg(value)
              into v_cadena
   				    FROM jsonb_array_elements_text(v_parametros.importes->'codigo_tarifa'->'string');
            else
              SELECT array_agg(tt.codigo_tarifa)
              into v_cadena
   				    FROM ttasa tt;
            end if;
          else
		  		  SELECT array_agg(tt.codigo_tarifa)
            into v_cadena
   				  FROM ttasa tt;
          end if;
--raise 'vuelos: %, %',  jsonb_typeof(v_parametros.vuelo), v_cadena;
            if jsonb_typeof(v_parametros.vuelo) = 'array' then
              v_tam_cod_tarifa = array_length(v_cadena,1);
              for v_record_json in SELECT * FROM jsonb_array_elements(v_parametros.vuelo)  loop

                  select tl.nombre, tl.codigo, tl.id_lugar
                  into v_ciudad_o
                  from param.tlugar tl
                  where tl.codigo::varchar = (v_record_json->>'origen')::varchar;

                  select tl.codigo
                  into v_pais_o
                  from param.tlugar tl
                  where tl.id_lugar = param.f_get_id_lugar_pais(v_ciudad_o.id_lugar);

                  select tl.nombre, tl.codigo, tl.id_lugar
                  into v_ciudad_d
                  from param.tlugar tl
                  where tl.codigo::varchar = (v_record_json->>'destino')::varchar;

                  select tl.codigo
                  into v_pais_d
                  from param.tlugar tl
                  where tl.id_lugar = param.f_get_id_lugar_pais(v_ciudad_d.id_lugar);

                  if (v_record_json->>'estado')::varchar != 'B' or ((v_record_json->>'estado')::varchar = 'B' and v_fecha_emision::date != current_date) then
                    --if (v_record_json->>'hora_salida')::time > current_time and (v_record_json->>'fecha_salida')::date >= current_date then
                      insert into tvuelos(
                        id_vuelo,
                        clase,
                        linea,
                        estado,
                        origen,
                        destino,
                        num_vuelo,
                        hora_salida,
                        fecha_salida,
                        hora_llegada,
                        codigo_tarifa,
                        origen_cod,
                		    destino_cod
                      )values (
                          v_contador_id,
                          (v_record_json->>'clase')::varchar,
                          (v_record_json->>'linea')::varchar,
                          (v_record_json->>'estado')::varchar,
                          (v_ciudad_o.nombre||' '||'('||(v_record_json->>'origen')::varchar||')')::varchar,
                          (v_ciudad_d.nombre||' '||'('||(v_record_json->>'destino')::varchar||')')::varchar,
                          (v_record_json->>'num_vuelo')::varchar,
                          (v_record_json->>'hora_salida')::varchar,
                          (v_record_json->>'fecha_salida')::varchar,
                          (v_record_json->>'hora_llegada')::varchar,
                          case when v_tam_cod_tarifa > 1 then v_cadena[v_contador_id]::varchar else  trim(v_cadena[1]::varchar,'"') end,
                          v_pais_o,
                          v_pais_d
                      );
                    --end if;
                  end if;
                  v_contador_id = v_contador_id + 1;
              end loop;
            else

                 select tl.nombre, tl.codigo, tl.id_lugar
                  into v_ciudad_o
                  from param.tlugar tl
                  where tl.codigo::varchar = (v_parametros.vuelo->>'origen')::varchar;

                  select tl.codigo
                  into v_pais_o
                  from param.tlugar tl
                  where tl.id_lugar = param.f_get_id_lugar_pais(v_ciudad_o.id_lugar);

                  select tl.nombre, tl.codigo, tl.id_lugar
                  into v_ciudad_d
                  from param.tlugar tl
                  where tl.codigo::varchar = (v_parametros.vuelo->>'destino')::varchar;

                  select tl.codigo
                  into v_pais_d
                  from param.tlugar tl
                  where tl.id_lugar = param.f_get_id_lugar_pais(v_ciudad_d.id_lugar);
--raise 'ruta: %, %, %, %',v_ciudad_o,v_pais_o,v_ciudad_d,v_pais_d;
                  if jsonb_typeof(v_parametros.importes->'codigo_tarifa'->'string') = 'object' or jsonb_typeof(v_parametros.importes->'codigo_tarifa'->'string') = 'array' then
                      SELECT array_agg(value)
                      into v_cadena
                      FROM jsonb_array_elements_text(v_parametros.importes->'codigo_tarifa'->'string');
                  else
                      SELECT array_agg(tt.codigo_tarifa)
                      into v_cadena
                      FROM ttasa tt;
                  end if;
                  --raise 'v_cadena: %', v_cadena;
                  if (v_parametros.vuelo->>'estado')::varchar != 'B' or ((v_parametros.vuelo->>'estado')::varchar = 'B' and v_fecha_emision::date != current_date) then
--raise 'entra';
                      insert into tvuelos(
                        id_vuelo,
                        clase,
                        linea,
                        estado,
                        origen,
                        destino,
                        num_vuelo,
                        hora_salida,
                        fecha_salida,
                        hora_llegada,
                        codigo_tarifa,
                        origen_cod,
                		    destino_cod
                      )values (
                          1,
                          (v_parametros.vuelo->>'clase')::varchar,
                          (v_parametros.vuelo->>'linea')::varchar,
                          (v_parametros.vuelo->>'estado')::varchar,
                          (v_ciudad_o.nombre||' '||'('||(v_parametros.vuelo->>'origen')::varchar||')')::varchar,
                          (v_ciudad_d.nombre||' '||'('||(v_parametros.vuelo->>'destino')::varchar||')')::varchar,
                          (v_parametros.vuelo->>'num_vuelo')::varchar,
                          (v_parametros.vuelo->>'hora_salida')::varchar,
                          (v_parametros.vuelo->>'fecha_salida')::varchar,
                          (v_parametros.vuelo->>'hora_llegada')::varchar,
                          case when (v_parametros.importes->'codigo_tarifa'->>'string') is null then v_cadena[1] else (v_parametros.importes->'codigo_tarifa'->>'string') end,
                          v_pais_o,
                          v_pais_d
                      );

                  end if;
            end if;
/*select *
into v_record
from tvuelos tt;
raise 'v_pasajero: %', v_record;*/

/*select tt.id_vuelo
into v_contador_exch
from tvuelos tt;
--where tt.pasajero = v_pasajero ;
raise 'tvuelo: %', v_contador_exch;*/

            select count(tba.id_boleto_amadeus)
            into v_contador_exch
            from obingresos.tboleto_amadeus tba
            where tba.localizador = v_parametros.pnr and tba.trans_code_exch = 'EXCH';



            v_nro_boleto = ('930'||v_parametros.nro_boleto);

            select tba.trans_code_exch
            into v_code_exch
            from obingresos.tboleto_amadeus tba
            where tba.nro_boleto = v_nro_boleto;

            if v_tipo_emision = 'R' and v_record_exch.trans_code = 'TKTT' and v_contador_exch = 0 and (v_code_exch is null or v_code_exch = '') then

                select max(tba.nro_boleto)
            	into v_max_exch
            	from obingresos.tboleto_amadeus tba
            	where tba.localizador = v_parametros.pnr and tba.trans_code != 'EMDS';

                if v_nro_boleto  > v_max_exch then
                  	update  obingresos.tboleto_amadeus set
                      impreso = 'si',
                      trans_code_exch = 'EXCH'
                    where nro_boleto = v_nro_boleto::varchar;
                  	--where id_boleto_amadeus = any(v_exch_sel);
                else
                	update  obingresos.tboleto_amadeus set
                      impreso = 'si',
                      trans_code_exch = 'EXCH'
                  	where nro_boleto = v_max_exch::varchar;
                    --where id_boleto_amadeus = any(v_exch_sel);
                end if;

            else

            	if v_code_exch = 'EXCH' then
                  	update  obingresos.tboleto_amadeus set
                    	impreso = 'si',
                    	trans_code_exch = 'EXCH'
                    where nro_boleto = v_nro_boleto::varchar;
                  	--where id_boleto_amadeus = any(v_exch_sel);
                else
                	update  obingresos.tboleto_amadeus set
                    	impreso = 'si',
                    	trans_code_exch = 'ORIG'
                    where nro_boleto = v_nro_boleto::varchar;
                  	--where id_boleto_amadeus = any(v_exch_sel);
                end if;
            end if;

			/*select tba.tc
            into v_tipo_cambio
            from  obingresos.tboleto_amadeus tba
            where tba.code_a = 'TKTT' and tba.tc is not null and tba.localizador =  (v_parametros.localizador->>'localizador_resiber')::varchar ;*/

            select tc.oficial
            INTO v_tipo_cambio
			      from param.ttipo_cambio tc
			      where tc.fecha = to_date((v_parametros.localizador->>'fecha_creacion')::varchar,'ddmmyy') and tc.id_moneda = 2;


            /*v_consulta:='
                          select
                          tv.id_vuelo,
                          tv.clase,
                          tv.linea,
                          tv.estado,
                          tv.origen,
                          tv.destino,
                          tv.num_vuelo,
                          tv.hora_salida,
                          tv.fecha_salida,
                          tv.hora_llegada,
                          tv.codigo_tarifa as codigo_tarifa,
                          '''||COALESCE(v_calculo_tarifa,'')||'''::varchar as calculo_tarifa,
                          '''||COALESCE(v_tasa,'')||'''::varchar as tasa,
                          '||COALESCE(v_importe,0)||'::numeric as rc_iva,
                          '''|| COALESCE((v_parametros.ssrs->'ssr'->>'texto'),'')||'''::varchar as forma_identificacion,
                          '''||(v_parametros.importes->>'moneda_total')||' '||(v_parametros.importes->>'importe_total')||'''::varchar as importe_total,
                          '''||(v_parametros.importes->>'moneda_tarifa')||' '||(v_parametros.importes->>'importe_tarifa')||'''::varchar as importe_tarifa,
                          '''|| (v_parametros.responsable->>'tipo_reserva')||'''::varchar as agente,
                          '''||COALESCE(v_oficina.nombre,'')||'''::varchar as nombre_ofi,
                          '''||COALESCE(v_oficina.codigo,'')||'''::varchar as codigo_iata,
                          '''||COALESCE(v_oficina.telefono,'')||'''::varchar as telefono_ofi,
                          '''||COALESCE(v_oficina.direccion,'')||'''::varchar as direccion_ofi,
                          '||COALESCE(v_tipo_cambio,0)||'::numeric as tipo_cambio,
                          '''||coalesce((v_parametros.localizador->'endosos'->'endoso'->>'texto'),'')||'''::varchar as endoso,
                          '''||to_date((v_parametros.localizador->>'fecha_creacion')::varchar,'ddmmyy')||'''::date as fecha_create,
                          '''||(v_parametros.importes->>'moneda_tarifa')||'''::varchar as moneda_iva
                          from tvuelos tv

            ';*/

            select
            tv.id_vuelo,
            tv.clase,
            tv.linea,
            tv.estado,
            tv.origen,
            tv.destino,
            tv.num_vuelo,
            tv.hora_salida,
            tv.fecha_salida,
            tv.hora_llegada,
            tv.codigo_tarifa as codigo_tarifa,
            ts.calculo_tarifa as calculo_tarifa,
            ts.tasa as tasa,
            ts.rc_iva as rc_iva,
            COALESCE((v_parametros.ssrs->'ssr'->>'texto'),'') as forma_identificacion,
            (ts.moneda_total ||' '|| ts.importe_total)::varchar as importe_total,
            (ts.moneda_tarifa ||' '|| ts.importe_tarifa)::varchar as importe_tarifa,
            coalesce((v_parametros.responsable->>'tipo_reserva'),'')::varchar as agente,
            COALESCE(v_oficina.nombre,'')::varchar as nombre_ofi,
            COALESCE(v_oficina.codigo,'')::varchar as codigo_iata,
            COALESCE(v_oficina.telefono,'')::varchar as telefono_ofi,
            COALESCE(v_oficina.direccion,'')::varchar as direccion_ofi,
            COALESCE(v_tipo_cambio,0)::numeric as tipo_cambio,
            coalesce((v_parametros.localizador->'endosos'->'endoso'->>'texto'),'')::varchar as endoso,
            coalesce((v_parametros.update->>'fecha_upd')::varchar, v_fecha_emision)::varchar as fecha_create,
            ts.moneda_tarifa::varchar as moneda_iva,
            v_tipo_emision::varchar as tipo_emision,
            ts.moneda_tarifa,
            v_pasajero::varchar as pasajero,
            v_numero_billete::varchar as numero_billete,
            tv.origen_cod,
            tv.destino_cod
            into v_record

            from tvuelos tv
            cross join ttasa ts ;
             raise 'v_record: %', v_record;


          	v_consulta:='
                          select
                          tv.id_vuelo,
                          tv.clase,
                          tv.linea,
                          tv.estado,
                          tv.origen,
                          tv.destino,
                          tv.num_vuelo,
                          tv.hora_salida,
                          tv.fecha_salida,
                          tv.hora_llegada,
                          tv.codigo_tarifa::varchar as codigo_tarifa,
                          ts.calculo_tarifa::varchar as calculo_tarifa,
                          ts.tasa::varchar as tasa,
                          ts.rc_iva as rc_iva,
                          '''|| COALESCE((v_parametros.ssrs->'ssr'->>'texto'),'')||'''::varchar as forma_identificacion,
                          (ts.moneda_total ||'' ''|| ts.importe_total)::varchar as importe_total,
                          (ts.moneda_tarifa ||'' ''|| ts.importe_tarifa)::varchar as importe_tarifa,
                          '''|| coalesce((v_parametros.responsable->>'tipo_reserva'),'')||'''::varchar as agente,
                          '''||COALESCE(v_oficina.nombre,'')||'''::varchar as nombre_ofi,
                          '''||COALESCE(v_oficina.codigo,'')||'''::varchar as codigo_iata,
                          '''||COALESCE(v_oficina.telefono,'')||'''::varchar as telefono_ofi,
                          '''||COALESCE(v_oficina.direccion,'')||'''::varchar as direccion_ofi,
                          '||COALESCE(v_tipo_cambio,0)||'::numeric as tipo_cambio,
                          '''||coalesce((v_parametros.localizador->'endosos'->'endoso'->>'texto'),'')||'''::varchar as endoso,
                          '''||coalesce((v_parametros.update->>'fecha_upd')::varchar, v_fecha_emision)||'''::varchar as fecha_create,
                          ts.moneda_tarifa::varchar as moneda_iva,
                          '''||v_tipo_emision||'''::varchar as tipo_emision,
                          ts.moneda_tarifa,
                          '''||v_pasajero||'''::varchar as pasajero,
                          '''||v_numero_billete||'''::varchar as numero_billete,
                          tv.origen_cod,
                          tv.destino_cod
                          from tvuelos tv
                          cross join ttasa ts
            ';


                --Devuelve la respuesta
                return v_consulta;
          else

          end if;
		end;

	/*********************************
 	#TRANSACCION:  'OBING_SAL_BOL_SEL'
 	#DESCRIPCION:	Consulta de Boletos Vendidos
 	#AUTOR:		franklin.espinoza
 	#FECHA:		09-04-2019 20:28:10
	***********************************/

	elsif(p_transaccion='OBING_SAL_BOL_SEL')then

       begin
    		--Sentencia de la consulta

            /*Cambiando la consulta para separar moneda local y moneda exranjera (Ismael Valdivia 26/02/2020)*/

            if (v_parametros.tipo_interfaz = 'ResumenDetalle') then

            CREATE TEMPORARY TABLE datos_amadeus ( id_boleto_amadeus  int4,
                                      pasajero varchar,
                                      localizador varchar,
                                      nro_boleto varchar,
          							  forma_pago_amadeus varchar,
                                      moneda varchar,
                                      precio_total_ml numeric,
                                      precio_total_me numeric,
                                      agente_venta varchar,
                                      id_forma_pago int4,
                                      monto_forma_pago_ml numeric,
                                      monto_forma_pago_me numeric,
                                      forma_pago varchar,
                                      fecha_emision date,
                                      trans_code varchar,
                                      trans_issue_indicator varchar,
                                      nombre varchar,
                                      trans_code_exch varchar,
                                      impreso varchar )ON COMMIT DROP;


           /* select ex.id_usuario into v_id_usuario
            from segu.tusuario_externo ex
            where ex.usuario_externo = v_parametros.agente_venta;*/


              For v_datos in (with forma_pago_temporal as(
                                            select bol.id_boleto_amadeus,
                                                   array_agg(fp.id_forma_pago) as id_forma_pago,
                                                   array_agg(mon.codigo_internacional) as moneda_fp,
                                                   array_agg(fp.nombre || ' - ' || mon.codigo_internacional) as forma_pago,
                                                   array_agg(fp.codigo) as codigo_forma_pago,
                                                   array_agg(bfp.numero_tarjeta) as numero_tarjeta,
                                                   array_agg(bfp.mco) as mco,
                                                   array_agg(bfp.codigo_tarjeta) as codigo_tarjeta,
                                                   array_agg(bfp.id_auxiliar) as id_auxiliar,
                                                   array_agg(aux.nombre_auxiliar) as nombre_auxiliar,
                                                   array_agg(bfp.importe) as monto_forma_pago
                                            from obingresos.tboleto_amadeus bol
                                                 left join obingresos.tboleto_amadeus_forma_pago bfp on bfp.id_boleto_amadeus=bol.id_boleto_amadeus
                                                 left join obingresos.tforma_pago fp on fp.id_forma_pago = bfp.id_forma_pago
                                                 left join param.tmoneda mon on mon.id_moneda = fp.id_moneda
                                                 left join conta.tauxiliar aux on aux.id_auxiliar=bfp.id_auxiliar
                                            where bol.fecha_emision between v_parametros.fecha_ini::date and v_parametros.fecha_fin::date
                                            group by bol.id_boleto_amadeus)

                            select nr.id_boleto_amadeus,
                                   nr.pasajero as pasajero,
                                   nr.localizador as localizador,
                                   substring(nr.nro_boleto from 4)::varchar as nro_boleto,
                                   nr.forma_pago as forma_pago_amadeus,
                                   nr.moneda,
                                   nr.total as precio_total,
                                   nr.agente_venta as codigo_agente,
                                   fpo.id_forma_pago [ 1 ]::integer as id_forma_pago,
                                   fpo.monto_forma_pago [ 1 ]::numeric as monto_forma_pago,
                                   fpo.forma_pago [ 1 ]::varchar as forma_pago,
                                   nr.fecha_emision,
                                   nr.trans_code,
                                   nr.trans_issue_indicator,
                                   pv.nombre as punto_venta,
                                   nr.trans_code_exch,
                                   nr.impreso,
                                   fpo.moneda_fp [ 1 ]::varchar as moneda_fp

                            from obingresos.tboleto_amadeus nr
                            inner join vef.tpunto_venta pv on pv.id_punto_venta=nr.id_punto_venta
                            inner join vef.tsucursal_moneda suc on suc.id_sucursal=pv.id_sucursal and suc.tipo_moneda='moneda_base'
                            inner join param.tmoneda mon on mon.id_moneda=suc.id_moneda
                            inner join forma_pago_temporal fpo on fpo.id_boleto_amadeus=nr.id_boleto_amadeus
                            left join segu.tusuario_externo usuex on usuex.usuario_externo=nr.agente_venta
                            where nr.agente_venta = v_parametros.agente_venta and pv.id_punto_venta = v_parametros.punto_venta::integer)

                          LOOP

                          if (v_datos.moneda = 'USD') then

                          		 insert into datos_amadeus (
                                      id_boleto_amadeus,
                                      pasajero,
                                      localizador,
                                      nro_boleto,
          							  forma_pago_amadeus,
                                      moneda,
                                      precio_total_me,
                                      agente_venta,
                                      id_forma_pago,
                                      monto_forma_pago_me,
                                      monto_forma_pago_ml,
                                      forma_pago,
                                      fecha_emision,
                                      trans_code,
                                      trans_issue_indicator,
                                      nombre,
                                      trans_code_exch,
                                      impreso

                                        )
                                        values(
                                        v_datos.id_boleto_amadeus,
                                        v_datos.pasajero,
                                        v_datos.localizador,
                                        v_datos.nro_boleto,
                                        v_datos.forma_pago_amadeus,
                                        v_datos.moneda,
                                        v_datos.precio_total,
                                        v_datos.codigo_agente,
                                        v_datos.id_forma_pago,
                                        CASE WHEN v_datos.moneda_fp = 'USD' then
                                        v_datos.monto_forma_pago
                                        else
                                        0
                                        end,
                                        CASE WHEN v_datos.moneda_fp != 'USD' then
                                        v_datos.monto_forma_pago
                                        else
                                        0
                                        end,
                                        v_datos.forma_pago,
                                        v_datos.fecha_emision,
                                        v_datos.trans_code,
                                        v_datos.trans_issue_indicator,
                                        v_datos.punto_venta,
                                        v_datos.trans_code_exch,
                                        v_datos.impreso
                                      );
                           	else

                            insert into datos_amadeus (
                                      id_boleto_amadeus,
                                      pasajero,
                                      localizador,
                                      nro_boleto,
          							  forma_pago_amadeus,
                                      moneda,
                                      precio_total_ml,
                                      agente_venta,
                                      id_forma_pago,
                                      monto_forma_pago_me,
                                      monto_forma_pago_ml,
                                      forma_pago,
                                      fecha_emision,
                                      trans_code,
                                      trans_issue_indicator,
                                      nombre,
                                      trans_code_exch,
                                      impreso

                                        )
                                        values(
                                        v_datos.id_boleto_amadeus,
                                        v_datos.pasajero,
                                        v_datos.localizador,
                                        v_datos.nro_boleto,
                                        v_datos.forma_pago_amadeus,
                                        v_datos.moneda,
                                        v_datos.precio_total,
                                        v_datos.codigo_agente,
                                        v_datos.id_forma_pago,
                                        CASE WHEN v_datos.moneda_fp = 'USD' then
                                        v_datos.monto_forma_pago
                                        else
                                        0
                                        end,
                                        CASE WHEN v_datos.moneda_fp != 'USD' then
                                        v_datos.monto_forma_pago
                                        else
                                        0
                                        end,
                                        v_datos.forma_pago,
                                        v_datos.fecha_emision,
                                        v_datos.trans_code,
                                        v_datos.trans_issue_indicator,
                                        v_datos.punto_venta,
                                        v_datos.trans_code_exch,
                                        v_datos.impreso
                                      );


                            end if;

                          END LOOP;

            else

            CREATE TEMPORARY TABLE datos_amadeus ( id_boleto_amadeus  int4,
                                      pasajero varchar,
                                      localizador varchar,
                                      nro_boleto varchar,
          							  forma_pago_amadeus varchar,
                                      moneda varchar,
                                      precio_total_ml numeric,
                                      precio_total_me numeric,
                                      agente_venta varchar,
                                      id_forma_pago int4,
                                      monto_forma_pago_ml numeric,
                                      monto_forma_pago_me numeric,
                                      forma_pago varchar,
                                      fecha_emision date,
                                      trans_code varchar,
                                      trans_issue_indicator varchar,
                                      nombre varchar,
                                      trans_code_exch varchar,
                                      impreso varchar )ON COMMIT DROP;




            For v_datos in (with forma_pago_temporal as(
                                          select bol.id_boleto_amadeus,
                                                 array_agg(fp.id_forma_pago) as id_forma_pago,
                                                 array_agg(mon.codigo_internacional) as moneda_fp,
                                                 array_agg(fp.nombre || ' - ' || mon.codigo_internacional) as forma_pago,
                                                 array_agg(fp.codigo) as codigo_forma_pago,
                                                 array_agg(bfp.numero_tarjeta) as numero_tarjeta,
                                                 array_agg(bfp.mco) as mco,
                                                 array_agg(bfp.codigo_tarjeta) as codigo_tarjeta,
                                                 array_agg(bfp.id_auxiliar) as id_auxiliar,
                                                 array_agg(aux.nombre_auxiliar) as nombre_auxiliar,
                                                 array_agg(bfp.importe) as monto_forma_pago
                                          from obingresos.tboleto_amadeus bol
                                               left join obingresos.tboleto_amadeus_forma_pago bfp on bfp.id_boleto_amadeus=bol.id_boleto_amadeus
                                               left join obingresos.tforma_pago fp on fp.id_forma_pago = bfp.id_forma_pago
                                               left join param.tmoneda mon on mon.id_moneda = fp.id_moneda
                                               left join conta.tauxiliar aux on aux.id_auxiliar=bfp.id_auxiliar
                                          where bol.fecha_emision = v_parametros.fecha::date
                                          group by bol.id_boleto_amadeus)

                          select nr.id_boleto_amadeus,
                          		 nr.pasajero as pasajero,
                          		 nr.localizador as localizador,
                                 substring(nr.nro_boleto from 4)::varchar as nro_boleto,
                                 nr.forma_pago as forma_pago_amadeus,
                                 nr.moneda,
                                 nr.total as precio_total,
                                 nr.agente_venta as codigo_agente,
                                 fpo.id_forma_pago [ 1 ]::integer as id_forma_pago,
                                 fpo.monto_forma_pago [ 1 ]::numeric as monto_forma_pago,
                                 fpo.forma_pago [ 1 ]::varchar as forma_pago,
                                 nr.fecha_emision,
                                 nr.trans_code,
                         		 nr.trans_issue_indicator,
                         		 pv.nombre as punto_venta,
                                 nr.trans_code_exch,
                                 nr.impreso,
                                 fpo.moneda_fp [ 1 ]::varchar as moneda_fp

                          from obingresos.tboleto_amadeus nr
                          inner join vef.tpunto_venta pv on pv.id_punto_venta=nr.id_punto_venta
                          inner join vef.tsucursal_moneda suc on suc.id_sucursal=pv.id_sucursal and suc.tipo_moneda='moneda_base'
                          inner join param.tmoneda mon on mon.id_moneda=suc.id_moneda
                          inner join forma_pago_temporal fpo on fpo.id_boleto_amadeus=nr.id_boleto_amadeus
                          left join segu.tusuario_externo usuex on usuex.usuario_externo=nr.agente_venta
                          where usuex.id_usuario = p_id_usuario)

                          LOOP

                          if (v_datos.moneda = 'USD') then

                          		 insert into datos_amadeus (
                                      id_boleto_amadeus,
                                      pasajero,
                                      localizador,
                                      nro_boleto,
          							  forma_pago_amadeus,
                                      moneda,
                                      precio_total_me,
                                      agente_venta,
                                      id_forma_pago,
                                      monto_forma_pago_me,
                                      monto_forma_pago_ml,
                                      forma_pago,
                                      fecha_emision,
                                      trans_code,
                                      trans_issue_indicator,
                                      nombre,
                                      trans_code_exch,
                                      impreso

                                        )
                                        values(
                                        v_datos.id_boleto_amadeus,
                                        v_datos.pasajero,
                                        v_datos.localizador,
                                        v_datos.nro_boleto,
                                        v_datos.forma_pago_amadeus,
                                        v_datos.moneda,
                                        v_datos.precio_total,
                                        v_datos.codigo_agente,
                                        v_datos.id_forma_pago,
                                        CASE WHEN v_datos.moneda_fp = 'USD' then
                                        v_datos.monto_forma_pago
                                        else
                                        0
                                        end,
                                        CASE WHEN v_datos.moneda_fp != 'USD' then
                                        v_datos.monto_forma_pago
                                        else
                                        0
                                        end,
                                        v_datos.forma_pago,
                                        v_datos.fecha_emision,
                                        v_datos.trans_code,
                                        v_datos.trans_issue_indicator,
                                        v_datos.punto_venta,
                                        v_datos.trans_code_exch,
                                        v_datos.impreso
                                      );
                           	else

                            insert into datos_amadeus (
                                      id_boleto_amadeus,
                                      pasajero,
                                      localizador,
                                      nro_boleto,
          							  forma_pago_amadeus,
                                      moneda,
                                      precio_total_ml,
                                      agente_venta,
                                      id_forma_pago,
                                      monto_forma_pago_me,
                                      monto_forma_pago_ml,
                                      forma_pago,
                                      fecha_emision,
                                      trans_code,
                                      trans_issue_indicator,
                                      nombre,
                                      trans_code_exch,
                                      impreso

                                        )
                                        values(
                                        v_datos.id_boleto_amadeus,
                                        v_datos.pasajero,
                                        v_datos.localizador,
                                        v_datos.nro_boleto,
                                        v_datos.forma_pago_amadeus,
                                        v_datos.moneda,
                                        v_datos.precio_total,
                                        v_datos.codigo_agente,
                                        v_datos.id_forma_pago,
                                        CASE WHEN v_datos.moneda_fp = 'USD' then
                                        v_datos.monto_forma_pago
                                        else
                                        0
                                        end,
                                        CASE WHEN v_datos.moneda_fp != 'USD' then
                                        v_datos.monto_forma_pago
                                        else
                                        0
                                        end,
                                        v_datos.forma_pago,
                                        v_datos.fecha_emision,
                                        v_datos.trans_code,
                                        v_datos.trans_issue_indicator,
                                        v_datos.punto_venta,
                                        v_datos.trans_code_exch,
                                        v_datos.impreso
                                      );


                            end if;

                          END LOOP;
            end if;

			v_consulta:='select id_boleto_amadeus,
                                      pasajero,
                                      localizador,
                                      nro_boleto,
          							  forma_pago_amadeus,
                                      moneda,
                                      COALESCE (precio_total_ml,0) as precio_total_ml,
                                      COALESCE (precio_total_me,0) as precio_total_me,
                                      agente_venta,
                                      id_forma_pago,
                                      monto_forma_pago_ml,
                                      monto_forma_pago_me,
                                      forma_pago,
                                      fecha_emision,
                                      trans_code,
                                      trans_issue_indicator,
                                      nombre,
                                      trans_code_exch,
                                      impreso

                          from datos_amadeus
                          where ';

            --Definicion de la respuesta
			v_consulta = v_consulta||v_parametros.filtro;
        	v_consulta = v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

            raise notice 'v_consulta: %',v_consulta;
			--Devuelve la respuesta
			return v_consulta;

		end;

	/*********************************
 	#TRANSACCION:  'OBING_SAL_BOL_CONT'
 	#DESCRIPCION:	Conteo de boletos vendidos
 	#AUTOR:		franklin.espinoza
 	#FECHA:		09-04-2019 20:28:10
	***********************************/

	elsif(p_transaccion='OBING_SAL_BOL_CONT')then

		begin
			--Sentencia de la consulta de conteo de registros

             if (v_parametros.tipo_interfaz = 'ResumenDetalle') then

            CREATE TEMPORARY TABLE datos_amadeus ( id_boleto_amadeus  int4,
                                      pasajero varchar,
                                      localizador varchar,
                                      nro_boleto varchar,
          							  forma_pago_amadeus varchar,
                                      moneda varchar,
                                      precio_total_ml numeric,
                                      precio_total_me numeric,
                                      agente_venta varchar,
                                      id_forma_pago int4,
                                      monto_forma_pago_ml numeric,
                                      monto_forma_pago_me numeric,
                                      forma_pago varchar,
                                      fecha_emision date,
                                      trans_code varchar,
                                      trans_issue_indicator varchar,
                                      nombre varchar,
                                      trans_code_exch varchar,
                                      impreso varchar )ON COMMIT DROP;


            /*select ex.id_usuario into v_id_usuario
            from segu.tusuario_externo ex
            where ex.usuario_externo = v_parametros.agente_venta;*/


            For v_datos in (with forma_pago_temporal as(
                                          select bol.id_boleto_amadeus,
                                                 array_agg(fp.id_forma_pago) as id_forma_pago,
                                                 array_agg(mon.codigo_internacional) as moneda_fp,
                                                 array_agg(fp.nombre || ' - ' || mon.codigo_internacional) as forma_pago,
                                                 array_agg(fp.codigo) as codigo_forma_pago,
                                                 array_agg(bfp.numero_tarjeta) as numero_tarjeta,
                                                 array_agg(bfp.mco) as mco,
                                                 array_agg(bfp.codigo_tarjeta) as codigo_tarjeta,
                                                 array_agg(bfp.id_auxiliar) as id_auxiliar,
                                                 array_agg(aux.nombre_auxiliar) as nombre_auxiliar,
                                                 array_agg(bfp.importe) as monto_forma_pago
                                          from obingresos.tboleto_amadeus bol
                                               left join obingresos.tboleto_amadeus_forma_pago bfp on bfp.id_boleto_amadeus=bol.id_boleto_amadeus
                                               left join obingresos.tforma_pago fp on fp.id_forma_pago = bfp.id_forma_pago
                                               left join param.tmoneda mon on mon.id_moneda = fp.id_moneda
                                               left join conta.tauxiliar aux on aux.id_auxiliar=bfp.id_auxiliar
                                          where bol.fecha_emision between v_parametros.fecha_ini::date and v_parametros.fecha_fin::date
                                          group by bol.id_boleto_amadeus)

                          select nr.id_boleto_amadeus,
                          		 nr.pasajero as pasajero,
                          		 nr.localizador as localizador,
                                 substring(nr.nro_boleto from 4)::varchar as nro_boleto,
                                 nr.forma_pago as forma_pago_amadeus,
                                 nr.moneda,
                                 nr.total as precio_total,
                                 nr.agente_venta as codigo_agente,
                                 fpo.id_forma_pago [ 1 ]::integer as id_forma_pago,
                                 fpo.monto_forma_pago [ 1 ]::numeric as monto_forma_pago,
                                 fpo.forma_pago [ 1 ]::varchar as forma_pago,
                                 nr.fecha_emision,
                                 nr.trans_code,
                         		 nr.trans_issue_indicator,
                         		 pv.nombre as punto_venta,
                                 nr.trans_code_exch,
                                 nr.impreso,
                                 fpo.moneda_fp [ 1 ]::varchar as moneda_fp

                          from obingresos.tboleto_amadeus nr
                          inner join vef.tpunto_venta pv on pv.id_punto_venta=nr.id_punto_venta
                          inner join vef.tsucursal_moneda suc on suc.id_sucursal=pv.id_sucursal and suc.tipo_moneda='moneda_base'
                          inner join param.tmoneda mon on mon.id_moneda=suc.id_moneda
                          inner join forma_pago_temporal fpo on fpo.id_boleto_amadeus=nr.id_boleto_amadeus
                          left join segu.tusuario_externo usuex on usuex.usuario_externo=nr.agente_venta
                          where nr.agente_venta = v_parametros.agente_venta and pv.id_punto_venta = v_parametros.punto_venta::integer)

                          LOOP

                          if (v_datos.moneda = 'USD') then

                          		 insert into datos_amadeus (
                                      id_boleto_amadeus,
                                      pasajero,
                                      localizador,
                                      nro_boleto,
          							  forma_pago_amadeus,
                                      moneda,
                                      precio_total_me,
                                      agente_venta,
                                      id_forma_pago,
                                      monto_forma_pago_me,
                                      monto_forma_pago_ml,
                                      forma_pago,
                                      fecha_emision,
                                      trans_code,
                                      trans_issue_indicator,
                                      nombre,
                                      trans_code_exch,
                                      impreso

                                        )
                                        values(
                                        v_datos.id_boleto_amadeus,
                                        v_datos.pasajero,
                                        v_datos.localizador,
                                        v_datos.nro_boleto,
                                        v_datos.forma_pago_amadeus,
                                        v_datos.moneda,
                                        v_datos.precio_total,
                                        v_datos.codigo_agente,
                                        v_datos.id_forma_pago,
                                        CASE WHEN v_datos.moneda_fp = 'USD' then
                                        v_datos.monto_forma_pago
                                        else
                                        0
                                        end,
                                        CASE WHEN v_datos.moneda_fp != 'USD' then
                                        v_datos.monto_forma_pago
                                        else
                                        0
                                        end,
                                        v_datos.forma_pago,
                                        v_datos.fecha_emision,
                                        v_datos.trans_code,
                                        v_datos.trans_issue_indicator,
                                        v_datos.punto_venta,
                                        v_datos.trans_code_exch,
                                        v_datos.impreso
                                      );
                           	else

                            insert into datos_amadeus (
                                      id_boleto_amadeus,
                                      pasajero,
                                      localizador,
                                      nro_boleto,
          							  forma_pago_amadeus,
                                      moneda,
                                      precio_total_ml,
                                      agente_venta,
                                      id_forma_pago,
                                      monto_forma_pago_me,
                                      monto_forma_pago_ml,
                                      forma_pago,
                                      fecha_emision,
                                      trans_code,
                                      trans_issue_indicator,
                                      nombre,
                                      trans_code_exch,
                                      impreso

                                        )
                                        values(
                                        v_datos.id_boleto_amadeus,
                                        v_datos.pasajero,
                                        v_datos.localizador,
                                        v_datos.nro_boleto,
                                        v_datos.forma_pago_amadeus,
                                        v_datos.moneda,
                                        v_datos.precio_total,
                                        v_datos.codigo_agente,
                                        v_datos.id_forma_pago,
                                        CASE WHEN v_datos.moneda_fp = 'USD' then
                                        v_datos.monto_forma_pago
                                        else
                                        0
                                        end,
                                        CASE WHEN v_datos.moneda_fp != 'USD' then
                                        v_datos.monto_forma_pago
                                        else
                                        0
                                        end,
                                        v_datos.forma_pago,
                                        v_datos.fecha_emision,
                                        v_datos.trans_code,
                                        v_datos.trans_issue_indicator,
                                        v_datos.punto_venta,
                                        v_datos.trans_code_exch,
                                        v_datos.impreso
                                      );


                            end if;

                          END LOOP;

            else

            CREATE TEMPORARY TABLE datos_amadeus ( id_boleto_amadeus  int4,
                                      pasajero varchar,
                                      localizador varchar,
                                      nro_boleto varchar,
          							  forma_pago_amadeus varchar,
                                      moneda varchar,
                                      precio_total_ml numeric,
                                      precio_total_me numeric,
                                      agente_venta varchar,
                                      id_forma_pago int4,
                                      monto_forma_pago_ml numeric,
                                      monto_forma_pago_me numeric,
                                      forma_pago varchar,
                                      fecha_emision date,
                                      trans_code varchar,
                                      trans_issue_indicator varchar,
                                      nombre varchar,
                                      trans_code_exch varchar,
                                      impreso varchar )ON COMMIT DROP;




            For v_datos in (with forma_pago_temporal as(
                                          select bol.id_boleto_amadeus,
                                                 array_agg(fp.id_forma_pago) as id_forma_pago,
                                                 array_agg(mon.codigo_internacional) as moneda_fp,
                                                 array_agg(fp.nombre || ' - ' || mon.codigo_internacional) as forma_pago,
                                                 array_agg(fp.codigo) as codigo_forma_pago,
                                                 array_agg(bfp.numero_tarjeta) as numero_tarjeta,
                                                 array_agg(bfp.mco) as mco,
                                                 array_agg(bfp.codigo_tarjeta) as codigo_tarjeta,
                                                 array_agg(bfp.id_auxiliar) as id_auxiliar,
                                                 array_agg(aux.nombre_auxiliar) as nombre_auxiliar,
                                                 array_agg(bfp.importe) as monto_forma_pago
                                          from obingresos.tboleto_amadeus bol
                                               left join obingresos.tboleto_amadeus_forma_pago bfp on bfp.id_boleto_amadeus=bol.id_boleto_amadeus
                                               left join obingresos.tforma_pago fp on fp.id_forma_pago = bfp.id_forma_pago
                                               left join param.tmoneda mon on mon.id_moneda = fp.id_moneda
                                               left join conta.tauxiliar aux on aux.id_auxiliar=bfp.id_auxiliar
                                          where bol.fecha_emision = v_parametros.fecha::date
                                          group by bol.id_boleto_amadeus)

                          select nr.id_boleto_amadeus,
                          		 nr.pasajero as pasajero,
                          		 nr.localizador as localizador,
                                 substring(nr.nro_boleto from 4)::varchar as nro_boleto,
                                 nr.forma_pago as forma_pago_amadeus,
                                 nr.moneda,
                                 nr.total as precio_total,
                                 nr.agente_venta as codigo_agente,
                                 fpo.id_forma_pago [ 1 ]::integer as id_forma_pago,
                                 fpo.monto_forma_pago [ 1 ]::numeric as monto_forma_pago,
                                 fpo.forma_pago [ 1 ]::varchar as forma_pago,
                                 nr.fecha_emision,
                                 nr.trans_code,
                         		 nr.trans_issue_indicator,
                         		 pv.nombre as punto_venta,
                                 nr.trans_code_exch,
                                 nr.impreso,
                                 fpo.moneda_fp [ 1 ]::varchar as moneda_fp

                          from obingresos.tboleto_amadeus nr
                          inner join vef.tpunto_venta pv on pv.id_punto_venta=nr.id_punto_venta
                          inner join vef.tsucursal_moneda suc on suc.id_sucursal=pv.id_sucursal and suc.tipo_moneda='moneda_base'
                          inner join param.tmoneda mon on mon.id_moneda=suc.id_moneda
                          inner join forma_pago_temporal fpo on fpo.id_boleto_amadeus=nr.id_boleto_amadeus
                          left join segu.tusuario_externo usuex on usuex.usuario_externo=nr.agente_venta
                          where usuex.id_usuario = p_id_usuario)

                          LOOP

                          if (v_datos.moneda = 'USD') then

                          		 insert into datos_amadeus (
                                      id_boleto_amadeus,
                                      pasajero,
                                      localizador,
                                      nro_boleto,
          							  forma_pago_amadeus,
                                      moneda,
                                      precio_total_me,
                                      agente_venta,
                                      id_forma_pago,
                                      monto_forma_pago_me,
                                      monto_forma_pago_ml,
                                      forma_pago,
                                      fecha_emision,
                                      trans_code,
                                      trans_issue_indicator,
                                      nombre,
                                      trans_code_exch,
                                      impreso

                                        )
                                        values(
                                        v_datos.id_boleto_amadeus,
                                        v_datos.pasajero,
                                        v_datos.localizador,
                                        v_datos.nro_boleto,
                                        v_datos.forma_pago_amadeus,
                                        v_datos.moneda,
                                        v_datos.precio_total,
                                        v_datos.codigo_agente,
                                        v_datos.id_forma_pago,
                                        CASE WHEN v_datos.moneda_fp = 'USD' then
                                        v_datos.monto_forma_pago
                                        else
                                        0
                                        end,
                                        CASE WHEN v_datos.moneda_fp != 'USD' then
                                        v_datos.monto_forma_pago
                                        else
                                        0
                                        end,
                                        v_datos.forma_pago,
                                        v_datos.fecha_emision,
                                        v_datos.trans_code,
                                        v_datos.trans_issue_indicator,
                                        v_datos.punto_venta,
                                        v_datos.trans_code_exch,
                                        v_datos.impreso
                                      );
                           	else

                            insert into datos_amadeus (
                                      id_boleto_amadeus,
                                      pasajero,
                                      localizador,
                                      nro_boleto,
          							  forma_pago_amadeus,
                                      moneda,
                                      precio_total_ml,
                                      agente_venta,
                                      id_forma_pago,
                                      monto_forma_pago_me,
                                      monto_forma_pago_ml,
                                      forma_pago,
                                      fecha_emision,
                                      trans_code,
                                      trans_issue_indicator,
                                      nombre,
                                      trans_code_exch,
                                      impreso

                                        )
                                        values(
                                        v_datos.id_boleto_amadeus,
                                        v_datos.pasajero,
                                        v_datos.localizador,
                                        v_datos.nro_boleto,
                                        v_datos.forma_pago_amadeus,
                                        v_datos.moneda,
                                        v_datos.precio_total,
                                        v_datos.codigo_agente,
                                        v_datos.id_forma_pago,
                                        CASE WHEN v_datos.moneda_fp = 'USD' then
                                        v_datos.monto_forma_pago
                                        else
                                        0
                                        end,
                                        CASE WHEN v_datos.moneda_fp != 'USD' then
                                        v_datos.monto_forma_pago
                                        else
                                        0
                                        end,
                                        v_datos.forma_pago,
                                        v_datos.fecha_emision,
                                        v_datos.trans_code,
                                        v_datos.trans_issue_indicator,
                                        v_datos.punto_venta,
                                        v_datos.trans_code_exch,
                                        v_datos.impreso
                                      );


                            end if;

                          END LOOP;
            end if;

			v_consulta:='select count(id_boleto_amadeus) as total,
                        		COALESCE (sum(precio_total_ml),0) as precio_total_ml,
                                COALESCE (sum(precio_total_me),0) as precio_total_me,
                                COALESCE (sum(monto_forma_pago_ml),0) as monto_forma_pago_ml,
                                COALESCE (sum(monto_forma_pago_me),0) as monto_forma_pago_me
                          from datos_amadeus
                          where ';

			v_consulta:=v_consulta||v_parametros.filtro;
			--Devuelve la respuesta
			return v_consulta;

		end;

        /*********************************
 	#TRANSACCION:  'OBING_RESUVEN_SEL'
 	#DESCRIPCION:	Consulta de Boletos Vendidos
 	#AUTOR:		Ismael Valdivia
 	#FECHA:		26-02-2022 18:00:00
	***********************************/

	elsif(p_transaccion='OBING_RESUVEN_SEL')then

       begin
    		--Sentencia de la consulta

            CREATE TEMPORARY TABLE datos_resumen ( agente_venta varchar,
            									   counter varchar,
                                                   monto_ml numeric,
                                                   monto_me numeric
             				          )ON COMMIT DROP;




            For v_datos in (with forma_pago_temporal as(
                                          select bol.id_boleto_amadeus,
                                                 array_agg(fp.id_forma_pago) as id_forma_pago,
                                                 array_agg(mon.codigo_internacional) as moneda_fp,
                                                 array_agg(fp.nombre || ' - ' || mon.codigo_internacional) as forma_pago,
                                                 array_agg(fp.codigo) as codigo_forma_pago,
                                                 array_agg(bfp.numero_tarjeta) as numero_tarjeta,
                                                 array_agg(bfp.mco) as mco,
                                                 array_agg(bfp.codigo_tarjeta) as codigo_tarjeta,
                                                 array_agg(bfp.id_auxiliar) as id_auxiliar,
                                                 array_agg(aux.nombre_auxiliar) as nombre_auxiliar,
                                                 array_agg(bfp.importe) as monto_forma_pago
                                          from obingresos.tboleto_amadeus bol
                                               left join obingresos.tboleto_amadeus_forma_pago bfp on bfp.id_boleto_amadeus=bol.id_boleto_amadeus
                                               left join obingresos.tforma_pago fp on fp.id_forma_pago = bfp.id_forma_pago
                                               left join param.tmoneda mon on mon.id_moneda = fp.id_moneda
                                               left join conta.tauxiliar aux on aux.id_auxiliar=bfp.id_auxiliar
                                          where bol.fecha_emision between v_parametros.fecha_ini::date and v_parametros.fecha_fin::date
                                          group by bol.id_boleto_amadeus)

                          select
                                 nr.agente_venta as codigo_agente,
                                 per.nombre_completo2,
                                 nr.moneda,
                                 sum (nr.total) as precio_total

                          from obingresos.tboleto_amadeus nr
                          inner join vef.tpunto_venta pv on pv.id_punto_venta=nr.id_punto_venta
                          inner join vef.tsucursal_moneda suc on suc.id_sucursal=pv.id_sucursal and suc.tipo_moneda='moneda_base'
                          inner join param.tmoneda mon on mon.id_moneda=suc.id_moneda
                          inner join forma_pago_temporal fpo on fpo.id_boleto_amadeus=nr.id_boleto_amadeus
                          left join segu.tusuario_externo usuex on usuex.usuario_externo=nr.agente_venta
                          left join segu.tusuario usu on usu.id_usuario = usuex.id_usuario
                          left join segu.vpersona per on per.id_persona = usu.id_persona
                          where pv.id_punto_venta = v_parametros.punto_venta::integer
                          group by nr.agente_venta, per.nombre_completo2, nr.moneda)

                          LOOP

                          		select count(*) into v_contador
                                    from datos_resumen
                                    where agente_venta = v_datos.codigo_agente;

                                 if (v_contador = 0) then

                                 	if (v_datos.moneda = 'USD') then

                                       insert into datos_resumen (
                                              agente_venta,
                                              counter,
                                              monto_ml,
                                              monto_me
                                              )
                                              values(
                                              v_datos.codigo_agente,
                                              v_datos.nombre_completo2,
                                              0,
                                              v_datos.precio_total
                                            );
                                    else
                                    	insert into datos_resumen (
                                              agente_venta,
                                              counter,
                                              monto_ml,
                                              monto_me
                                              )
                                              values(
                                              v_datos.codigo_agente,
                                              v_datos.nombre_completo2,
                                              v_datos.precio_total,
                                              0
                                            );
                                    end if;

                                else

                                    	if (v_datos.moneda = 'USD')  then
                                            update datos_resumen set
                                            monto_me = v_datos.precio_total
                                            where agente_venta = v_datos.codigo_agente;

                                        else
                                            update datos_resumen set
                                            monto_ml = v_datos.precio_total
                                            where agente_venta = v_datos.codigo_agente;
                                        end if;

                            	end if;



                          END LOOP;

			v_consulta:='select agente_venta,
            					counter,
                                monto_ml,
                                monto_me
                          from datos_resumen
                          where ';

            --Definicion de la respuesta
			v_consulta = v_consulta||v_parametros.filtro;
        	v_consulta = v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

            --raise notice 'v_consulta: %',v_consulta;
			--Devuelve la respuesta
			return v_consulta;

		end;

	/*********************************
 	#TRANSACCION:  'OBING_RESUVEN_CONT'
 	#DESCRIPCION:	Conteo de boletos vendidos
 	#AUTOR:		franklin.espinoza
 	#FECHA:		09-04-2019 20:28:10
	***********************************/

	elsif(p_transaccion='OBING_RESUVEN_CONT')then

		begin
			--Sentencia de la consulta de conteo de registros

             CREATE TEMPORARY TABLE datos_resumen ( agente_venta varchar,
            									   counter varchar,
                                                   monto_ml numeric,
                                                   monto_me numeric
             				          )ON COMMIT DROP;




            For v_datos in (with forma_pago_temporal as(
                                          select bol.id_boleto_amadeus,
                                                 array_agg(fp.id_forma_pago) as id_forma_pago,
                                                 array_agg(mon.codigo_internacional) as moneda_fp,
                                                 array_agg(fp.nombre || ' - ' || mon.codigo_internacional) as forma_pago,
                                                 array_agg(fp.codigo) as codigo_forma_pago,
                                                 array_agg(bfp.numero_tarjeta) as numero_tarjeta,
                                                 array_agg(bfp.mco) as mco,
                                                 array_agg(bfp.codigo_tarjeta) as codigo_tarjeta,
                                                 array_agg(bfp.id_auxiliar) as id_auxiliar,
                                                 array_agg(aux.nombre_auxiliar) as nombre_auxiliar,
                                                 array_agg(bfp.importe) as monto_forma_pago
                                          from obingresos.tboleto_amadeus bol
                                               left join obingresos.tboleto_amadeus_forma_pago bfp on bfp.id_boleto_amadeus=bol.id_boleto_amadeus
                                               left join obingresos.tforma_pago fp on fp.id_forma_pago = bfp.id_forma_pago
                                               left join param.tmoneda mon on mon.id_moneda = fp.id_moneda
                                               left join conta.tauxiliar aux on aux.id_auxiliar=bfp.id_auxiliar
                                          where bol.fecha_emision between v_parametros.fecha_ini::date and v_parametros.fecha_fin::date
                                          group by bol.id_boleto_amadeus)

                          select
                                 nr.agente_venta as codigo_agente,
                                 per.nombre_completo2,
                                 nr.moneda,
                                 sum (nr.total) as precio_total

                          from obingresos.tboleto_amadeus nr
                          inner join vef.tpunto_venta pv on pv.id_punto_venta=nr.id_punto_venta
                          inner join vef.tsucursal_moneda suc on suc.id_sucursal=pv.id_sucursal and suc.tipo_moneda='moneda_base'
                          inner join param.tmoneda mon on mon.id_moneda=suc.id_moneda
                          inner join forma_pago_temporal fpo on fpo.id_boleto_amadeus=nr.id_boleto_amadeus
                          left join segu.tusuario_externo usuex on usuex.usuario_externo=nr.agente_venta
                          left join segu.tusuario usu on usu.id_usuario = usuex.id_usuario
                          left join segu.vpersona per on per.id_persona = usu.id_persona
                          where pv.id_punto_venta = v_parametros.punto_venta::integer
                          group by nr.agente_venta, per.nombre_completo2, nr.moneda)

                          LOOP

                          		select count(*) into v_contador
                                    from datos_resumen
                                    where agente_venta = v_datos.codigo_agente;

                                 if (v_contador = 0) then

                                 	if (v_datos.moneda = 'USD') then

                                       insert into datos_resumen (
                                              agente_venta,
                                              counter,
                                              monto_ml,
                                              monto_me
                                              )
                                              values(
                                              v_datos.codigo_agente,
                                              v_datos.nombre_completo2,
                                              0,
                                              v_datos.precio_total
                                            );
                                    else
                                    	insert into datos_resumen (
                                              agente_venta,
                                              counter,
                                              monto_ml,
                                              monto_me
                                              )
                                              values(
                                              v_datos.codigo_agente,
                                              v_datos.nombre_completo2,
                                              v_datos.precio_total,
                                              0
                                            );
                                    end if;

                                else

                                    	if (v_datos.moneda = 'USD')  then
                                            update datos_resumen set
                                            monto_me = v_datos.precio_total
                                            where agente_venta = v_datos.codigo_agente;

                                        else
                                            update datos_resumen set
                                            monto_ml = v_datos.precio_total
                                            where agente_venta = v_datos.codigo_agente;
                                        end if;

                            	end if;



                          END LOOP;

			v_consulta:='select count(agente_venta),
                                sum(monto_ml),
                                sum(monto_me)
                          from datos_resumen
                          where ';

			v_consulta:=v_consulta||v_parametros.filtro;
			--Devuelve la respuesta
			return v_consulta;

		end;



        /*********************************
 	#TRANSACCION:  'OBING_RESUVENREP_SEL'
 	#DESCRIPCION:	Consulta de Boletos Vendidos
 	#AUTOR:		Ismael Valdivia
 	#FECHA:		26-02-2022 18:00:00
	***********************************/

	elsif(p_transaccion='OBING_RESUVENREP_SEL')then

       begin
    		--Sentencia de la consulta

            /*Cambiando la consulta para separar moneda local y moneda exranjera (Ismael Valdivia 26/02/2020)*/


            CREATE TEMPORARY TABLE datos_resumen ( agente_venta varchar,
            									   counter varchar,
                                                   monto_ml numeric,
                                                   monto_me numeric
             				          )ON COMMIT DROP;




            For v_datos in (with forma_pago_temporal as(
                                          select bol.id_boleto_amadeus,
                                                 array_agg(fp.id_forma_pago) as id_forma_pago,
                                                 array_agg(mon.codigo_internacional) as moneda_fp,
                                                 array_agg(fp.nombre || ' - ' || mon.codigo_internacional) as forma_pago,
                                                 array_agg(fp.codigo) as codigo_forma_pago,
                                                 array_agg(bfp.numero_tarjeta) as numero_tarjeta,
                                                 array_agg(bfp.mco) as mco,
                                                 array_agg(bfp.codigo_tarjeta) as codigo_tarjeta,
                                                 array_agg(bfp.id_auxiliar) as id_auxiliar,
                                                 array_agg(aux.nombre_auxiliar) as nombre_auxiliar,
                                                 array_agg(bfp.importe) as monto_forma_pago
                                          from obingresos.tboleto_amadeus bol
                                               left join obingresos.tboleto_amadeus_forma_pago bfp on bfp.id_boleto_amadeus=bol.id_boleto_amadeus
                                               left join obingresos.tforma_pago fp on fp.id_forma_pago = bfp.id_forma_pago
                                               left join param.tmoneda mon on mon.id_moneda = fp.id_moneda
                                               left join conta.tauxiliar aux on aux.id_auxiliar=bfp.id_auxiliar
                                          where bol.fecha_emision between v_parametros.fecha_ini::date and v_parametros.fecha_fin::date
                                          group by bol.id_boleto_amadeus)

                          select
                                 nr.agente_venta as codigo_agente,
                                 per.nombre_completo2,
                                 nr.moneda,
                                 sum (nr.total) as precio_total

                          from obingresos.tboleto_amadeus nr
                          inner join vef.tpunto_venta pv on pv.id_punto_venta=nr.id_punto_venta
                          inner join vef.tsucursal_moneda suc on suc.id_sucursal=pv.id_sucursal and suc.tipo_moneda='moneda_base'
                          inner join param.tmoneda mon on mon.id_moneda=suc.id_moneda
                          inner join forma_pago_temporal fpo on fpo.id_boleto_amadeus=nr.id_boleto_amadeus
                          left join segu.tusuario_externo usuex on usuex.usuario_externo=nr.agente_venta
                          left join segu.tusuario usu on usu.id_usuario = usuex.id_usuario
                          left join segu.vpersona per on per.id_persona = usu.id_persona
                          where pv.id_punto_venta = v_parametros.punto_venta::integer
                          group by nr.agente_venta, per.nombre_completo2, nr.moneda)

                          LOOP

                          		select count(*) into v_contador
                                    from datos_resumen
                                    where agente_venta = v_datos.codigo_agente;

                                 if (v_contador = 0) then

                                 	if (v_datos.moneda = 'USD') then

                                       insert into datos_resumen (
                                              agente_venta,
                                              counter,
                                              monto_ml,
                                              monto_me
                                              )
                                              values(
                                              v_datos.codigo_agente,
                                              v_datos.nombre_completo2,
                                              0,
                                              v_datos.precio_total
                                            );
                                    else
                                    	insert into datos_resumen (
                                              agente_venta,
                                              counter,
                                              monto_ml,
                                              monto_me
                                              )
                                              values(
                                              v_datos.codigo_agente,
                                              v_datos.nombre_completo2,
                                              v_datos.precio_total,
                                              0
                                            );
                                    end if;

                                else

                                    	if (v_datos.moneda = 'USD')  then
                                            update datos_resumen set
                                            monto_me = v_datos.precio_total
                                            where agente_venta = v_datos.codigo_agente;

                                        else
                                            update datos_resumen set
                                            monto_ml = v_datos.precio_total
                                            where agente_venta = v_datos.codigo_agente;
                                        end if;

                            	end if;



                          END LOOP;

			v_consulta:='select agente_venta,
            					counter,
                                monto_ml,
                                monto_me
                          from datos_resumen
                          order by counter ASC
                          ';

			return v_consulta;

        end;

     /*********************************
     #TRANSACCION:  'OBING_GET_PV_SEL'
     #DESCRIPCION:	Consulta de datos
     #AUTOR:		Ismael Valdivia
     #FECHA:		03-03-2020 08:30:00
    ***********************************/

    elsif(p_transaccion='OBING_GET_PV_SEL')then

      begin
      			select ex.usuario_externo into v_agente_venta
                from segu.tusuario_externo ex
                where ex.id_usuario = p_id_usuario;

                select string_to_array (list(distinct (ama.id_punto_venta)::varchar),',')::integer[] into v_puntos_venta
                from obingresos.tboleto_amadeus ama
                where ama.agente_venta = v_agente_venta;


                CREATE TEMPORARY TABLE puntos_venta_counter (  id_punto_venta int4,
                                                               estado_reg varchar,
                                                               id_sucursal int4,
                                                               nombre varchar,
                                                               descripcion text,
                                                               codigo varchar,
                                                               habilitar_comisiones varchar,
                                                               formato_comprobante varchar,
                                                               tipo varchar
                                                  )ON COMMIT DROP;


                for v_puntos_venta_counter in ( select
                                              puve.id_punto_venta,
                                              puve.estado_reg,
                                              puve.id_sucursal,
                                              puve.nombre,
                                              puve.descripcion,
                                              puve.codigo,
                                              puve.habilitar_comisiones,
                                              suc.formato_comprobante,
                                              puve.tipo
                                              from vef.tpunto_venta puve
                                              inner join segu.tusuario usu1 on usu1.id_usuario = puve.id_usuario_reg
                                              left join segu.tusuario usu2 on usu2.id_usuario = puve.id_usuario_mod
                                              inner join vef.tsucursal suc on suc.id_sucursal = puve.id_sucursal
                                              where puve.id_punto_venta = any (v_puntos_venta) )
                LOOP

                                insert into puntos_venta_counter (
                                       id_punto_venta,
                                       estado_reg,
                                       id_sucursal,
                                       nombre,
                                       descripcion,
                                       codigo,
                                       habilitar_comisiones,
                                       formato_comprobante,
                                       tipo
                                )VALUES(
                                	   v_puntos_venta_counter.id_punto_venta,
                                       v_puntos_venta_counter.estado_reg,
                                       v_puntos_venta_counter.id_sucursal,
                                       v_puntos_venta_counter.nombre,
                                       v_puntos_venta_counter.descripcion,
                                       v_puntos_venta_counter.codigo,
                                       v_puntos_venta_counter.habilitar_comisiones,
                                       v_puntos_venta_counter.formato_comprobante,
                                       v_puntos_venta_counter.tipo
                                );

                end loop;





        --Sentencia de la consulta
        v_consulta:='select
						 id_punto_venta,
                         estado_reg,
                         id_sucursal,
                         nombre,
                         descripcion,
                         codigo,
                         habilitar_comisiones,
                         formato_comprobante,
                         tipo
                         from puntos_venta_counter';

        --Devuelve la respuesta
        return v_consulta;

      end;

      /*********************************
       #TRANSACCION:  'OBING_GET_PV_CONT'
       #DESCRIPCION:	Conteo de registros
       #AUTOR:		Ismael Valdivia
     #FECHA:		03-03-2020 08:30:00
      ***********************************/

      elsif(p_transaccion='OBING_GET_PV_CONT')then

        begin
        select ex.usuario_externo into v_agente_venta
                from segu.tusuario_externo ex
                where ex.id_usuario = p_id_usuario;

                select string_to_array (list(distinct (ama.id_punto_venta)::varchar),',')::integer[] into v_puntos_venta
                from obingresos.tboleto_amadeus ama
                where ama.agente_venta = v_agente_venta;


                CREATE TEMPORARY TABLE puntos_venta_counter (  id_punto_venta int4,
                                                               estado_reg varchar,
                                                               id_sucursal int4,
                                                               nombre varchar,
                                                               descripcion text,
                                                               codigo varchar,
                                                               habilitar_comisiones varchar,
                                                               formato_comprobante varchar,
                                                               tipo varchar
                                                  )ON COMMIT DROP;


                for v_puntos_venta_counter in ( select
                                              puve.id_punto_venta,
                                              puve.estado_reg,
                                              puve.id_sucursal,
                                              puve.nombre,
                                              puve.descripcion,
                                              puve.codigo,
                                              puve.habilitar_comisiones,
                                              suc.formato_comprobante,
                                              puve.tipo
                                              from vef.tpunto_venta puve
                                              inner join segu.tusuario usu1 on usu1.id_usuario = puve.id_usuario_reg
                                              left join segu.tusuario usu2 on usu2.id_usuario = puve.id_usuario_mod
                                              inner join vef.tsucursal suc on suc.id_sucursal = puve.id_sucursal
                                              where puve.id_punto_venta = any (v_puntos_venta) )
                LOOP

                                insert into puntos_venta_counter (
                                       id_punto_venta,
                                       estado_reg,
                                       id_sucursal,
                                       nombre,
                                       descripcion,
                                       codigo,
                                       habilitar_comisiones,
                                       formato_comprobante,
                                       tipo
                                )VALUES(
                                	   v_puntos_venta_counter.id_punto_venta,
                                       v_puntos_venta_counter.estado_reg,
                                       v_puntos_venta_counter.id_sucursal,
                                       v_puntos_venta_counter.nombre,
                                       v_puntos_venta_counter.descripcion,
                                       v_puntos_venta_counter.codigo,
                                       v_puntos_venta_counter.habilitar_comisiones,
                                       v_puntos_venta_counter.formato_comprobante,
                                       v_puntos_venta_counter.tipo
                                );

                end loop;
          --Sentencia de la consulta de conteo de registros
          v_consulta:='select
						 count (id_punto_venta)
                         from puntos_venta_counter ';
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

ALTER FUNCTION obingresos.ft_boleto_sel (p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
  OWNER TO postgres;
