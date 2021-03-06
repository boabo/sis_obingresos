CREATE OR REPLACE FUNCTION obingresos.f_validacion_venta_web (
)
RETURNS void AS
$body$
  DECLARE
    v_res	text;
    v_id_alarma	integer;
  BEGIN
    with consulta as (select b.nro_boleto,
                        b.numero_tarjeta,
                        b.fecha_emision
                      from obingresos.tboleto_retweb b
                        left join obingresos.tdetalle_boletos_web dbw on dbw.billete = b.nro_boleto
                        left join obingresos.tventa_web_modificaciones anu  on
                                                                              b.nro_boleto = anu.nro_boleto and anu.tipo in ('anulado','tsu_anulado')
                        left join obingresos.tventa_web_modificaciones reem  on
                                                                               b.nro_boleto = reem.nro_boleto_reemision and anu.tipo in ('reemision')
                      where b.fecha_emision >= '01/10/2016'::date and
                            b.fecha_emision < now()::date and
                            b.numero_tarjeta like '%000000005555' and
                            b.tarjeta = 'VI' and
                            b.estado = '1' and
                            dbw.billete is null and
                            anu.nro_boleto is null and
                            reem.nro_boleto_reemision is null

                      order by b.fecha_emision
    )
    select string_agg(c.nro_boleto || ' , ' || c.numero_tarjeta || ' , ' || to_char(c.fecha_emision,'DD/MM/YYYY'),'<BR>') into v_res
    from consulta c;
    v_id_alarma = (select param.f_inserta_alarma_dblink (1,'Boletos con # tarjeta ****5555 no reportados',v_res,'miguel.mamani@boa.bo,aldo.zeballos@boa.bo'));

    select string_agg(b.nro_boleto || ' , ' || to_char(b.fecha_emision,'DD/MM/YYYY'),'<BR>') into v_res
    from obingresos.tventa_web_modificaciones vwm
      inner join obingresos.tboleto b on b.nro_boleto = vwm.nro_boleto and b.estado_reg = 'activo'
    where b.voided = 'no';

    v_id_alarma = (select param.f_inserta_alarma_dblink (1,'Boletos anulados en Resiber, por anular en sistema de ingresos',v_res,'miguel.mamani@boa.bo,aldo.zeballos@boa.bo,dcamacho@boa.bo,xzambrana@boa.bo,gsanabria@boa.bo'));

  END;
$body$
LANGUAGE 'plpgsql'
VOLATILE
CALLED ON NULL INPUT
SECURITY INVOKER
COST 100;