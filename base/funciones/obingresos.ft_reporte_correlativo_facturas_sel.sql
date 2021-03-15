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

    v_conexion varchar;
    v_cadena_cnx	varchar;
        v_res_cone	varchar;

    v_cod_punto  varchar;
    v_nom_pv	varchar;
    v_nom_suc	varchar;
    v_nom_esta	varchar;
    v_filtro_pv	varchar;
    v_cod_pv_suc varchar;
    v_consl_carga varchar=' ';

    v_sql_tabla				varchar;
    v_sql_tabla_todo		varchar;
    v_datos_venta			record;
    v_resultado_correlativo	record;

    v_min_id_punto_venta	integer;
    v_bandera_fac			integer;
    v_lugar_vacio			varchar;

    v_id_punto_venta_fac	varchar;
    v_id_lugar_fac			varchar;
    v_id_sucursal_fac		varchar;
    v_cod_punto_for			record;


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

        v_sql_tabla_todo = 'CREATE TEMPORARY TABLE temp_correlativo_todo
    		(	estacion VARCHAR,
            	id_sucursal INTEGER,
                id_punto_venta INTEGER,
                nroaut VARCHAR,
                nro_factura INTEGER,
                tipo_generacion VARCHAR,
                bandera INTEGER
  			) ON COMMIT DROP';

        v_sql_tabla = 'CREATE TEMPORARY TABLE temp_correlativo
    		(	estacion VARCHAR,
            	id_sucursal INTEGER,
                id_punto_venta INTEGER,
                nroaut VARCHAR,
                nro_factura_ini INTEGER,
                nro_factura_fin INTEGER,
                tipo_generacion VARCHAR
  			) ON COMMIT DROP';

        EXECUTE(v_sql_tabla_todo);
         EXECUTE(v_sql_tabla);

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

              -- control caraga comentado revisar tarda mucho
              /*
			  IF v_cod_punto is not null THEN

              			v_cadena_cnx = vef.f_obtener_cadena_conexion_facturacion();
                        v_conexion = (SELECT dblink_connect(v_cadena_cnx));

                  FOR v_autorizaciones in SELECT nro_autorizacion
                                           FROM dblink(''||v_cadena_cnx||'',
                                                    '
                                                    SELECT DISTINCT nro_autorizacion
                                                    from sfe.tfactura
                                                    WHERE sistema_origen = ''CARGA''
                                                    and lower(tipo_factura) = '''||v_parametros.tipo_generacion||'''
                                                    and 0 = 0
                                                    and fecha_factura BETWEEN '''||v_parametros.fecha_desde||''' and '''||v_parametros.fecha_hasta||'''
                                                    ')
                                        AS t1(
                                              nro_autorizacion varchar
                                              )
                                          LOOP

										SELECT min_fac, max_fac into v_resultado_fac
                                        	FROM dblink(''||v_cadena_cnx||'',
                                                    'select min(nro_factura) as min_fac, max(nro_factura) as max_fac
                                                    from sfe.tfactura
                                                    where nro_autorizacion = '''||(v_autorizaciones.nro_autorizacion)::varchar||'''
                                                    and fecha_factura BETWEEN '''||v_parametros.fecha_desde||''' and '''||v_parametros.fecha_hasta||'''
                                                    and sistema_origen = ''CARGA''
                                                    ')
                                            AS t2 (min_fac integer, max_fac integer);



                                        IF (v_resultado_fac.min_fac is not null and v_resultado_fac.max_fac is not null) THEN


                                          FOR i IN v_resultado_fac.min_fac .. v_resultado_fac.max_fac LOOP

                                              IF NOT EXISTS(select 1
                                              			FROM dblink(''||v_cadena_cnx||'',
                                                        '
                                                        select nro_factura
                                                        from sfe.tfactura
                                                      where lower(tipo_factura) = '''||v_parametros.tipo_generacion||'''
                                                      and nro_autorizacion = '''||(v_autorizaciones.nro_autorizacion)::varchar||'''
                                                      and sistema_origen = ''CARGA''
                                                      and nro_factura = '''||i||'''
                                                        ')
                                                        AS t3 (nro_factura varchar)
                                                      )THEN

                                                  v_bandera:='false';
                                                  v_v_autorizacion_falla:=(v_autorizaciones.nro_autorizacion::varchar);
                                                  v_nro_fac:=i;

	                                              v_errores = v_errores ||' -->Nro Autorización:'||v_v_autorizacion_falla ||' Nro Factura: '|| v_nro_fac || ' Tipo: ' ||initcap(v_parametros.tipo_generacion)||', ';

                                              END IF;


                                          END LOOP;

                                        END IF;



                  END LOOP;
                     v_res_cone=(select dblink_disconnect());
              END IF;*/


