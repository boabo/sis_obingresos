CREATE OR REPLACE FUNCTION obingresos.ft_consulta_vouchers_sel (
  p_nro_boleto varchar
)
RETURNS TABLE (
  nombre varchar,
  fecha timestamp,
  oficina varchar,
  accion varchar,
  tipo_persona varchar,
  num_boleto varchar,
  pnr varchar
) AS
$body$
/**************************************************************************
 SISTEMA:		Ingresos
 FUNCION: 		obingresos.ft_consulta_vouchers_sel
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'obingresos.tviajero_interno_det'
 AUTOR: 		 (ismael valdivia)
 FECHA:	        25-07-2019 11:11:00
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				25-07-2019 11:11:00							Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'obingresos.tviajero_interno_det'
 #
 ***************************************************************************/

DECLARE
	v_resp					varchar;
    v_consulta				varchar;
    v_counter				varchar;
    v_cajero				varchar;
    v_arreglo				record;
    v_datos_counter			record;
    v_datos_cajero			record;
    v_nombre_funcion   		text;
    v_prueba				record;

BEGIN

	/*********************************
 	#TRANSACCION:  'OBING_VOUCHER_SEL'
 	#DESCRIPCION:	Consulta de registros
 	#AUTOR:		Ismael Valdivia
 	#FECHA:		25-07-2019 11:11:00
	***********************************/


		begin


        	/*********************Creamos tabla Temporal************************/
            CREATE TEMPORARY TABLE temp ( nombre_t  varchar,
                                      fecha_t TIMESTAMP,
                                      oficina_t varchar,
                                      accion_t varchar,
          							  tipo_persona_t varchar,
                                      nro_boleto_t text,
                                      pnr_t varchar )ON COMMIT DROP;
            /*******************************************************************/


        	for v_arreglo in ( select UNNEST(string_to_array(p_nro_boleto,','))::varchar as nro_boleto) LOOP
            /*Recuperamos datos del counter e insertamos en la tabla temporal*/
               for v_datos_counter in (select 	per.nombre_completo2 as nombre,
                                                  vdet.fecha_reg as fecha,
                                                  vdet.id_usuario_reg as counter,
                                                  (LTRIM(v.mensaje,'=>')) as accion,
                                                  'Counter'::varchar as tipo_persona,
                                                  vdet.num_boleto,
                                                  vdet.pnr,
                                                  pven.nombre as oficina
                                                  from obingresos.tviajero_interno_det vdet
                                                  left join obingresos.tviajero_interno v on v.id_viajero_interno = vdet.id_viajero_interno
                                                  inner join segu.tusuario usu on usu.id_usuario = v.id_usuario_reg
                                                  inner join segu.vpersona2 per on per.id_persona = usu.id_persona
                                                  inner join obingresos.tboleto_amadeus ama on ama.nro_boleto = vdet.num_boleto
                                                  inner join vef.tpunto_venta pven on pven.id_punto_venta = ama.id_punto_venta

                                                  where vdet.num_boleto = v_arreglo.nro_boleto) loop



                                                     insert into temp (
                                                        nombre_t,
                                                        fecha_t,
                                                        oficina_t,
                                                        accion_t,
                                                        tipo_persona_t,
                                                        nro_boleto_t,
                                                        pnr_t
                                                        )values(
                                                        v_datos_counter.nombre,
                                                        v_datos_counter.fecha,
                                                        v_datos_counter.oficina,
                                                        v_datos_counter.accion,
                                                        v_datos_counter.tipo_persona,
                                                        v_datos_counter.num_boleto,
                                                        v_datos_counter.pnr
                                                        );



                                                end loop;

            /*-------------------------------------------------------------------------------------------------------------------------------------*/
            /*Recuperamos datos del cajero e insertamos en la tabla temporal*/
             	for v_datos_cajero in (select
                                                    per.nombre_completo2 as nombre,
                                                    ama.fecha_reg as fecha,
                                                    ven.nombre as oficina,
                                                    'Boleto Cobrado'::text as accion,
                                                    'Cajero'::varchar as tipo_persona,
                                                    ama.nro_boleto as num_boleto,
                                                    ama.localizador as pnr
                                                    from obingresos.tboleto_amadeus ama
                                                    inner join vef.tpunto_venta ven on ven.id_punto_venta = ama.id_punto_venta
                                                    inner join segu.tusuario usu on usu.id_usuario = ama.id_usuario_cajero
                                                    inner join segu.vpersona2 per on per.id_persona = usu.id_persona
                                                    where ama.nro_boleto = v_arreglo.nro_boleto) loop

                                                      insert into temp (
                                                      nombre_t,
                                                      fecha_t,
                                                      oficina_t,
                                                      accion_t,
                                                      tipo_persona_t,
                                                      nro_boleto_t,
                                                      pnr_t
                                                      )values(
                                                      v_datos_cajero.nombre,
                                                      v_datos_cajero.fecha,
                                                      v_datos_cajero.oficina,
                                                      v_datos_cajero.accion,
                                                      v_datos_cajero.tipo_persona,
                                                      v_datos_cajero.num_boleto,
                                                      v_datos_cajero.pnr
                                                      );
                                              end loop;


            end loop;


           return QUERY select
           						nombre_t::varchar,
                                fecha_t::timestamp,
                                oficina_t::varchar,
                                accion_t::varchar,
                                tipo_persona_t::varchar,
                                nro_boleto_t::varchar,
                                pnr_t::varchar
                          from temp;


		end;



END;
$body$
LANGUAGE 'plpgsql'
VOLATILE
CALLED ON NULL INPUT
SECURITY INVOKER
COST 100 ROWS 1000;

ALTER FUNCTION obingresos.ft_consulta_vouchers_sel (p_nro_boleto varchar)
  OWNER TO postgres;
