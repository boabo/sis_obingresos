CREATE OR REPLACE FUNCTION obingresos.f_generar_periodo (
  p_id_pais integer,
  p_id_gestion integer,
  p_tipo varchar,
  p_id_usuario integer,
  p_tipo_periodo varchar
)
RETURNS varchar AS
$body$
DECLARE  
  v_resp	            varchar;
  v_nombre_funcion      text;
  v_mes					integer;
  v_periodo				integer;
  v_fecha_ini			date;
  v_fecha_fin			date;
  v_gestion				integer;
  v_dias				integer;
  v_id_periodo_venta	integer;
BEGIN
	v_nombre_funcion = 'obingresos.f_generar_periodo';
   	--validar que no exista ningun periodo registrado para el pais, gestion y tipo
    
    if (exists (select 1 
    			from obingresos.tperiodo_venta pv
                where pv.estado_reg = 'activo' and pv.id_pais = p_id_pais and
                pv.id_gestion = p_id_gestion and pv.tipo = p_tipo)) then
    	raise exception 'No se puede generar los periodos, ya existe(n) periodos registrados para este tipo de venta';
    end if;
    v_mes = 1;
    v_periodo = 1;
    v_fecha_ini = ('01/01/' || (select g.gestion from param.tgestion g where id_gestion = p_id_gestion))::date;
    v_fecha_fin = ('31/12/' || (select g.gestion from param.tgestion g where id_gestion = p_id_gestion))::date;
    
        
    while v_fecha_ini <= v_fecha_fin loop
    
    	if (p_tipo_periodo = '8_dias_bsp') then
    	--Sentencia de la insercion
           insert into obingresos.tperiodo_venta(
                  id_pais,
                  id_gestion,
                  mes,
                  estado,
                  nro_periodo_mes,
                  fecha_fin,
                  fecha_ini,
                  tipo,				
                  id_usuario_reg			
          ) values(
                  p_id_pais,
                  p_id_gestion,
                  pxp.f_obtener_literal_periodo(v_mes,0),
                  'abierto',
                  v_periodo,
                  v_fecha_ini + interval '7 day',
                  v_fecha_ini,
                  p_tipo,				
                  p_id_usuario
          )returning id_periodo_venta into v_id_periodo_venta;    	
          v_fecha_ini = v_fecha_ini + interval '8 day ';
                    
        elsif (p_tipo_periodo = 'diario') then
            insert into obingresos.tperiodo_venta(
                  id_pais,
                  id_gestion,
                  mes,
                  estado,
                  nro_periodo_mes,
                  fecha_fin,
                  fecha_ini,
                  tipo,				
                  id_usuario_reg			
          ) values(
                  p_id_pais,
                  p_id_gestion,
                  pxp.f_obtener_literal_periodo(v_mes,0),
                  'abierto',
                  v_periodo,
                  v_fecha_ini,
                  v_fecha_ini,
                  p_tipo,				
                  p_id_usuario
          )returning id_periodo_venta into v_id_periodo_venta;  
          v_fecha_ini = v_fecha_ini + interval '1 day '; 
        else 
        	raise exception 'No existe ese tipo de periodo';
        end if;
    	
        
        
        if (to_char(v_fecha_ini,'MM')::integer != v_mes) then
        	v_fecha_ini = to_date('01/'||to_char(v_fecha_ini,'MM')||'/'||to_char(v_fecha_ini,'YYYY'),'DD/MM/YYYY');
        	
            update obingresos.tperiodo_venta SET
            	fecha_fin = v_fecha_ini - interval '1 day'
            where id_periodo_venta = v_id_periodo_venta;
            
            v_mes = v_mes + 1;
            v_periodo = 1;
        else
        	v_periodo = v_periodo + 1;
        end if;
    end loop;
    
    
  
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