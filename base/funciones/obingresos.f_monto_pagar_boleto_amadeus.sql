CREATE OR REPLACE FUNCTION obingresos.f_monto_pagar_boleto_amadeus (
  p_id_boleto integer,
  p_monto numeric,
  p_id_forma_pago integer
)
RETURNS varchar AS
$body$
/**************************************************************************
 FUNCION: 		obingresos.f_monto_pagar_boleto_amadeus
 DESCRIPCION:   devuelve el monto de la forma pago disponible para pagar el boleto amadeus
 AUTOR: 	    Gonzalo Sarmiento
 FECHA:			26/10/2017
 COMENTARIOS:
***************************************************************************
 HISTORIA DE MODIFICACIONES:

 DESCRIPCION:
 AUTOR:
 FECHA:
 ***************************************************************************/
DECLARE

    v_resp                      varchar;
    v_nombre_funcion            text;
    v_mensaje_error             text;
    v_boleto           			record;
    v_id_moneda_fp               integer;
    v_tipo_cambio				numeric;
    v_acumulado_moneda_boleto	numeric;
    v_registros					record;
    v_id_moneda_usd				integer;
    v_tc						numeric;
    v_monto_pagado_mb			numeric;
    v_forma_pago				record;

BEGIN
    v_nombre_funcion:='obingresos.f_monto_pagar_boleto_amadeus';
    select b.* into v_boleto
    from obingresos.tboleto_amadeus b
    where b.id_boleto_amadeus = p_id_boleto;

    SELECT coalesce(sum(case when fp.id_moneda = v_boleto.id_moneda_boleto then
    				bfp.importe
    			when fp.id_moneda != v_boleto.id_moneda_boleto and v_boleto.moneda = 'USD' then
                	obingresos.f_round_menor(bfp.importe/v_boleto.tc)
                else
                	obingresos.f_round_menor(bfp.importe*v_boleto.tc)
                end ),0.00) into v_monto_pagado_mb
    from obingresos.tboleto_amadeus_forma_pago bfp
    inner join obingresos.tforma_pago fp on fp.id_forma_pago = bfp.id_forma_pago
    inner join param.tmoneda mon on mon.id_moneda = fp.id_moneda
    where bfp.id_boleto_amadeus = p_id_boleto;

    select fp.*,m.codigo_internacional as moneda into v_forma_pago
    from obingresos.tforma_pago fp
    inner join param.tmoneda m on m.id_moneda = fp.id_moneda
    where fp.id_forma_pago = p_id_forma_pago;

    if (v_boleto.id_moneda_boleto = v_forma_pago.id_moneda) then
    	if (p_monto >= (v_boleto.total - v_boleto.comision -v_monto_pagado_mb)) then
        	return v_boleto.total - v_boleto.comision - v_monto_pagado_mb;
        else
        	return p_monto;
        end if;
    else
    	if (v_boleto.moneda = 'USD') then
        	if (p_monto >= obingresos.f_round_mayor((v_boleto.total - v_boleto.comision - v_monto_pagado_mb) * v_boleto.tc)) then
            	return obingresos.f_round_mayor((v_boleto.total - v_boleto.comision - v_monto_pagado_mb) * v_boleto.tc);
            else
            	return p_monto;
            end if;
        ELSE
        	if (p_monto >= obingresos.f_round_mayor((v_boleto.total - v_boleto.comision - v_monto_pagado_mb) / v_boleto.tc)) then
            	return obingresos.f_round_mayor((v_boleto.total - v_boleto.comision - v_monto_pagado_mb) / v_boleto.tc);
            else
            	return p_monto;
            end if;

        end if;
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