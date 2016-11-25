CREATE OR REPLACE FUNCTION obingresos.f_tr_deposito (
)
RETURNS trigger AS
$body$
DECLARE
  v_agencia				record;
  v_filtro				varchar;
  v_boletos				record;
  v_saldo				numeric;
  v_monto_a_usar		numeric;
  v_fecha_conversion	date;
  v_saldo_menor_boleto	boolean;
  v_tc					numeric;
  v_boleto_completo		boolean;
  v_id_agencia			integer;
  v_id_moneda			integer;
  v_consulta			varchar;
BEGIN
	v_id_agencia = NEW.id_agencia;
    if (v_id_agencia is null) then
    	return NEW;
    end if;

    v_id_moneda = NEW.id_moneda;

    if (v_id_moneda is null) then
    	raise exception 'No hay moneda para el deposito:%',NEW.nro_deposito;
    end if;

	--obtener los datos generales de la agencia
	select * into v_agencia
    from obingresos.tagencia
    where id_agencia = v_id_agencia;

    v_filtro = '';
    --si la agencia solo asocia boletos y depositos de la misma moneda aplico filtro
    if (v_agencia.depositos_moneda_boleto = 'si') then
    	v_filtro = v_filtro || ' and bol.id_moneda_boleto = ' || v_id_moneda;
    end if;
    v_saldo = NEW.saldo;
    v_consulta = '	select *
    					from obingresos.tboleto bol
                        where bol.liquido > monto_pagado_moneda_boleto
                        ' || v_filtro ||
                        'order by id_boleto asc';

    --se recorren todos los boletos que no estan completamente pagados
    for v_boletos in execute(v_consulta) loop

    	--se verifica con que fecha se debe obtener el tipode  cambio
        if (v_agencia.tipo_cambio = 'venta') then
            v_fecha_conversion = v_boletos.fecha_emision;
        else
            v_fecha_conversion = NEW.fecha;
        end if;
        --obtener el tipo de cambio de la moneda de deposito a la moneda del boleto
        v_tc = param.f_get_tipo_cambio_v2(v_boletos.id_moneda_boleto,v_id_moneda,
        	v_fecha_conversion,'O');

        --el monto que se pagara es el liquido menos el monto ya pagado
        v_monto_a_usar = param.f_convertir_moneda(v_boletos.id_moneda_boleto,v_id_moneda,(v_boletos.liquido - v_boletos.monto_pagado_moneda_boleto),
        				v_fecha_conversion,'O',2);

        v_boleto_completo = TRUE;
        --si el monto a pagar es mayor q el saldo solo se paga el saldo
        if (v_monto_a_usar  > v_saldo)then
        	v_boleto_completo = FALSE;
        	v_monto_a_usar = v_saldo;
        end if;

        --se inserta la relacion entre boleto y deposito
        INSERT INTO
            obingresos.tdeposito_boleto
          (
            id_usuario_reg,
            fecha_reg,
            estado_reg,
            id_deposito,
            id_boleto,
            monto_moneda_boleto,
            monto_moneda_deposito,
            tc
          )
          VALUES (
            NEW.id_usuario_reg,
            now(),
            'activo',
            NEW.id_deposito,
            v_boletos.id_boleto,
            (case when v_boleto_completo THEN
            	v_boletos.liquido - v_boletos.monto_pagado_moneda_boleto
            else
            	param.f_convertir_moneda(v_id_moneda,v_boletos.id_moneda_boleto,v_monto_a_usar,
        				v_fecha_conversion,'O',2)
            END),
            v_monto_a_usar,
            v_tc
          );
          -- se actualiza el monto pagado del boleto
          update obingresos.tboleto
          set monto_pagado_moneda_boleto = (case when v_boleto_completo THEN
            	v_boletos.liquido
            else
            	param.f_convertir_moneda(v_id_moneda,v_boletos.id_moneda_boleto,v_monto_a_usar,
        				v_fecha_conversion,'O',2)
            END)
          WHERE id_boleto = v_boletos.id_boleto;

        --restamos al saldo lo q usamos en este boleto
        v_saldo = v_saldo - v_monto_a_usar;

        --si ya no tenemos saldo salimos del loop
        if (v_saldo = 0) then
           	exit;
    	end if;
    end loop;
    --El nuevo saldo a insertar es lo q nos queda
    UPDATE obingresos.tdeposito
    set saldo = v_saldo
    where id_deposito = NEW.id_deposito;

    return NEW;


END;
$body$
LANGUAGE 'plpgsql'
VOLATILE
CALLED ON NULL INPUT
SECURITY INVOKER
COST 100;