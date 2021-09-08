CREATE OR REPLACE FUNCTION obingresos.ft_reportes_sel (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Ingresos
 FUNCION: 		obingresos.ft_reportes_sel
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'vef.tpunto_venta'
 AUTOR: 		 (franklin.espinoza)
 FECHA:	        11-04-2020 15:14:58
 COMENTARIOS:
 ***************************************************************************/

  DECLARE

	v_consulta    		varchar;
    v_parametros  		record;
    v_nombre_funcion   	text;
    v_resp				varchar;

    v_record_json		jsonb;
    v_contador_id		integer=1;

    v_tipo_cambio		numeric(18,2) = 0;

    v_total_nacional	integer = 0;
    v_total_inter		integer = 0;
    v_total_sinA7		integer = 0;
    v_total_pax_boa		integer = 0;
    v_total_imp_boa		numeric = 0;
    v_total_pax_sabsa	integer = 0;
    v_total_imp_sabsa	numeric = 0;
    v_total_diferencia	numeric = 0;
    v_total_valor		integer = 0;

  BEGIN

    v_nombre_funcion = 'vef.ft_reportes_sel';
    v_parametros = pxp.f_get_record(p_tabla);

    /*********************************
       #TRANSACCION:  'OBING_PUVE_CT_SEL'
     #DESCRIPCION:	Consulta de datos
     #AUTOR:		franklin.espinoza
     #FECHA:		11-04-2020 15:14:58
    ***********************************/

    if(p_transaccion='OBING_PUVE_CT_SEL')then

      begin
        --Sentencia de la consulta
        v_consulta:='select
						puve.id_punto_venta,
						puve.estado_reg,
						puve.id_sucursal,
						puve.nombre,
						puve.descripcion,
						puve.id_usuario_reg,
						puve.fecha_reg,
						puve.id_usuario_ai,
						puve.usuario_ai,
						puve.id_usuario_mod,
						puve.fecha_mod,
						usu1.cuenta as usr_reg,
						usu2.cuenta as usr_mod,
            puve.codigo,
            puve.habilitar_comisiones,
            suc.formato_comprobante,
            puve.tipo,
            tag.codigo_int as office_id,
            lug.nombre as lugar
						from vef.tpunto_venta puve
						inner join segu.tusuario usu1 on usu1.id_usuario = puve.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = puve.id_usuario_mod
				    inner join vef.tsucursal suc on suc.id_sucursal = puve.id_sucursal
            left join obingresos.tagencia tag on tag.codigo = puve.codigo
            left join param.tlugar lug on lug.id_lugar = tag.id_lugar
            where  ';

        --Definicion de la respuesta
        v_consulta:=v_consulta||v_parametros.filtro;
        v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;
        raise notice '%',v_consulta;
        --Devuelve la respuesta
        return v_consulta;
      end;
    /*********************************
     #TRANSACCION:  'OBING_PUVE_CT_CONT'
     #DESCRIPCION:	Conteo de registros
     #AUTOR:		franklin.espinoza
     #FECHA:		11-04-2020 15:14:58
    ***********************************/
    elsif(p_transaccion='OBING_PUVE_CT_CONT')then

      begin
        --Sentencia de la consulta de conteo de registros
        v_consulta:='select count(id_punto_venta)
					    from vef.tpunto_venta puve
					    inner join segu.tusuario usu1 on usu1.id_usuario = puve.id_usuario_reg
						  left join segu.tusuario usu2 on usu2.id_usuario = puve.id_usuario_mod
					    inner join vef.tsucursal suc on suc.id_sucursal = puve.id_sucursal
              left join obingresos.tagencia tag on tag.codigo = puve.codigo
              left join param.tlugar lug on lug.id_lugar = tag.id_lugar
              where ';

        --Definicion de la respuesta
        v_consulta:=v_consulta||v_parametros.filtro;

        --Devuelve la respuesta
        return v_consulta;

      end;

    /*********************************
     #TRANSACCION:  'OBING_DEPO_TIGO_SEL'
     #DESCRIPCION:	depositos realizados tigo money
     #AUTOR:		franklin.espinoza
     #FECHA:		11-04-2020 15:14:58
    ***********************************/
    elsif(p_transaccion='OBING_DEPO_TIGO_SEL')then

      begin
        --Sentencia de la consulta de conteo de registros
        v_consulta = 'select
            dep.fecha_venta,
            dep.monto_total
            from obingresos.tdeposito dep
            where dep.fecha_venta between '''||v_parametros.fecha_desde||'''::date and '''||v_parametros.fecha_hasta||'''::date and dep.agt = ''TMY''';


      --Definicion de la respuesta
      --v_consulta:=v_consulta||v_parametros.filtro;

        --Devuelve la respuesta
        return v_consulta;

      end;

    /*********************************
     #TRANSACCION:  'OBING_CALCULO_A7_SEL'
     #DESCRIPCION:	Data para el listado Calculo A7
     #AUTOR:		franklin.espinoza
     #FECHA:		22-12-2020 15:14:58
    ***********************************/
    elsif(p_transaccion='OBING_CALCULO_A7_SEL')then

      begin

        if jsonb_typeof(v_parametros.dataA7->'dataA7') = 'array' then

          create temp table ttcalculo_vuelos(
          		  id_vuelo			integer,
                  VueloID			integer,
                  FechaVuelo		date,
                  NroVuelo			varchar,
                  RutaVl			varchar,
                  NroPaxBoA			varchar,
                  NroPAxSabsa		varchar,
                  ImporteSabsa		numeric,
                  ImporteBoa		numeric,
                  diferencia		numeric,
                  total_nac			integer,
                  total_inter		integer,
                  total_cero		integer,
                  MatriculaBoa		varchar,
                  MatriculaSabsa	varchar,
                  RutaSabsa		    varchar,
                  StatusVl			varchar
          )on commit drop;
          for v_record_json in SELECT * FROM jsonb_array_elements(v_parametros.dataA7->'dataA7') loop
          	--raise 'v_record_json: %',(v_record_json->>'PaxA7Nac');
          	insert into ttcalculo_vuelos(
              id_vuelo,
              VueloID,
              FechaVuelo,
              NroVuelo,
              RutaVl,
              NroPaxBoA,
              NroPAxSabsa,
              ImporteSabsa,
              ImporteBoa,
              diferencia,
              total_nac,
              total_inter,
              total_cero,
              MatriculaBoa,
              MatriculaSabsa,
              RutaSabsa,
              StatusVl
            )values (
            	v_contador_id,
                (v_record_json->>'VueloID')::integer,
                case when v_parametros.tipo_rep = 'normal' then to_date(v_record_json->>'FechaVuelo', 'Mon DD YYYY HH24:MI:SS:AM') else to_date(v_record_json->>'FechaVuelo', 'YYYYMMDD') end,
                (v_record_json->>'NroVuelo')::varchar,
                case when v_parametros.tipo_rep = 'normal' then (v_record_json->>'RutaVl')::varchar else (v_record_json->>'RutaBoa')::varchar end,
                (v_record_json->>'NroPaxBoA')::varchar,
                (v_record_json->>'NroPAxSabsa')::varchar,
                (v_record_json->>'ImporteSabsa')::numeric,
                (v_record_json->>'ImporteBoa')::numeric,
                coalesce((v_record_json->>'ImporteBoa')::numeric,0::numeric)-(v_record_json->>'ImporteSabsa')::numeric,
                coalesce((v_record_json->>'PaxA7Nac')::integer,0::integer),
                coalesce((v_record_json->>'PaxA7Int')::integer,0::integer),
                coalesce((v_record_json->>'PaxA70')::integer,0::integer),
                case when v_parametros.tipo_rep = 'normal' then ''::varchar else (v_record_json->>'MatriculaBoa')::varchar end,
                case when v_parametros.tipo_rep = 'normal' then ''::varchar else (v_record_json->>'MatriculaSabsa')::varchar end,
                (v_record_json->>'RutaPax')::varchar,
                (v_record_json->>'StatusVl')::varchar
            );

            v_total_nacional = v_total_nacional + coalesce((v_record_json->>'PaxA7Nac')::integer,0::integer);
            v_total_inter = v_total_inter + coalesce((v_record_json->>'PaxA7Int')::integer,0::integer);
            v_total_sinA7 = v_total_sinA7 + coalesce((v_record_json->>'PaxA70')::integer,0::integer);
            v_total_pax_boa = v_total_pax_boa + (v_record_json->>'NroPaxBoA')::integer;
            v_total_imp_boa = v_total_imp_boa + (v_record_json->>'ImporteBoa')::numeric;
            v_total_pax_sabsa = v_total_pax_sabsa + (v_record_json->>'NroPAxSabsa')::integer;
            v_total_imp_sabsa = v_total_imp_sabsa + (v_record_json->>'ImporteSabsa')::numeric;
            v_total_diferencia = v_total_diferencia + (coalesce((v_record_json->>'ImporteBoa')::numeric,0::numeric)-(v_record_json->>'ImporteSabsa')::numeric);

          	v_contador_id = v_contador_id + 1;
          end loop;
        end if;

        	insert into ttcalculo_vuelos(
              id_vuelo,
              VueloID,
              FechaVuelo,
              NroVuelo,
              RutaVl,
              NroPaxBoA,
              NroPAxSabsa,
              ImporteSabsa,
              ImporteBoa,
              diferencia,
              total_nac,
              total_inter,
              total_cero
            )values (
            	v_contador_id,
                0::integer,
                '31/12/9999'::date,
                ''::varchar,
                'TOTAL'::varchar,
                v_total_pax_boa::varchar,
                v_total_pax_sabsa::varchar,
                v_total_imp_sabsa::numeric,
                v_total_imp_boa::numeric,
                v_total_diferencia::numeric,
                v_total_nacional::integer,
                v_total_inter::integer,
                v_total_sinA7::integer
            );

        --Sentencia de la consulta de conteo de registros
        v_consulta = 'select
                          id_vuelo,
                          VueloID vuelo_id,
                          FechaVuelo fecha_vuelo,
                          NroVuelo nro_vuelo,
                          RutaVl ruta_vl,
                          NroPaxBoA nro_pax_boa,
                          ImporteBoa importe_boa,
                          NroPAxSabsa nro_pax_sabsa,
                          ImporteSabsa importe_sabsa,
                          diferencia,
                          total_nac,
                          total_inter,
                          total_cero,
                          MatriculaBoa matricula_boa,
                          MatriculaSabsa matricula_sabsa,
                          RutaSabsa ruta_sabsa,
                          StatusVl status
                      from ttcalculo_vuelos
                      order by fecha_vuelo asc
                       ';


      --Definicion de la respuesta
      	--v_consulta:=v_consulta||v_parametros.filtro;
		--v_consulta:=v_consulta||' order by fecha_vuelo asc';
        --Devuelve la respuesta
        return v_consulta;

      end;

    /*********************************
     #TRANSACCION:  'OBING_DETALLE_A7_SEL'
     #DESCRIPCION:	Data para el listado Detalle Vuelo A7
     #AUTOR:		franklin.espinoza
     #FECHA:		22-12-2020 15:14:58
    ***********************************/
    elsif(p_transaccion='OBING_DETALLE_A7_SEL')then

      begin

        if jsonb_typeof(v_parametros.detalle_vuelo) = 'array' then

          create temp table ttdetalle_vuelo(
          		  id_detalle		integer,
                  ato_origen		varchar,
                  ruta_completa		varchar,
                  nombre_pasajero	varchar,
                  nro_vuelo			varchar,
                  nro_asiento		varchar,
                  fecha_vuelo		date,
                  pnr				varchar,
                  nro_boleto		varchar,
                  hora_vuelo		varchar,
                  estado_vuelo		varchar,
                  valor_a7			numeric,
                  calculo_a7		numeric,
                  pax_id			varchar,
                  std_date			varchar
          )on commit drop;

          for v_record_json in SELECT * FROM jsonb_array_elements(v_parametros.detalle_vuelo) loop

          	if (v_record_json->>'PAX_A7')::numeric = 25 then
            	select tc.venta
                into v_tipo_cambio
                from param.ttipo_cambio tc
                where tc.id_moneda = 2 and tc.fecha = (v_record_json->>'PAX_FECHAVUELO')::date;
            end if;

          	insert into ttdetalle_vuelo(
              	id_detalle,
                ato_origen,
                ruta_completa,
                nombre_pasajero,
                nro_vuelo,
                nro_asiento,
                fecha_vuelo,
                pnr,
                nro_boleto,
                hora_vuelo,
                estado_vuelo,
                valor_a7,
                calculo_a7,
                pax_id,
                std_date
            )values (
            	v_contador_id,
                (v_record_json->>'PAX_AEROPUERTO_ORIGEN')::varchar,
				(v_record_json->>'PAX_RUTACOMPLETA')::varchar,
                (v_record_json->>'PAX_NOMBRECOMPLETO')::varchar,
                (v_record_json->>'PAX_NROVUELO')::varchar,
                (v_record_json->>'PAX_NROASIENTO')::varchar,
                (v_record_json->>'PAX_FECHAVUELO')::date,
                (v_record_json->>'PAX_PNR')::varchar,
                (v_record_json->>'PAX_NRO_BOLETO')::varchar,
                (v_record_json->>'PAX_HORA_PROG')::varchar,
                (v_record_json->>'PAX_ESTADO')::varchar,
                (v_record_json->>'PAX_A7')::numeric,
                case when (v_record_json->>'PAX_A7')::numeric = 25 then 25 * v_tipo_cambio else  (v_record_json->>'PAX_A7')::numeric end,
                (v_record_json->>'PAX_ID')::varchar,
                (v_record_json->>'STD_ddMMyyyyHHmmss')::varchar
            );

            v_total_valor = case when (v_record_json->>'PAX_A7')::numeric = 25 then (25 * v_tipo_cambio)::integer else  (v_record_json->>'PAX_A7')::integer end;
            v_total_inter = v_total_inter + v_total_valor;
          	v_contador_id = v_contador_id + 1;
          end loop;
        end if;

        insert into ttdetalle_vuelo(
              	id_detalle,
                ato_origen,
                ruta_completa,
                nombre_pasajero,
                nro_vuelo,
                nro_asiento,
                fecha_vuelo,
                pnr,
                nro_boleto,
                hora_vuelo,
                estado_vuelo,
                valor_a7,
                calculo_a7,
                pax_id,
                std_date
            )values (
            	v_contador_id,
                ''::varchar,
				''::varchar,
                'ZZZZZZZZZZZZZZZ'::varchar,
                ''::varchar,
                ''::varchar,
                null::date,
                ''::varchar,
                ''::varchar,
                ''::varchar,
                'TOTAL'::varchar,
                null::numeric,
                v_total_inter::numeric,
                ''::varchar,
                ''::varchar
            );

        --Sentencia de la consulta de conteo de registros
        v_consulta = 'select
                          id_detalle,
                          ato_origen,
                          ruta_completa,
                          nombre_pasajero,
                          nro_vuelo,
                          nro_asiento,
                          fecha_vuelo,
                          pnr,
                          nro_boleto,
                          hora_vuelo,
                          estado_vuelo,
                          valor_a7,
                          calculo_a7,
                          pax_id,
                          std_date
                      from ttdetalle_vuelo
                      order by nombre_pasajero asc';


      --Definicion de la respuesta
      --v_consulta:=v_consulta||v_parametros.filtro;

        --Devuelve la respuesta
        return v_consulta;

      end;

    /*********************************
     #TRANSACCION:  'OBING_DET_PAX_A7_SEL'
     #DESCRIPCION:	Data para el listado Detalle Pasajero A7
     #AUTOR:		franklin.espinoza
     #FECHA:		22-12-2020 15:14:58
    ***********************************/
    elsif(p_transaccion='OBING_DET_PAX_A7_SEL')then

      begin

        if jsonb_typeof(v_parametros.detalle_pasajero) = 'array' then

          create temp table ttdetalle_pasajero(
          		  id_pasajero		integer,
                  passenger_id		varchar,
                  is_current		varchar,
                  posicion			varchar,
                  fecha_salida		varchar,
                  fecha_salida_show	date,
                  origen			varchar,
                  destino    		varchar,
                  ticket    		varchar,
                  std       		varchar,
                  std_show			varchar,
                  sta       		varchar,
                  sta_show			varchar,
                  here_a7			varchar,
                  is_sabsa			varchar
          )on commit drop;

          for v_record_json in SELECT * FROM jsonb_array_elements(v_parametros.detalle_pasajero) loop

          	if (v_record_json->>'PAX_A7')::numeric = 25 then
            	select tc.venta
                into v_tipo_cambio
                from param.ttipo_cambio tc
                where tc.id_moneda = 2 and tc.fecha = (v_record_json->>'PAX_FECHAVUELO')::date;
            end if;

          	insert into ttdetalle_pasajero(
              	id_pasajero,
                passenger_id,
                is_current,
                posicion,
                fecha_salida,
                fecha_salida_show,
                origen,
                destino,
                ticket,
                std,
                std_show,
                sta,
                sta_show,
                here_a7,
                is_sabsa
            )values (
            	v_contador_id,
                (v_record_json->>'PassengerId')::varchar,
				(v_record_json->>'IsCurrent')::varchar,
                (v_record_json->>'Position')::varchar,
                (v_record_json->>'DepartureDate')::varchar,
                (v_record_json->>'DepartureDateShow')::date,
                (v_record_json->>'Origin')::varchar,
                (v_record_json->>'Destination')::varchar,
                (v_record_json->>'Ticket')::varchar,
                (v_record_json->>'STD')::varchar,
                (v_record_json->>'STDShow')::varchar,
                (v_record_json->>'STA')::varchar,
                (v_record_json->>'STAShow')::varchar,
                (v_record_json->>'HereA7')::varchar,
                (v_record_json->>'IsSabsa')::varchar
            );
          	v_contador_id = v_contador_id + 1;
          end loop;
        end if;

        --Sentencia de la consulta de conteo de registros
        v_consulta = 'select
                          id_pasajero,
                          passenger_id,
                          is_current,
                          posicion,
                          fecha_salida,
                          fecha_salida_show,
                          origen,
                          destino,
                          ticket,
                          std,
                          std_show,
                          sta,
                          sta_show,
                          here_a7,
                          is_sabsa
                      from ttdetalle_pasajero
                      order by passenger_id asc';


      --Definicion de la respuesta
      --v_consulta:=v_consulta||v_parametros.filtro;

        --Devuelve la respuesta
        return v_consulta;

      end;
    /*********************************
     #TRANSACCION:  'OBING_GETDATEOC_SEL'
     #DESCRIPCION:	Data para el listado Detalle de los periodos Generados
     #AUTOR:		franklin.espinoza
     #FECHA:		31-06-2021
    ***********************************/
    elsif(p_transaccion='OBING_GETDATEOC_SEL')then

      begin


        --Sentencia de la consulta de conteo de registros
        v_consulta = 'select
                          toc.id_calculo_over_comison,
                          toc.tipo,
                          (''''||to_char(toc.fecha_ini_calculo,''dd/mm/yyyy'')::varchar || '' - '' || to_char(toc.fecha_fin_calculo,''dd/mm/yyyy'')::varchar||'''') ::varchar intervalo,
                          toc.calculo_generado,
                          toc.fecha_ini_calculo,
                          toc.fecha_fin_calculo,
                          toc.documento

                      from obingresos.tcalculo_over_comison toc
                      where toc.calculo_generado = ''abonado'' or ( toc.tipo = ''IATA'' and toc.calculo_generado in (''enviado'') )
                      ';


      --Definicion de la respuesta
      --v_consulta:=v_consulta||v_parametros.filtro;

        --Devuelve la respuesta
        return v_consulta;

      end;

    /*********************************
     #TRANSACCION:  'OBING_GETDATEOC_CONT'
     #DESCRIPCION:	Data para el listado Detalle de los periodos Generados
     #AUTOR:		franklin.espinoza
     #FECHA:		31-06-2021
    ***********************************/
    elsif(p_transaccion='OBING_GETDATEOC_CONT')then

      begin


        --Sentencia de la consulta de conteo de registros
        v_consulta = 'select
                          count(id_calculo_over_comison)
                      from obingresos.tcalculo_over_comison toc
                      where toc.calculo_generado = ''abonado'' or ( toc.tipo = ''IATA'' and toc.calculo_generado in (''enviado'') )
                      ';


      --Definicion de la respuesta
      --v_consulta:=v_consulta||v_parametros.filtro;

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

ALTER FUNCTION obingresos.ft_reportes_sel (p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
  OWNER TO postgres;