--si es opcion TODOS en sucursal
               IF (v_parametros.id_sucursal = 0) THEN
                    v_id_sucursal_fac = '';
               ELSE


                   select nombre
                   into v_nom_suc
                   from vef.tsucursal
                   where id_sucursal = v_parametros.id_sucursal;

                   v_id_sucursal_fac =  v_nom_suc ;

               END IF;

               --si es opcion TODOS en punto de venta
               IF (v_parametros.id_punto_venta = 0) THEN

               		select codigo
                   into v_cod_punto
                   from vef.tpunto_venta
                   where tipo = 'carga'
                   and codigo is not null
                   and id_sucursal = v_parametros.id_sucursal;

                    --v_id_punto_venta_fac := '';
                    v_id_punto_venta_fac:= ' and  codigo_punto_venta = '''||v_cod_punto||'''  ' ;
               ELSE


                   --para conexion con sfe.tfactura
                   select codigo, nombre
                   into v_cod_punto, v_nom_pv
                   from vef.tpunto_venta
                   where id_punto_venta = v_parametros.id_punto_venta
                   and tipo = 'carga'
                   and codigo is not null
                   limit 1;

                   v_id_punto_venta_fac:= ' and  codigo_punto_venta = '''||v_cod_punto||'''  ' ;

               END IF;


               --si es opcion TODOS en punto de venta estacion
               IF (v_parametros.id_lugar = 0) THEN
                    v_id_lugar_fac = ' ';
               ELSE

                    select codigo
                    into v_nom_esta
                    from param.tlugar
                    where id_lugar = v_parametros.id_lugar;

                    v_id_lugar_fac = v_nom_esta ;

               END IF;




              -- raise exception 'lllegabd %',v_cod_punto;

              IF(v_bandera='true')THEN


              	 IF (v_parametros.id_punto_venta = 0) THEN
                      FOR v_cod_punto_for in (select codigo
                                             from vef.tpunto_venta
                                             where tipo = 'carga'
                                             and id_sucursal = v_parametros.id_sucursal) LOOP

                                IF v_cod_punto_for is not null THEN

                                  v_cadena_cnx = vef.f_obtener_cadena_conexion_facturacion();
                                  v_conexion = (SELECT dblink_connect(v_cadena_cnx));

                                  v_consl_carga = 'union


                                              (SELECT
                                                     estacion,
                                                     sucursal,
                                                     (select nombre
                                                        from vef.tpunto_venta
                                                        where codigo = codigo_punto_venta
                                                        and tipo = ''carga''
                                                        limit 1) as punto_venta,
                                                     nro_autorizacion,
                                                     nro_desde,
                                                     nro_hasta,
                                                     cantidad
                                                              FROM dblink('''||v_cadena_cnx||''',
                                                                          ''select
                                                                            '''''||v_id_lugar_fac||'''''::varchar as estacion,
                                                                            '''''||v_nom_suc||'''''::varchar as sucursal,
                                                                            codigo_punto_venta,
                                                                            nro_autorizacion,
                                                                            min(nro_factura)::integer as nro_desde,
                                                                            max(nro_factura)::integer as nro_hasta,
                                                                            count(id_factura)::integer as cantidad

                                                                        from sfe.tfactura
                                                                        where
                                                                        estado_reg = ''''activo''''
                                                                        and fecha_factura BETWEEN '''''||v_parametros.fecha_desde||''''' and '''''||v_parametros.fecha_hasta||'''''
                                                                        and sistema_origen = ''''CARGA''''
                                                                        and lower(tipo_factura) = '''''||v_parametros.tipo_generacion||'''''
                                                                        and  codigo_punto_venta = '''''||v_cod_punto||'''''
                                                                        group by nro_autorizacion, codigo_punto_venta
                                                                        order by nro_autorizacion ASC, nro_desde ASC
                                                                           '')
                                                              AS t1(
                                                                    estacion varchar,
                                                                    sucursal varchar,
                                                                    codigo_punto_venta varchar,
                                                                    nro_autorizacion varchar,
                                                                    nro_desde integer,
                                                                    nro_hasta integer,
                                                                    cantidad integer
                                                                    ) ) ';

                              END IF;

                      	END LOOP;


                    ELSE

                        IF v_cod_punto is not null THEN

                          v_cadena_cnx = vef.f_obtener_cadena_conexion_facturacion();
                          v_conexion = (SELECT dblink_connect(v_cadena_cnx));

                          v_consl_carga = 'union


                                      (SELECT
                                             estacion,
                                             sucursal,
                                             (select nombre
                                                from vef.tpunto_venta
                                                where codigo = codigo_punto_venta
                                                and tipo = ''carga''
                                                limit 1) as punto_venta,
                                             nro_autorizacion,
                                             nro_desde,
                                             nro_hasta,
                                             cantidad
                                                      FROM dblink('''||v_cadena_cnx||''',
                                                                  ''select
                                                                  '''''||v_nom_esta||'''''::varchar as estacion,
                                                                  '''''||v_nom_suc||'''''::varchar as sucursal,
                                                                    codigo_punto_venta,
                                                                    nro_autorizacion,
                                                                    min(nro_factura)::integer as nro_desde,
                                                                    max(nro_factura)::integer as nro_hasta,
                                                                    count(id_factura)::integer as cantidad

                                                                from sfe.tfactura
                                                                where
                                                                estado_reg = ''''activo''''
                                                                and fecha_factura BETWEEN '''''||v_parametros.fecha_desde||''''' and '''''||v_parametros.fecha_hasta||'''''
                                                                and sistema_origen = ''''CARGA''''
                                                                and lower(tipo_factura) = '''''||v_parametros.tipo_generacion||'''''
                                                                and  codigo_punto_venta = '''''||v_cod_punto||'''''
                                                                group by nro_autorizacion, codigo_punto_venta
                                                                order by nro_autorizacion ASC, nro_desde ASC
                                                                   '')
                                                      AS t1(
                                                            estacion varchar,
                                                            sucursal varchar,
                                                            codigo_punto_venta varchar,
                                                            nro_autorizacion varchar,
                                                            nro_desde integer,
                                                            nro_hasta integer,
                                                            cantidad integer
                                                            ) ) ';

                      		END IF;

                    END IF;



                    --(may) se modifica porque solo acepta para un punto de venta y todos
                    /*v_consl_carga = 'union


                                    (SELECT
                                    	   estacion,
                                           sucursal,
                                           (select nombre
                                              from vef.tpunto_venta
                                              where codigo = codigo_punto_venta
                                              and tipo = ''carga''
                                              limit 1) as punto_venta,
                                    	   nro_autorizacion,
                                    	   nro_desde,
                                           nro_hasta,
                                           cantidad
                                                    FROM dblink('''||v_cadena_cnx||''',
                                                                ''select
                                                                  '''''||v_nom_esta||'''''::varchar as estacion,
                                                                  '''''||v_nom_suc||'''''::varchar as sucursal,
                                                                  codigo_punto_venta,
                                                                  nro_autorizacion,
                                                                  min(nro_factura)::integer as nro_desde,
                                                                  max(nro_factura)::integer as nro_hasta,
                                                                  count(id_factura)::integer as cantidad

                                                              from sfe.tfactura
                                                              where
                                                              fecha_factura BETWEEN '''''||v_parametros.fecha_desde||''''' and '''''||v_parametros.fecha_hasta||'''''
                                                              and sistema_origen = ''''CARGA''''
                                                              and lower(tipo_factura) = '''''||v_parametros.tipo_generacion||'''''
                                                              and  codigo_punto_venta = '''''||v_cod_punto||'''''
                                                              group by nro_autorizacion, codigo_punto_venta
                                                              order by nro_autorizacion
                                                                 '')
                                                    AS t1(
                                                    	  estacion varchar,
                                                          sucursal varchar,
                                                          codigo_punto_venta varchar,
                                                    	  nro_autorizacion varchar,
                                                          nro_desde integer,
                                                          nro_hasta integer,
                                                          cantidad integer
                                                          ) ) ';
                                                          */

                    --10-03-2021 (may) modificacion segun el reporte si es Detallado(con punto de venta) o consolidado(sin punto de venta)
					--consolidado(sin punto de venta)
                    IF (v_parametros.tipo_reporte = 'consolidado') THEN

                                  v_consulta = ' (select  (lu.codigo)::varchar as estacion,
                                          (su.codigo||'' - ''||su.nombre)::varchar as sucursal,
                                          ''no''::varchar as punto_venta,
                                          dos.nroaut,
                                          min(ven.nro_factura)::integer as nro_desde,
                                          max(ven.nro_factura)::integer as nro_hasta,
                                          count(dos.nroaut)::integer as cantidad

                                  from vef.tventa ven
                                  inner join vef.tdosificacion dos on dos.id_dosificacion = ven.id_dosificacion

                                  left join vef.tsucursal su on su.id_sucursal = ven.id_sucursal
                                  left join param.tlugar lu on lu.id_lugar = su.id_lugar

                                  where ven.tipo_factura = '''||v_parametros.tipo_generacion||'''

                                  '||v_id_lugar||'
                                  '||v_id_sucursal||'
                                  and ven.fecha BETWEEN '''||v_parametros.fecha_desde||''' and '''||v_parametros.fecha_hasta||'''
                                  group by lu.codigo ,dos.nroaut, su.codigo, su.nombre
                                  order by  dos.nroaut ASC, nro_desde ASC )
                                  '||v_consl_carga||'

                                  ';

                    ELSE  --Detallado(con punto de venta)


                         FOR v_datos_venta IN ( SELECT  lu.codigo as estacion,
                                                ven.id_sucursal,
                                                ven.id_punto_venta,
                                                dos.nroaut,
                                                ven.nro_factura

                                        FROM vef.tventa ven
                                        inner join vef.tdosificacion dos on dos.id_dosificacion = ven.id_dosificacion
                                        left join vef.tsucursal su on su.id_sucursal = ven.id_sucursal
                                        left join param.tlugar lu on lu.id_lugar = su.id_lugar
                                        left join vef.tpunto_venta pv on pv.id_punto_venta = ven.id_punto_venta

                                        WHERE  ven.tipo_factura =  v_parametros.tipo_generacion
                                        and (case when v_parametros.id_lugar = 0 then ven.estado!='borrador'
                                      			else lu.id_lugar = v_parametros.id_lugar end)

                                        and (case when v_parametros.id_sucursal = 0 then ven.estado!='borrador'
                                      			else ven.id_sucursal = v_parametros.id_sucursal end)

                                        and (case when v_parametros.id_punto_venta = 0 then ven.estado!='borrador'
                                      			else ven.id_punto_venta = v_parametros.id_punto_venta end)

                                        --and lu.id_lugar = v_parametros.id_lugar
                                        --and su.id_sucursal =v_parametros.id_sucursal
                                        and ven.fecha BETWEEN v_parametros.fecha_desde and v_parametros.fecha_hasta
                                        ORDER BY ven.nro_factura) LOOP


                                     insert into temp_correlativo_todo (estacion,
                                                                     id_sucursal,
                                                                    id_punto_venta,
                                                                    nroaut,
                                                                    nro_factura,
                                                                    tipo_generacion,
                                                                    bandera
                                                                    )
                                                                  values (
                                                                  v_datos_venta.estacion,
                                                                  v_datos_venta.id_sucursal,
                                                                  v_datos_venta.id_punto_venta,
                                                                  v_datos_venta.nroaut,
                                                                  v_datos_venta.nro_factura,
                                                                  v_parametros.tipo_generacion,
                                                                  0
                                                                  );


                         END LOOP;


                         v_lugar_vacio = '' ;
                          select min(tc.nro_factura) as min_fac, max(tc.nro_factura) as max_fac
                          into v_resultado_fac
                          from temp_correlativo_todo tc;

                          /*select min(tc.nro_factura) as min_fac, max(tc.nro_factura) as max_fac
                          into v_resultado_fac
                          from temp_correlativo_todo tc
                          left join vef.tsucursal su on su.id_sucursal = tc.id_sucursal
                          left join param.tlugar lu on lu.id_lugar = su.id_lugar
                          left join vef.tpunto_venta pv on pv.id_punto_venta = tc.id_punto_venta
                          WHERE  tc.tipo_generacion =  v_parametros.tipo_generacion
                          and (case when v_parametros.id_lugar = 0 then tc.nro_factura is not null
                                  else lu.id_lugar = v_parametros.id_lugar end)

                          and (case when v_parametros.id_sucursal = 0 then tc.nro_factura is not null
                                  else tc.id_sucursal = v_parametros.id_sucursal end)

                          and (case when v_parametros.id_punto_venta = 0 then tc.nro_factura is not null
                                  else tc.id_punto_venta = v_parametros.id_punto_venta end);*/


                          select tc.id_punto_venta
                          into v_min_id_punto_venta
                          from temp_correlativo_todo tc
                          where tc.nro_factura = v_resultado_fac.min_fac;

                          v_min_id_punto_venta = v_min_id_punto_venta;


                          IF (v_resultado_fac.min_fac is not null and v_resultado_fac.max_fac is not null) THEN

								--v_min_id_punto_venta = 0;
                                v_bandera_fac = 1;

                            FOR i IN v_resultado_fac.min_fac .. v_resultado_fac.max_fac LOOP

                            	select tc.*
                                into v_resultado_correlativo
                                from temp_correlativo_todo tc
                                where tc.nro_factura = i;

                               --raise notice 'lllegabd % - %',v_min_id_punto_venta,v_resultado_correlativo.id_punto_venta ;
                            --raise exception 'lllegabd % - %',v_min_id_punto_venta,v_resultado_correlativo.id_punto_venta ;
                            	IF (v_min_id_punto_venta = v_resultado_correlativo.id_punto_venta) THEN


                                         UPDATE temp_correlativo_todo SET
                                         bandera = v_bandera_fac
                                         WHERE nro_factura = i;

                                         v_min_id_punto_venta = v_min_id_punto_venta;
                                         v_bandera_fac =v_bandera_fac ;

                                ELSE
                                 		v_bandera_fac = v_bandera_fac +1 ;

                                		 UPDATE temp_correlativo_todo SET
                                         bandera = v_bandera_fac
                                         WHERE nro_factura = i;

                                		select tc.id_punto_venta
                                        into v_min_id_punto_venta
                                        from temp_correlativo_todo tc
                                        where tc.nro_factura = i;

                                        v_min_id_punto_venta = v_min_id_punto_venta;


                                END IF;


                            	--raise exception 'llega % - %',v_resultado_fac.min_fac , i;

                            END LOOP;

                         END IF;




                          v_consulta = ' (select  (tc.estacion)::varchar as estacion,
                                                  (su.codigo||'' - ''||su.nombre)::varchar as sucursal,
                                                  pv.nombre::varchar as punto_venta,
                                                  tc.nroaut,
                                                  min(tc.nro_factura)::integer as nro_desde,
                                                  max(tc.nro_factura)::integer as nro_hasta,
                                                  count(tc.bandera)::integer as cantidad

                                          from temp_correlativo_todo tc
                                          left join vef.tsucursal su on su.id_sucursal = tc.id_sucursal
                                          left join vef.tpunto_venta pv on pv.id_punto_venta = tc.id_punto_venta

                                          where tc.tipo_generacion = '''||v_parametros.tipo_generacion||'''

                                          group by tc.estacion ,su.codigo, su.nombre, pv.nombre,tc.nroaut, tc.bandera,tc.id_punto_venta
                                          order by tc.nroaut ASC, nro_desde ASC)
                                          '||v_consl_carga||'

                              ';


                    END IF;



                         raise notice '%', v_consulta;
                         IF v_cod_punto is not null THEN
							v_res_cone=(select dblink_disconnect());
                         END IF;


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

ALTER FUNCTION obingresos.ft_reporte_correlativo_facturas_sel (p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
  OWNER TO postgres;
