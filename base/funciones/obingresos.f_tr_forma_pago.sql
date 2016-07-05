CREATE OR REPLACE FUNCTION obingresos.f_tr_forma_pago (
)
RETURNS trigger AS
$body$
DECLARE
  v_id_entidad	integer;  
BEGIN
  IF TG_OP = 'INSERT' THEN
  	
    select s.id_entidad into v_id_entidad
    from vef.tsucursal s   
    where param.f_get_id_lugar_pais(s.id_lugar) = NEW.id_lugar
    limit 1 offset 0;
    
    if (v_id_entidad is not null and NEW.codigo not in ('CM','CHQV') and 
    	substring(NEW.codigo from 1 for 2) != 'RF' and substring(NEW.codigo from 1 for 2) != 'TC') then
      INSERT INTO 
            vef.tforma_pago
          (
            id_usuario_reg,
            id_forma_pago,          
            codigo,
            nombre,
            id_entidad,
            id_moneda,
            defecto,
            registrar_tarjeta,
            registrar_cc,
            registrar_tipo_tarjeta
          )
          VALUES (
            NEW.id_usuario_reg,          
            NEW.id_forma_pago,
            NEW.codigo,
            NEW.nombre,
            v_id_entidad,
            NEW.id_moneda,
            'no',
            (case when substring(NEW.codigo from 1 for 2) = 'CC' THEN
              'si' 
            else
              'no'
            end),
            (case when substring(NEW.codigo from 1 for 2) = 'CT' THEN
              'si' 
            else
              'no'
            end),
            'no'
          );
      end if;
    
  ELSIF TG_OP = 'UPDATE' THEN
  	if (exists (select 1 
    			from vef.tforma_pago 
                where id_forma_pago = NEW.id_forma_pago))then
    	UPDATE 
          vef.tforma_pago 
        SET           
          estado_reg = NEW.estado_reg,         
          nombre = NEWnombre,          
          id_moneda = NEW.id_moneda          
        WHERE 
          id_forma_pago = NEW.id_forma_pago;
    else
    	select s.id_entidad into v_id_entidad
        from vef.tsucursal s        
        where param.f_get_id_lugar_pais(s.id_lugar) = NEW.id_lugar
        limit 1 offset 0;
        if (v_id_entidad is not null and NEW.codigo not in ('CM','CHQV') and 
    	substring(NEW.codigo from 1 for 2) != 'RF' and substring(NEW.codigo from 1 for 2) != 'TC') then
            INSERT INTO 
                  vef.tforma_pago
                (
                  id_usuario_reg,
                  id_forma_pago,          
                  codigo,
                  nombre,
                  id_entidad,
                  id_moneda,
                  defecto,
                  registrar_tarjeta,
                  registrar_cc,
                  registrar_tipo_tarjeta
                )
                VALUES (
                  NEW.id_usuario_reg,          
                  NEW.id_forma_pago,
                  NEW.codigo,
                  NEW.nombre,
                  v_id_entidad,
                  NEW.id_moneda,
                  'no',
                  (case when substring(NEW.codigo from 1 for 2) = 'CC' THEN
                    'si' 
                  else
                    'no'
                  end),
                  (case when substring(NEW.codigo from 1 for 2) = 'CT' THEN
                    'si' 
                  else
                    'no'
                  end),
                  'no'
                );
            end if;
    
    end if;
  
  ELSIF TG_OP = 'DELETE' THEN
  	delete from vef.tforma_pago
    where id_forma_pago = OLD.id_forma_pago;
  END IF;
  return NEW;
END;
$body$
LANGUAGE 'plpgsql'
VOLATILE
CALLED ON NULL INPUT
SECURITY INVOKER
COST 100;