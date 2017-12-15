CREATE OR REPLACE FUNCTION obingresos.f_forma_pago_amadeus_mod (
  p_id_boleto integer,
  p_id_forma_pago integer,
  p_numero_tarjeta varchar,
  p_id_auxiliar integer,
  p_usuario integer,
  p_codigo varchar,
  p_importe numeric,
  p_mco varchar
)
RETURNS integer AS
$body$
DECLARE
 v_nombre_funcion   	 text;
 v_resp    				 varchar;
 v_mensaje 			 	 varchar;
 v_registros 			 record;
 v_datos				 record;
 v_forma_pago			 varchar;
 v_codigo_tarjeta		varchar;
 v_res		            varchar;
 v_ctcc					varchar;
 v_moneda				varchar;
 v_cuenta				varchar;
 v_id_mod_forma_pago  integer;
BEGIN
v_nombre_funcion = 'vef.f_act_forma_pago_amadeus';

      WITH punto_venta AS (	SELECT p.id_punto_venta,
                                  l.codigo AS estacion,
                                  lu.codigo AS codigo_pais,
                                  p.nombre,
                                  p.codigo
                                 FROM param.tlugar l
                                 inner join vef.tsucursal s_1 ON s_1.id_lugar = l.id_lugar
                                 inner join vef.tpunto_venta p ON p.id_sucursal = s_1.id_sucursal
                                 inner join param.tlugar lu ON lu.id_lugar = l.id_lugar_fk
                                  )select   a.nro_boleto,
                                          a.fecha_emision,
                                          a.comision,
                                          p.codigo_pais,
                                          p.estacion,
                                          p.codigo
                                          into
                                          v_datos
                                  from obingresos.tboleto_amadeus a
                                  inner join punto_venta p on p.id_punto_venta = a.id_punto_venta
                                  where a.id_boleto_amadeus  = p_id_boleto;

                      select f.codigo,
                            m.codigo_internacional
                      into
                      v_forma_pago,
                      v_moneda
                      from vef.tforma_pago f
                      inner join param.tmoneda m on m.id_moneda = f.id_moneda
                      where f.id_forma_pago = p_id_forma_pago;

                      select fp.codigo into v_codigo_tarjeta
                      from obingresos.tforma_pago fp
                      where fp.id_forma_pago = p_id_forma_pago;

      v_codigo_tarjeta = (case when v_codigo_tarjeta like 'CC%' or v_codigo_tarjeta like 'SF%' then
                      substring(v_codigo_tarjeta from 3 for 2)
              else
                    NULL
            end);

        if (v_codigo_tarjeta is not null) then
            if (substring(p_numero_tarjeta from 1 for 1) != 'X') then
                v_res = pxp.f_valida_numero_tarjeta_credito(p_numero_tarjeta,v_codigo_tarjeta);
            end if;
        end if;


      ---raise exception 'p1 %',  v_moneda;
       INSERT INTO obingresos.tmod_forma_pago ( 	billete,
                                                    forma,
                                                    fecha,
                                                    importe,
                                                    comision,
                                                    agt,
                                                    pais,
                                                    estacion,---
                                                    numero,
                                                    tarjeta,
                                                    moneda,
                                                    autoriza,
                                                    ctacte,
                                                    usuario,
                                                    fecha_mod,
                                                    hora_mod,
                                                    pagomco,
                                                    observa
                                                    )VALUES(
                                                    CAST(v_datos.nro_boleto as  DECIMAL),
                                                    v_forma_pago,
                                                    v_datos.fecha_emision,
                                                    p_importe,
                                                    v_datos.comision,
                                                    CAST(v_datos.codigo as  DECIMAL),
                                                    v_datos.codigo_pais,
                                                    v_datos.estacion, ---
                                                    case
                                                    when v_forma_pago <> 'MCO' then
                                                    p_numero_tarjeta
                                                    else
                                                    ''
                                                    end,
                                                   COALESCE( v_codigo_tarjeta,' '),
                                                    v_moneda,
                                                   COALESCE( p_codigo,' '),
                                                   COALESCE((select a.codigo_auxiliar
      												from conta.tauxiliar a
                                                    where a.id_auxiliar = p_id_auxiliar),' '),
                                                    (select COALESCE(u.cuenta,' ')
                                                    from segu.vusuario u
                                                    where u.id_usuario = p_usuario),
                                                    now()::date,
                                                    to_char( now(), 'HH12:MI:SS'),
                                                    case
                                                    when v_forma_pago = 'MCO' then
                                                    cast( p_mco as DECIMAL)
                                                    else
                                                    0
                                                    end,
                                                    'ERP BOA' )RETURNING id_mod_forma_pago into v_id_mod_forma_pago;


RETURN v_id_mod_forma_pago;

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