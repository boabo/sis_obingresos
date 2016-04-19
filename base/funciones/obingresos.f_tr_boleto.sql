CREATE OR REPLACE FUNCTION obingresos.f_tr_boleto (
)
RETURNS trigger AS
$body$
DECLARE
  v_agencia				record;
  v_filtro				varchar;
  v_depositos			record;
  v_saldo				numeric;
  v_monto_a_usar		numeric;
  v_fecha_conversion	date;
  v_saldo_menor_boleto	boolean;
  v_tc					numeric;
  v_boleto_completo		boolean;
  v_id_agencia			integer;
  v_id_moneda			integer;
BEGIN
	select id_agencia into v_id_agencia
    from obingresos.tagencia a
    where trim(BOTH ' ' from a.codigo) = trim(BOTH ' ' from NEW.agt) and trim(BOTH ' ' from a.codigo_int) = trim(BOTH ' ' from NEW.agtnoiata);
    
    if (v_id_agencia is null) then
    	raise exception 'No existe la agencia para el boleto:%',NEW.nro_boleto;
    end if;
    
    select id_moneda into v_id_moneda
    from param.tmoneda m
    where trim(BOTH ' ' from m.codigo_internacional) = trim(BOTH ' ' from NEW.moneda);
    
    if (v_id_moneda is null) then
    	raise exception 'No hay moneda para el boleto:%',NEW.nro_boleto;
    end if;
    
    update obingresos.tboleto
    set id_agencia = v_id_agencia,
    id_moneda_boleto = v_id_moneda,
    liquido = total - comision
    where id_boleto = NEW.id_boleto;
    
	--obtener los datos generales de la agencia
	select * into v_agencia
    from obingresos.tagencia
    where id_agencia = v_id_agencia;
    
    v_filtro = '';
    --si la agencia solo asocia boletos y depositos de la misma moneda aplico filtro
    if (v_agencia.depositos_moneda_boleto = 'si') then
    	v_filtro = v_filtro || ' and dep.id_moneda_deposito = ' || v_id_moneda;
    end if;
    
    
    --se recorren todos los depositos que tienen saldo
    for v_depositos in execute('	select *
    					from obingresos.tdeposito dep
                        where dep.saldo > 0 and 
                        	dep.id_agencia = ' || v_id_agencia || '
                        ' || v_filtro ||
                        'order by id_deposito asc') loop
    	
    	--se verifica con que fecha se debe obtener el tipode  cambio
        if (v_agencia.tipo_cambio = 'venta') then
        	v_fecha_conversion = NEW.fecha_emision;
            
        else
            v_fecha_conversion = v_depositos.fecha; 
        end if;
        --raise exception '%,%,%',NEW.id_moneda_boleto,v_depositos.id_moneda_deposito,v_fecha_conversion;
         --obtener el tipo de cambio de la moneda del boleto a la moneda del deposito
        v_tc = param.f_get_tipo_cambio_v2(v_id_moneda,v_depositos.id_moneda_deposito,
        	v_fecha_conversion,'O');
        
        --el monto que se pagara es el liquido
        v_monto_a_usar = param.f_convertir_moneda(v_id_moneda,v_depositos.id_moneda_deposito,(NEW.liquido - NEW.monto_pagado_moneda_boleto),
        				v_fecha_conversion,'O',2);
        v_boleto_completo = true;      
        --si el monto a pagar es mayor q el saldo solo se paga el saldo
        if (v_monto_a_usar  > v_depositos.saldo)then
        	v_monto_a_usar = v_depositos.saldo; 
            v_boleto_completo = false;           
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
            v_depositos.id_deposito,
            NEW.id_boleto,
            (case when v_boleto_completo THEN
            	NEW.liquido
            else
            	param.f_convertir_moneda(v_depositos.id_moneda_deposito,v_id_moneda,v_monto_a_usar,
        				v_fecha_conversion,'O',2)
            END),           
            v_monto_a_usar,
            v_tc
          );
          -- se actualiza el monto pagado del boleto
          update obingresos.tdeposito
          set saldo = saldo - v_monto_a_usar
          WHERE id_deposito = v_depositos.id_deposito;
        
        NEW.monto_pagado_moneda_boleto = (case when v_boleto_completo THEN
                                            NEW.liquido
                                        else
                                            param.f_convertir_moneda(v_depositos.id_moneda_deposito,v_id_moneda,v_monto_a_usar,
                                                    v_fecha_conversion,'O',2)
                                        END);
        
        update obingresos.tboleto
        set monto_pagado_moneda_boleto = NEW.monto_pagado_moneda_boleto
        WHERE id_boleto = NEW.id_boleto;
        
        --si ya esta todo pagado salimos del loop
        if (exists(select 1
        			from obingresos.tboleto
                    where id_boleto = NEW.id_boleto and liquido = monto_pagado_moneda_boleto)) then
           	exit;
    	end if;	
    end loop;
    
    
    return NEW;
    
    
END;
$body$
LANGUAGE 'plpgsql'
VOLATILE
CALLED ON NULL INPUT
SECURITY INVOKER
COST 100;