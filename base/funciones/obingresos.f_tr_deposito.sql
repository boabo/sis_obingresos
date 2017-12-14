CREATE OR REPLACE FUNCTION obingresos.f_tr_deposito (
)
RETURNS trigger AS
$body$
DECLARE
  v_agencia				record;
  v_filtro				varchar;
  v_periodo_venta		record;
  v_saldo				numeric;
  v_monto_a_usar		numeric;
  v_fecha_conversion	date;
  v_saldo_menor_boleto	boolean;
  v_tc					numeric;
  v_boleto_completo		boolean;
  v_id_agencia			integer;
  v_id_moneda_base		integer;
  v_id_moneda_usd		integer;
  v_consulta			varchar;
  v_monto_mb			numeric;
  
BEGIN
	v_id_agencia = NEW.id_agencia;
   
    if (v_id_agencia is null) then
    	
    	return NEW;
    elsif (v_id_agencia is not null and NEW.estado = 'validado' and OLD.estado = 'borrador') then
    	
        	v_saldo = obingresos.f_relacionar_periodo_deposito(NEW.id_usuario_reg,NEW.id_deposito,NEW.monto_deposito,NEW.id_moneda_deposito,NEW.fecha,NEW.nro_deposito,v_id_agencia);
        	if (v_saldo > 0) then
            	INSERT INTO 
                        obingresos.tmovimiento_entidad
                      (
                        id_usuario_reg,                        
                        id_usuario_ai,
                        usuario_ai,                        
                        tipo,
                        pnr,
                        fecha,
                        apellido,
                        monto,
                        id_moneda,
                        autorizacion__nro_deposito,
                        garantia,
                        ajuste,
                        id_periodo_venta,
                        id_agencia,
                        monto_total
                      )
                      VALUES (
                        NEW.id_usuario_reg,                        
                         NEW.id_usuario_ai,
              		  	 NEW.usuario_ai,
                        'credito',
                        NULL,
                        NEW.fecha,
                        NULL,
                        v_saldo,
                        NEW.id_moneda_deposito,
                        NEW.nro_deposito,
                        'no',
                        'no',
                        NULL,
                        v_id_agencia,
                        v_saldo
                      );
            	end if;
        
        
    	return NEW;
    else
    	return NEW;
    end if;
    
END;
$body$
LANGUAGE 'plpgsql'
VOLATILE
CALLED ON NULL INPUT
SECURITY INVOKER
COST 100;