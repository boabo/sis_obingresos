CREATE OR REPLACE FUNCTION obingresos.f_valida_boleto_amadeus_fp (
  p_id_boleto integer
)
RETURNS varchar AS
$body$
/**************************************************************************
 FUNCION: 		obingresos.f_valida_boleto_amadeus_fp
 DESCRIPCION:   valida que las formas de pago sean iguales o mayores al monto del boleto_amadeus
 AUTOR: 	    RCM
 FECHA:			06/09/2013
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

BEGIN
    v_nombre_funcion:='obingresos.f_valida_boleto_amadeus_fp';
    select * into v_boleto
    from obingresos.tboleto_amadeus
    where id_boleto_amadeus = p_id_boleto;
    v_acumulado_moneda_boleto = 0;

    IF(pxp.f_get_variable_global('instancias_de_pago_nuevas') = 'no') THEN

      for v_registros in (select bfp.*,fp.id_moneda as id_moneda_forma_pago
                          from obingresos.tboleto_amadeus_forma_pago bfp
                          inner join obingresos.tforma_pago fp on fp.id_forma_pago = bfp.id_forma_pago
                          where id_boleto_amadeus = p_id_boleto) loop

          if (v_boleto.id_moneda_boleto != v_registros.id_moneda_forma_pago) then
              v_acumulado_moneda_boleto = v_acumulado_moneda_boleto +
                      param.f_convertir_moneda(	v_registros.id_moneda_forma_pago,
                                                  v_boleto.id_moneda_boleto,
                                                  v_registros.importe,
                                                  v_boleto.fecha_emision,'O',2);
          else
              v_acumulado_moneda_boleto = v_acumulado_moneda_boleto + v_registros.importe;
          end if;
      end loop;

    else
    	for v_registros in (select bfp.*
                          from obingresos.tboleto_amadeus_forma_pago bfp
                          --inner join obingresos.tforma_pago fp on fp.id_forma_pago = bfp.id_forma_pago
                          where id_boleto_amadeus = p_id_boleto) loop
          if (v_boleto.id_moneda_boleto != v_registros.id_moneda) then
              v_acumulado_moneda_boleto = v_acumulado_moneda_boleto +
                      param.f_convertir_moneda(	v_registros.id_moneda,
                                                  v_boleto.id_moneda_boleto,
                                                  v_registros.importe,
                                                  v_boleto.fecha_emision,'O',2);
          else
              v_acumulado_moneda_boleto = v_acumulado_moneda_boleto + v_registros.importe;
          end if;
      end loop;
    end if;

    if ((v_boleto.total-v_boleto.comision) > v_acumulado_moneda_boleto) then
    	raise exception 'El total a pagar definido en las formas de pago:% ,no iguala al total del boleto : %',v_acumulado_moneda_boleto,v_boleto.total;

    elsif ((v_acumulado_moneda_boleto - (v_boleto.total-v_boleto.comision)) > 1) then
    	raise exception ' El total a pagar definido en las formas de pago:% , es mayor al total del boleto: %',v_acumulado_moneda_boleto,v_boleto.total;
    end if;

	return 'exito';

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

ALTER FUNCTION obingresos.f_valida_boleto_amadeus_fp (p_id_boleto integer)
  OWNER TO postgres;
