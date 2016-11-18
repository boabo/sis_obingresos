CREATE OR REPLACE FUNCTION obingresos.f_get_impuestos_from_cadena (
  p_cadena varchar,
  out out_impuesto varchar,
  out out_valor numeric
)
RETURNS SETOF record AS
$body$
DECLARE
  v_puntero integer;
  v_tamano	integer;
  v_caracter char(1);
  v_valor	varchar;
BEGIN
  v_tamano = length(p_cadena);
  v_puntero = 1;
  v_valor = '';
  --174A7124US28XA49XY39YC39AY124US32XF
  while v_puntero < v_tamano loop
  	v_caracter = substr(p_cadena, v_puntero, 1);
    if ( v_caracter ~* '[A-Z]') then
    	out_impuesto = substr(p_cadena, v_puntero, 2);
        out_valor = v_valor::numeric;
        v_valor = '';
        v_puntero = v_puntero + 2;
        return next;
    ELSE
    	v_valor = v_valor || v_caracter;
        v_puntero = v_puntero + 1;
    end if;
  end loop;

EXCEPTION

	WHEN OTHERS THEN
		out_impuesto = 'ERROR';
        out_valor = 0;
        return;
END;
$body$
LANGUAGE 'plpgsql'
VOLATILE
CALLED ON NULL INPUT
SECURITY INVOKER
COST 100 ROWS 1000;