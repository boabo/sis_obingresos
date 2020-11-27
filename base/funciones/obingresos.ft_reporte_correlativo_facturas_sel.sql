CREATE OR REPLACE FUNCTION obingresos.ft_reporte_correlativo_facturas_sel (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Ingresos
 FUNCION: 		obingresos.ft_reporte_correlativo_facturas_sel
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con el reporte 'vef.ft_reporte_correlativo_facturas_sel'
 AUTOR: 		 maylee.perez
 FECHA:	        27-07-2020 15:14:58
 COMENTARIOS:
 ***************************************************************************/

  DECLARE

	v_consulta    		varchar;
    v_parametros  		record;
    v_nombre_funcion   	text;
    v_resp				varchar;

    v_filtro			varchar;
    v_bandera			varchar;

    v_autorizaciones    record;
    v_resultado_fac		record;
    v_v_autorizacion_falla  varchar;
    v_nro_fac			integer;
    v_id_punto_venta	varchar;
    v_id_sucursal		varchar;
    v_id_lugar			varchar;
    v_temp				record;
    v_errores 			varchar;

    v_punto_venta    	record;
    v_punto_venta_falla varchar;
    v_tipo_generacion	varchar;

  BEGIN

    v_nombre_funcion = 'vef.ft_reporte_correlativo_facturas_sel';
    v_parametros = pxp.f_get_record(p_tabla);

    /*********************************
       #TRANSACCION:  'OBING_RCORREFAC_SEL'
     #DESCRIPCION:	Consulta de datos
     #AUTOR:		maylee.perez
     #FECHA:		27-07-2020 15:14:58
    ***********************************/

    if(p_transaccion='OBING_RCORREFAC_SEL')then

      begin
      --raise exception 'llega % - % - % - % - %',v_parametros.fecha_desde, v_parametros.fecha_hasta, v_parametros.tipo_generacion, v_parametros.id_punto_venta, v_parametros.id_lugar;
        --Sentencia de la consulta

       --raise exception 'lllega % - % - %',v_parametros.tipo_generacion, v_parametros.id_sucursal,v_parametros.id_lugar ;

       --si es opcion TODOS en punto de venta
       IF (v_parametros.id_punto_venta = 0) THEN
       		v_id_punto_venta:= '';
       ELSE
       		v_id_punto_venta:= ' and ven.id_punto_venta = '||v_parametros.id_punto_venta||'  ' ;
       END IF;

       --si es opcion TODOS en sucursal
       IF (v_parametros.id_sucursal = 0) THEN
       		v_id_sucursal = '';
       ELSE
       		v_id_sucursal = ' and su.id_sucursal = '||v_parametros.id_sucursal||'  ' ;
       END IF;

       --si es opcion TODOS en punto de venta
       IF (v_parametros.id_lugar = 0) THEN
       		v_id_lugar = ' ';
       ELSE
       		v_id_lugar = ' and lu.id_lugar = '||v_parametros.id_lugar||'  ' ;
       END IF;


        v_bandera:='true';
        v_errores = '';

        --PARA TIPO DE DOCUMENTO manual y computarizada
        IF (v_parametros.tipo_generacion in ('manual', 'computarizada') ) THEN


              FOR v_autorizaciones in SELECT DISTINCT
                                      dos.nroaut as nroaut
                                     from vef.tventa ven
                                      left join vef.tdosificacion dos on dos.id_dosificacion = ven.id_dosificacion
                                      left join vef.tpunto_venta pv on pv.id_punto_venta = ven.id_punto_venta
                                      left join vef.tsucursal su on su.id_sucursal = ven.id_sucursal
                                      left join param.tlugar lu on lu.id_lugar = su.id_lugar
                                      WHERE (ven.estado='finalizado' or ven.estado ='anulado')
                                      and ven.tipo_factura = v_parametros.tipo_generacion

                                      and (case when v_parametros.id_sucursal = 0 then ven.estado!='borrador'
                                      			else ven.id_sucursal = v_parametros.id_sucursal end)

                                      and (case when v_parametros.id_punto_venta = 0 then ven.estado!='borrador'
                                      			else ven.id_punto_venta = v_parametros.id_punto_venta end)

                                      and (case when v_parametros.id_lugar = 0 then ven.estado!='borrador'
                                      			else lu.id_lugar = v_parametros.id_lugar end)
                                      --and lu.id_lugar = v_parametros.id_lugar
                                      and ven.fecha BETWEEN v_parametros.fecha_desde and v_parametros.fecha_hasta

                                      LOOP


                                      select min(ven.nro_factura) as min_fac, max(ven.nro_factura) as max_fac
                                      into v_resultado_fac
                                      from vef.tventa ven
                                      left join vef.tdosificacion dos on dos.id_dosificacion = ven.id_dosificacion
                                      where dos.nroaut = (v_autorizaciones.nroaut)::varchar
                                      and ven.fecha BETWEEN v_parametros.fecha_desde and v_parametros.fecha_hasta ;



                                    IF (v_resultado_fac.min_fac is not null and v_resultado_fac.max_fac is not null) THEN


                                      FOR i IN v_resultado_fac.min_fac .. v_resultado_fac.max_fac LOOP

                                          IF NOT EXISTS(select 1
                                                  from vef.tventa ven
                                                  left join vef.tdosificacion dos on dos.id_dosificacion = ven.id_dosificacion
                                                  where dos.tipo_generacion = v_parametros.tipo_generacion
                                                  and dos.nroaut = (v_autorizaciones.nroaut)::varchar and ven.nro_factura = i )THEN

                                              v_bandera:='false';
                                              v_v_autorizacion_falla:=(v_autorizaciones.nroaut::varchar);
                                              v_nro_fac:=i;



                                            v_errores = v_errores ||' -->Nro Autorización:'||v_v_autorizacion_falla ||' Nro Factura: '|| v_nro_fac || ' Tipo: ' ||initcap(v_parametros.tipo_generacion)||', ';


                                              --raise exception 'Existe inconvenientes en los correlativos, Nro Autorización %, No existe la factura %', v_v_autorizacion_falla,v_nro_fac;


                                          END IF;


                                      END LOOP;

                                    END IF;



              END LOOP;




              IF(v_bandera='true')THEN

                    v_consulta = ' select  (lu.codigo)::varchar as estacion,
                                          (su.codigo||'' - ''||su.nombre)::varchar as sucursal,
                                          (pv.nombre)::varchar as punto_venta,
                                          dos.nroaut,
                                          min(ven.nro_factura)::integer as nro_desde,
                                          max(ven.nro_factura)::integer as nro_hasta,
                                          count(pv.id_punto_venta)::integer as cantidad

                                  from vef.tventa ven
                                  left join vef.tdosificacion dos on dos.id_dosificacion = ven.id_dosificacion
                                  left join vef.tpunto_venta pv on pv.id_punto_venta = ven.id_punto_venta
                                  left join vef.tsucursal su on su.id_sucursal = ven.id_sucursal
                                  left join param.tlugar lu on lu.id_lugar = su.id_lugar

                                  where ven.tipo_factura = '''||v_parametros.tipo_generacion||'''
                                  '||v_id_punto_venta||'
                                  '||v_id_lugar||'
                                  and ven.fecha BETWEEN '''||v_parametros.fecha_desde||''' and '''||v_parametros.fecha_hasta||'''


                                  group by lu.codigo, pv.nombre ,dos.nroaut, su.codigo, su.nombre
                                  order by lu.codigo ';

                         raise notice '%', v_consulta;



                         return v_consulta;

              ELSE

                       IF (v_errores is not null )THEN
                            raise exception 'Los siguientes Documentos tienen Facturas faltantes:  %', v_errores;
                       END IF;

              END IF;


        ELSIF (v_parametros.tipo_generacion = 'recibo' ) THEN
        --PARA TIPO DE DOCUMENTO RECIBO


            IF (v_parametros.tipo_generacion = 'recibo') THEN
            	v_tipo_generacion = '''recibo''';
            END IF;

        	FOR v_punto_venta in SELECT DISTINCT
                                      ven.id_punto_venta
                                     from vef.tventa ven
                                      left join vef.tsucursal su on su.id_sucursal = ven.id_sucursal
                                      left join param.tlugar lu on lu.id_lugar = su.id_lugar
                                      WHERE ven.tipo_factura = v_parametros.tipo_generacion
                                      and (ven.estado='finalizado' or ven.estado ='anulado')
                                      and ven.tipo_factura = v_parametros.tipo_generacion

                                      and (case when v_parametros.id_sucursal = 0 then ven.estado!='borrador'
                                      			else ven.id_sucursal = v_parametros.id_sucursal end)

                                      and (case when v_parametros.id_punto_venta = 0 then ven.estado!='borrador'
                                      			else ven.id_punto_venta = v_parametros.id_punto_venta end)

                                      and (case when v_parametros.id_lugar = 0 then ven.estado!='borrador'
                                      			else lu.id_lugar = v_parametros.id_lugar end)

                                      --and lu.id_lugar = v_parametros.id_lugar
                                      and ven.fecha BETWEEN v_parametros.fecha_desde and v_parametros.fecha_hasta

                                      LOOP


                                      select min(ven.nro_factura) as min_fac, max(ven.nro_factura) as max_fac
                                      into v_resultado_fac
                                      from vef.tventa ven
                                      where ven.id_punto_venta = v_punto_venta.id_punto_venta
                                      and ven.tipo_factura = v_parametros.tipo_generacion
                                      and ven.fecha BETWEEN v_parametros.fecha_desde and v_parametros.fecha_hasta ;



                                    IF (v_resultado_fac.min_fac is not null and v_resultado_fac.max_fac is not null) THEN


                                      FOR i IN v_resultado_fac.min_fac .. v_resultado_fac.max_fac LOOP



                                          IF NOT EXISTS(select 1
                                                        from vef.tventa ven
                                                        --where  ven.id_punto_venta = v_punto_venta.id_punto_venta and ven.nro_factura = i )THEN
                                                        where ven.id_punto_venta = v_punto_venta.id_punto_venta and  ven.nro_factura = i) THEN

                                              v_bandera:='false';
                                              v_nro_fac:=i;
                                             --raise exception 'llega % - %',v_bandera, v_nro_fac;
                                              SELECT pv.nombre
                                              INTO v_punto_venta_falla
                                              FROM vef.tpunto_venta pv
                                              WHERE pv.id_punto_venta = v_punto_venta.id_punto_venta;


                                            v_errores = v_errores ||' -->Punto de Venta:'||v_punto_venta_falla ||' Nro Factura: '|| v_nro_fac || ' Tipo: ' ||initcap(v_parametros.tipo_generacion)||', ';



                                          END IF;


                                      END LOOP;

                                    END IF;



              END LOOP;




              IF(v_bandera='true')THEN

                    v_consulta = ' select  (lu.codigo)::varchar as estacion,
                                          (su.codigo||'' - ''||su.nombre)::varchar as sucursal,
                                          (pv.nombre)::varchar as punto_venta,
                                          ('''')::varchar as nroaut,
                                          min(ven.nro_factura)::integer as nro_desde,
                                          max(ven.nro_factura)::integer as nro_hasta,
                                          count(pv.id_punto_venta)::integer as cantidad

                                  from vef.tventa ven
                                  left join vef.tpunto_venta pv on pv.id_punto_venta = ven.id_punto_venta
                                  left join vef.tsucursal su on su.id_sucursal = ven.id_sucursal
                                  left join param.tlugar lu on lu.id_lugar = su.id_lugar

                                  where ven.tipo_factura = ''recibo''
                                  '||v_id_punto_venta||'
                                  '||v_id_lugar||'
                                  and ven.fecha BETWEEN '''||v_parametros.fecha_desde||''' and '''||v_parametros.fecha_hasta||'''


                                  group by lu.codigo, pv.nombre , su.codigo, su.nombre
                                  order by lu.codigo ';

                         raise notice '%', v_consulta;



                         return v_consulta;

              ELSE

                       IF (v_errores is not null )THEN
                            raise exception 'Los siguientes Documentos tienen Recibos faltantes:  %', v_errores;
                       END IF;

              END IF;



        END IF;



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
