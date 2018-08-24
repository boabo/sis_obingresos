CREATE OR REPLACE FUNCTION obingresos.f_get_codigo_periodo (
  p_id_tipo_periodo integer,
  p_fecha_ini date
)
RETURNS varchar AS
$body$
DECLARE
  v_dia					varchar;
  v_semana				varchar;
  v_periodo				varchar;
  v_mes					varchar;
  v_gestion				varchar;
  v_periodo_venta		record;
BEGIN
	select * into v_periodo_venta
    from obingresos.ttipo_periodo tp
    where tp.id_tipo_periodo = p_id_tipo_periodo;

    v_gestion = to_char(p_fecha_ini,'YYYY')::varchar;

    if (v_periodo_venta.medio_pago = 'cuenta_corriente') then

        v_mes = to_char(p_fecha_ini,'MM')::varchar;
        v_dia = to_char(p_fecha_ini,'DD')::varchar;

        v_periodo = (case when v_dia = '01' then
        				'01'
        			when v_dia = '09' then
                    	'02'
                    when v_dia = '16' then
                    	'03'
                    when v_dia = '24' then
                    	'04'
                    END);

        return v_gestion||v_mes||v_periodo;
    else
    	--v_semana = pxp.f_rellena_cero(EXTRACT(week FROM DATE p_fecha_ini)::varchar);
    	--v_dia = EXTRACT(dow FROM DATE p_fecha_ini)::varchar;
        if (v_dia = '1') then
        	v_periodo = '01';
        elsif (v_dia = '3') THEN
        	v_periodo = '02';
        elsif (v_dia = '5') then
        	v_periodo = '03';
        end if;
        return v_gestion||v_semana||v_periodo;
    end if;
END;
$body$
LANGUAGE 'plpgsql'
VOLATILE
CALLED ON NULL INPUT
SECURITY INVOKER
COST 100;