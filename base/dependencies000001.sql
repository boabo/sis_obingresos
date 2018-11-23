/********************************************I-DEP-JRR-OBINGRESOS-0-10/12/2015********************************************/

ALTER TABLE ONLY obingresos.tagencia
    ADD CONSTRAINT fk_agencia__id_moneda_control
    FOREIGN KEY (id_moneda_control) REFERENCES param.tmoneda(id_moneda);

ALTER TABLE ONLY obingresos.tboleto
    ADD CONSTRAINT fk_boleto__id_agencia
    FOREIGN KEY (id_agencia) REFERENCES obingresos.tagencia(id_agencia);
    
ALTER TABLE ONLY obingresos.tboleto
    ADD CONSTRAINT fk_boleto__id_moneda_boleto
    FOREIGN KEY (id_moneda_boleto) REFERENCES param.tmoneda(id_moneda);
    
ALTER TABLE ONLY obingresos.tdeposito
    ADD CONSTRAINT fk_deposito__id_moneda_deposito
    FOREIGN KEY (id_moneda_deposito) REFERENCES param.tmoneda(id_moneda);

ALTER TABLE ONLY obingresos.tdeposito
    ADD CONSTRAINT fk_deposito__id_agencia
    FOREIGN KEY (id_agencia) REFERENCES obingresos.tagencia(id_agencia);
    
ALTER TABLE ONLY obingresos.tdeposito_boleto
    ADD CONSTRAINT fk_deposito_boleto__id_boleto
    FOREIGN KEY (id_boleto) REFERENCES obingresos.tboleto(id_boleto);
    
ALTER TABLE ONLY obingresos.tdeposito_boleto
    ADD CONSTRAINT fk_deposito_boleto__id_deposito
    FOREIGN KEY (id_deposito) REFERENCES obingresos.tdeposito(id_deposito);
    
CREATE TRIGGER tdeposito_tr
  AFTER INSERT 
  ON obingresos.tdeposito FOR EACH ROW 
  EXECUTE PROCEDURE obingresos.f_tr_deposito();

    

/********************************************F-DEP-JRR-OBINGRESOS-0-10/12/2015********************************************/


/********************************************I-DEP-JRR-OBINGRESOS-0-08/04/2016********************************************/

ALTER TABLE ONLY obingresos.tperiodo_venta
    ADD CONSTRAINT fk_tperiodo_venta__id_pais
    FOREIGN KEY (id_pais) REFERENCES param.tlugar(id_lugar);
    
ALTER TABLE ONLY obingresos.tperiodo_venta
    ADD CONSTRAINT fk_tperiodo_venta__id_gestion
    FOREIGN KEY (id_gestion) REFERENCES param.tgestion(id_gestion);
    
ALTER TABLE ONLY obingresos.tagencia
    ADD CONSTRAINT fk_tagencia__id_lugar
    FOREIGN KEY (id_lugar) REFERENCES param.tlugar(id_lugar);

ALTER TABLE ONLY obingresos.tforma_pago
    ADD CONSTRAINT fk_tforma_pago__id_lugar
    FOREIGN KEY (id_lugar) REFERENCES param.tlugar(id_lugar);

CREATE TRIGGER trig_partition_boleto
  BEFORE INSERT 
  ON obingresos.tboleto FOR EACH ROW 
  EXECUTE PROCEDURE obingresos.ftrig_partition_boleto();
  
ALTER TABLE ONLY obingresos.taeropuerto
    ADD CONSTRAINT fk_taeropuerto__id_lugar
    FOREIGN KEY (id_lugar) REFERENCES param.tlugar(id_lugar);

/********************************************F-DEP-JRR-OBINGRESOS-0-08/04/2016********************************************/


/********************************************I-DEP-JRR-OBINGRESOS-0-19/07/2016********************************************/
ALTER TABLE ONLY obingresos.tboleto_vuelo
    ADD CONSTRAINT fk_tboleto_vuelo__id_aeropuerto_origen
    FOREIGN KEY (id_aeropuerto_origen) REFERENCES obingresos.taeropuerto(id_aeropuerto);
    
ALTER TABLE ONLY obingresos.tboleto_vuelo
    ADD CONSTRAINT fk_tboleto_vuelo__id_aeropuerto_destino
    FOREIGN KEY (id_aeropuerto_destino) REFERENCES obingresos.taeropuerto(id_aeropuerto);

/********************************************F-DEP-JRR-OBINGRESOS-0-19/07/2016********************************************/

/********************************************I-DEP-JRR-OBINGRESOS-0-07/11/2016********************************************/

ALTER TABLE vef.tventa_detalle
ADD COLUMN id_boleto INTEGER;

/********************************************F-DEP-JRR-OBINGRESOS-0-07/11/2016********************************************/

/************************************I-DEP-JRR-OBINGRESOS-0-16/06/2016*************************************************/

CREATE TRIGGER tforma_pago_tr
AFTER INSERT OR UPDATE
ON obingresos.tforma_pago FOR EACH ROW
EXECUTE PROCEDURE obingresos.f_tr_forma_pago();

/************************************F-DEP-JRR-OBINGRESOS-0-16/06/2016*************************************************/


/************************************I-DEP-JRR-OBINGRESOS-0-17/03/2017*************************************************/
select pxp.f_insert_testructura_gui ('REGBOL', 'VEF');
select pxp.f_insert_testructura_gui ('BOLCAJ', 'VEF');
select pxp.f_insert_testructura_gui ('VENBOLVEN', 'VEF');
/************************************F-DEP-JRR-OBINGRESOS-0-17/03/2017*************************************************/

/************************************I-DEP-JRR-OBINGRESOS-0-16/03/2017*************************************************/
ALTER TABLE obingresos.tforma_pago ENABLE ALWAYS TRIGGER tforma_pago_tr;
/************************************F-DEP-JRR-OBINGRESOS-0-16/03/2017*************************************************/

/********************************************I-DEP-JRR-OBINGRESOS-0-28/04/2016********************************************/
ALTER TABLE ONLY obingresos.tcomision_agencia
    ADD CONSTRAINT fk_tcomision__id_contrato
    FOREIGN KEY (id_contrato) REFERENCES leg.tcontrato(id_contrato);

ALTER TABLE ONLY obingresos.tcomision_agencia
    ADD CONSTRAINT fk_tcomision__id_agencia
    FOREIGN KEY (id_agencia) REFERENCES obingresos.tagencia(id_agencia);

/********************************************F-DEP-JRR-OBINGRESOS-0-28/04/2016********************************************/

/********************************************I-DEP-FFP-OBINGRESOS-0-02/05/2017********************************************/

ALTER TABLE ONLY obingresos.tskybiz_archivo_detalle
  ADD CONSTRAINT fk_skybiz_archivo_detalle__id_skybiz_archivo
FOREIGN KEY (id_skybiz_archivo) REFERENCES obingresos.tskybiz_archivo(id_skybiz_archivo);

/********************************************F-DEP-FFP-OBINGRESOS-0-02/05/2017********************************************/

/********************************************I-DEP-FFP-OBINGRESOS-0-04/05/2017********************************************/

ALTER TABLE ONLY obingresos.tperiodo_venta
    ADD CONSTRAINT fk_tperiodo_venta__id_tipo_periodo
    FOREIGN KEY (id_tipo_periodo) REFERENCES obingresos.ttipo_periodo(id_tipo_periodo);

ALTER TABLE ONLY obingresos.tmovimiento_entidad
    ADD CONSTRAINT fk_tmovimiento_entidad__id_moneda
    FOREIGN KEY (id_moneda) REFERENCES param.tmoneda(id_moneda);

ALTER TABLE ONLY obingresos.tmovimiento_entidad
    ADD CONSTRAINT fk_tmovimiento_entidad__id_periodo_venta
    FOREIGN KEY (id_periodo_venta) REFERENCES obingresos.tperiodo_venta(id_periodo_venta);



ALTER TABLE ONLY obingresos.tmovimiento_entidad
    ADD CONSTRAINT fk_tmovimiento_entidad__id_agencia
    FOREIGN KEY (id_agencia) REFERENCES obingresos.tagencia(id_agencia);


ALTER TABLE ONLY obingresos.tperiodo_venta_agencia
    ADD CONSTRAINT fk_tperiodo_venta_agencia__id_periodo_venta
    FOREIGN KEY (id_periodo_venta) REFERENCES obingresos.tperiodo_venta(id_periodo_venta);

ALTER TABLE ONLY obingresos.tperiodo_venta_agencia
    ADD CONSTRAINT fk_tperiodo_venta_agencia__id_agencia
    FOREIGN KEY (id_agencia) REFERENCES obingresos.tagencia(id_agencia);

ALTER TABLE ONLY obingresos.tdeposito
    ADD CONSTRAINT fk_tdeposito__id_periodo_venta
    FOREIGN KEY (id_periodo_venta) REFERENCES obingresos.tperiodo_venta(id_periodo_venta);


CREATE OR REPLACE VIEW leg.vcontrato (
    id_usuario_reg,
    id_usuario_mod,
    fecha_reg,
    fecha_mod,
    estado_reg,
    id_usuario_ai,
    usuario_ai,
    id_contrato,
    id_estado_wf,
    id_proceso_wf,
    estado,
    tipo,
    objeto,
    fecha_inicio,
    fecha_fin,
    numero,
    id_gestion,
    id_persona,
    id_institucion,
    id_proveedor,
    observaciones,
    solicitud,
    monto,
    id_moneda,
    fecha_elaboracion,
    plazo,
    tipo_plazo,
    id_cotizacion,
    sujeto_contrato,
    moneda,
    fk_id_contrato,
    desc_ingas,
    desc_ot,
    desc_contrato_fk,
    contrato_adhesion,
    id_lugar,
    lugar,
    id_funcionario,
    solicitante,
    id_abogado,
    desc_abogado,
    rpc_regional,
    tipo_agencia,
    tipo_persona,
    tiene_boleta)
AS
SELECT con.id_usuario_reg,
    con.id_usuario_mod,
    con.fecha_reg,
    con.fecha_mod,
    con.estado_reg,
    con.id_usuario_ai,
    con.usuario_ai,
    con.id_contrato,
    con.id_estado_wf,
    con.id_proceso_wf,
    con.estado,
    con.tipo,
    con.objeto,
    con.fecha_inicio,
    con.fecha_fin,
    con.numero,
    con.id_gestion,
    con.id_persona,
    con.id_institucion,
    con.id_proveedor,
    con.observaciones,
    con.solicitud,
    con.monto,
    con.id_moneda,
    con.fecha_elaboracion,
    con.plazo,
    con.tipo_plazo,
    con.id_cotizacion,
        CASE
            WHEN con.id_persona IS NOT NULL THEN per.nombre_completo1
            WHEN con.id_institucion IS NOT NULL THEN ins.nombre::text
            WHEN con.id_proveedor IS NOT NULL THEN pro.desc_proveedor::text
            ELSE 'S/N'::text
        END AS sujeto_contrato,
    mon.moneda,
    con.fk_id_contrato,
    (
    SELECT pxp.list(ci.desc_ingas::text) AS list
    FROM param.tconcepto_ingas ci
    WHERE ci.id_concepto_ingas = ANY (con.id_concepto_ingas)
    ) AS desc_ingas,
    (
    SELECT pxp.list(ot.desc_orden::text) AS list
    FROM conta.torden_trabajo ot
    WHERE ot.id_orden_trabajo = ANY (con.id_orden_trabajo)
    ) AS desc_ot,
        CASE
            WHEN fkcon.id_persona IS NOT NULL THEN (fkper.nombre_completo1 ||
                ' - '::text) || fkcon.numero::text
            WHEN fkcon.id_institucion IS NOT NULL THEN (fkins.nombre::text ||
                ' - '::text) || fkcon.numero::text
            WHEN fkcon.id_proveedor IS NOT NULL THEN
                (fkpro.desc_proveedor::text || ' - '::text) || fkcon.numero::text
            ELSE 'S/N - '::text || fkcon.numero::text
        END AS desc_contrato_fk,
    con.contrato_adhesion,
    con.id_lugar,
    lug.nombre AS lugar,
    con.id_funcionario,
    fun.desc_funcionario1 AS solicitante,
    con.id_abogado,
    abo.desc_funcionario1 AS desc_abogado,
    con.rpc_regional,
    age.tipo_agencia,
    age.tipo_persona,
    bol.tipo AS tiene_boleta
FROM leg.tcontrato con
     JOIN param.tmoneda mon ON mon.id_moneda = con.id_moneda
     LEFT JOIN segu.vpersona per ON per.id_persona = con.id_persona
     LEFT JOIN param.tinstitucion ins ON ins.id_institucion = con.id_institucion
     LEFT JOIN param.vproveedor pro ON pro.id_proveedor = con.id_proveedor
     LEFT JOIN leg.tcontrato fkcon ON fkcon.id_contrato = con.id_contrato_fk
     LEFT JOIN segu.vpersona fkper ON fkper.id_persona = fkcon.id_persona
     LEFT JOIN param.tinstitucion fkins ON fkins.id_institucion = fkcon.id_institucion
     LEFT JOIN param.vproveedor fkpro ON fkpro.id_proveedor = fkcon.id_proveedor
     LEFT JOIN param.tlugar lug ON lug.id_lugar = con.id_lugar
     LEFT JOIN orga.vfuncionario fun ON fun.id_funcionario = con.id_funcionario
     LEFT JOIN orga.vfuncionario abo ON abo.id_funcionario = con.id_abogado
     LEFT JOIN obingresos.tagencia age ON age.id_agencia = con.id_agencia
     LEFT JOIN leg.tanexo bol ON bol.id_contrato = con.id_contrato AND
         bol.tipo::text = 'boleta_garantia'::text;

/********************************************F-DEP-FFP-OBINGRESOS-0-04/05/2017********************************************/


/********************************************I-DEP-FFP-OBINGRESOS-0-16/06/2017********************************************/

ALTER TABLE ONLY obingresos.tdetalle_boletos_web
    ADD CONSTRAINT fk_tdetalle_boletos_web__id_agencia
    FOREIGN KEY (id_agencia) REFERENCES obingresos.tagencia(id_agencia);

/********************************************F-DEP-FFP-OBINGRESOS-0-16/06/2017********************************************/


/********************************************I-DEP-JRR-OBINGRESOS-0-24/07/2017********************************************/

ALTER TABLE ONLY obingresos.tboleto_retweb
    ADD CONSTRAINT fk_tboleto_retweb__id_moneda
    FOREIGN KEY (id_moneda) REFERENCES param.tmoneda(id_moneda);
    
ALTER TABLE ONLY obingresos.tdetalle_boletos_web
    ADD CONSTRAINT fk_tdetalle_boletos_web__id_periodo_venta
    FOREIGN KEY (id_periodo_venta) REFERENCES obingresos.tperiodo_venta(id_periodo_venta);
    
ALTER TABLE ONLY obingresos.tboleto_retweb
    ADD CONSTRAINT fk_boleto_retweb__id_agencia
    FOREIGN KEY (id_agencia) REFERENCES obingresos.tagencia(id_agencia);
    
ALTER TABLE ONLY obingresos.tdetalle_boletos_web
    ADD CONSTRAINT fk_tdetalle_boletos_web__id_moneda
    FOREIGN KEY (id_moneda) REFERENCES param.tmoneda(id_moneda);


/********************************************F-DEP-JRR-OBINGRESOS-0-24/07/2017********************************************/


/********************************************I-DEP-FEA-OBINGRESOS-0-07/11/2018********************************************/

CREATE OR REPLACE VIEW obingresos.vboletos_a_pagar (
    id_agencia,
    id_periodo_venta,
    mes,
    fecha_ini,
    fecha_fin,
    monto_debito)
AS
 SELECT mo.id_agencia,
    mo.id_periodo_venta,
    pe.mes,
    pe.fecha_ini,
    pe.fecha_fin,
    sum(
        CASE
            WHEN mo.ajuste::text = 'no'::text THEN
            CASE
                WHEN mo.id_moneda = 1 THEN mo.monto
                ELSE param.f_convertir_moneda(2, 1, mo.monto, mo.fecha, 'O'::character varying, 2)
            END
            ELSE 0::numeric
        END) + sum(
        CASE
            WHEN mo.ajuste::text = 'si'::text THEN
            CASE
                WHEN mo.id_moneda = 1 THEN mo.monto
                ELSE param.f_convertir_moneda(2, 1, mo.monto, mo.fecha, 'O'::character varying, 2)
            END
            ELSE 0::numeric
        END) AS monto_debito
   FROM obingresos.tmovimiento_entidad mo
     LEFT JOIN obingresos.tperiodo_venta pe ON pe.id_periodo_venta = mo.id_periodo_venta
  WHERE mo.tipo::text = 'debito'::text AND mo.estado_reg::text = 'activo'::text AND mo.cierre_periodo::text = 'no'::text
  GROUP BY mo.id_periodo_venta, mo.tipo, mo.id_agencia, pe.mes, pe.fecha_ini, pe.fecha_fin
  ORDER BY mo.id_periodo_venta;

  CREATE OR REPLACE VIEW obingresos.vboletos_erp (
    nro_boleto,
    importe,
    comision,
    codigo,
    codigo_in,
    cambio,
    fecha_emision,
    total,
    total_ingresos)
AS
 WITH temp AS (
         SELECT m_1.nro_boleto,
            p2_1.importe,
            m_1.comision,
            f_1.codigo,
            p2_1.importe + m_1.comision AS total_ingresos
           FROM obingresos.tboleto_amadeus m_1
             JOIN obingresos.tboleto_amadeus_forma_pago p2_1 ON p2_1.id_boleto_amadeus = m_1.id_boleto_amadeus
             JOIN obingresos.tforma_pago f_1 ON f_1.id_forma_pago = p2_1.id_forma_pago
          WHERE m_1.fecha_emision >= '2018-01-01'::date AND m_1.fecha_emision <= '2018-01-31'::date AND p2_1.importe <> 0::numeric AND m_1.voided::text = 'no'::text AND m_1.estado_reg::text = 'activo'::text AND m_1.voided::text = 'no'::text
          ORDER BY m_1.nro_boleto
        )
 SELECT m.nro_boleto,
    p2.importe,
    m.comision,
    a.codigo,
    f.codigo AS codigo_in,
        CASE
            WHEN f.codigo::text <> a.codigo::text OR f.codigo::text = a.codigo::text AND p2.importe <> a.importe THEN 1
            ELSE 0
        END AS cambio,
    m.fecha_emision,
    p2.importe + m.comision AS total,
    a.total_ingresos
   FROM obingresos.tboleto_2018 m
     JOIN obingresos.tboleto_forma_pago p2 ON p2.id_boleto = m.id_boleto
     JOIN obingresos.tforma_pago f ON f.id_forma_pago = p2.id_forma_pago
     JOIN temp a ON a.nro_boleto::text = m.nro_boleto::text
  WHERE
        CASE
            WHEN f.codigo::text <> a.codigo::text OR f.codigo::text = a.codigo::text AND p2.importe <> a.importe THEN 1
            ELSE 0
        END = 1 AND m.fecha_emision >= '2018-01-01'::date AND m.fecha_emision <= '2018-01-31'::date AND m.voided::text = 'no'::text AND p2.importe <> 0::numeric
  ORDER BY m.nro_boleto;


CREATE OR REPLACE VIEW obingresos.vcomision_boletos (
    id_boleto,
    importe)
AS
 SELECT bc.id_boleto,
    sum(bc.importe) AS importe
   FROM obingresos.tboleto_comision bc
  GROUP BY bc.id_boleto;

CREATE VIEW obingresos.vcredito_ag (
    id_agencia,
    id_periodo_venta,
    tipo,
    mes,
    fecha_ini,
    fecha_fin,
    monto_total)
AS
SELECT mo.id_agencia,
    mo.id_periodo_venta,
    mo.tipo,
    pe.mes,
    pe.fecha_ini,
    pe.fecha_fin,
    sum(mo.monto_total) AS monto_total
FROM obingresos.tmovimiento_entidad mo
     LEFT JOIN obingresos.tperiodo_venta pe ON pe.id_periodo_venta = mo.id_periodo_venta
WHERE mo.tipo::text = 'credito'::text AND mo.estado_reg::text = 'activo'::text
    AND mo.garantia::text = 'no'::text
GROUP BY mo.tipo, mo.id_agencia, mo.id_periodo_venta, pe.mes, pe.fecha_ini, pe.fecha_fin
ORDER BY mo.id_periodo_venta;


CREATE OR REPLACE VIEW obingresos.vdebito_ag (
    id_agencia,
    id_periodo_venta,
    tipo,
    mes,
    fecha_ini,
    fecha_fin,
    monto_total)
AS
 SELECT mo.id_agencia,
    mo.id_periodo_venta,
    mo.tipo,
    pe.mes,
    pe.fecha_ini,
    pe.fecha_fin,
    sum(mo.monto) AS monto_total
   FROM obingresos.tmovimiento_entidad mo
     LEFT JOIN obingresos.tperiodo_venta pe ON pe.id_periodo_venta = mo.id_periodo_venta
  WHERE mo.tipo::text = 'debito'::text AND mo.estado_reg::text = 'activo'::text AND mo.cierre_periodo::text = 'no'::text
  GROUP BY mo.tipo, mo.id_agencia, mo.id_periodo_venta, pe.mes, pe.fecha_ini, pe.fecha_fin
  ORDER BY mo.id_periodo_venta;

  CREATE OR REPLACE VIEW obingresos.vdepositos_imp (
    id_deposito,
    id_agencia,
    fecha,
    nro_deposito,
    monto_deposito,
    estado,
    tipo)
AS
 SELECT mo.id_movimiento_entidad AS id_deposito,
    mo.id_agencia,
    mo.fecha,
    mo.autorizacion__nro_deposito AS nro_deposito,
        CASE
            WHEN mo.id_moneda = 1 THEN mo.monto
            ELSE param.f_convertir_moneda(2, 1, mo.monto, mo.fecha, 'O'::character varying, 2)
        END AS monto_deposito,
    'validado'::character varying AS estado,
    'agencia'::character varying AS tipo
   FROM obingresos.tmovimiento_entidad mo
  WHERE mo.fecha >= '2017-01-08'::date AND mo.fecha <= now()::date AND mo.cierre_periodo::text = 'no'::text AND mo.ajuste::text = 'no'::text AND mo.garantia::text = 'no'::text AND mo.estado_reg::text = 'activo'::text AND mo.tipo::text = 'credito'::text
UNION
 SELECT mo.id_movimiento_entidad AS id_deposito,
    mo.id_agencia,
    mo.fecha,
    mo.autorizacion__nro_deposito AS nro_deposito,
        CASE
            WHEN mo.id_moneda = 1 THEN mo.monto
            ELSE param.f_convertir_moneda(2, 1, mo.monto, mo.fecha, 'O'::character varying, 2)
        END AS monto_deposito,
    'validado'::character varying AS estado,
    'agencia'::character varying AS tipo
   FROM obingresos.tmovimiento_entidad mo
  WHERE mo.fecha >= '2017-01-08'::date AND mo.fecha <= now()::date AND mo.cierre_periodo::text = 'no'::text AND mo.ajuste::text = 'si'::text AND mo.garantia::text = 'no'::text AND mo.estado_reg::text = 'activo'::text AND mo.tipo::text = 'credito'::text
  ORDER BY 3;


  CREATE VIEW obingresos.vdepositos_periodo (
    id_movimiento_entidad,
    id_agencia,
    id_periodo_venta,
    gestion,
    mes,
    fecha_ini,
    fecha_fin,
    fecha,
    autorizacion__nro_deposito,
    monto_total)
AS
SELECT mo.id_movimiento_entidad,
    mo.id_agencia,
    mo.id_periodo_venta,
    g.gestion::character varying AS gestion,
    pe.mes,
    pe.fecha_ini,
    pe.fecha_fin,
    mo.fecha,
    mo.autorizacion__nro_deposito,
    mo.monto_total
FROM obingresos.tmovimiento_entidad mo
     JOIN obingresos.tperiodo_venta pe ON pe.id_periodo_venta = mo.id_periodo_venta
     JOIN param.tgestion g ON g.id_gestion = pe.id_gestion
WHERE mo.tipo::text = 'credito'::text AND mo.estado_reg::text = 'activo'::text
    AND mo.ajuste::text = 'no'::text AND mo.cierre_periodo::text = 'no'::text AND mo.garantia::text = 'no'::text;


CREATE OR REPLACE VIEW obingresos.vpnr (
    localizador,
    total,
    comision,
    liquido,
    id_moneda_boleto,
    moneda,
    neto,
    origen,
    destino,
    fecha_emision,
    boletos,
    pasajeros,
    id_punto_venta,
    id_usuario_reg)
AS
 SELECT tboleto.localizador,
    sum(tboleto.total) AS total,
    sum(tboleto.comision) AS comision,
    sum(tboleto.liquido) AS liquido,
    tboleto.id_moneda_boleto,
    tboleto.moneda,
    sum(tboleto.neto) AS neto,
    tboleto.origen,
    tboleto.destino,
    tboleto.fecha_emision,
    string_agg(tboleto.nro_boleto::text, ' - '::text) AS boletos,
    string_agg(tboleto.pasajero::text, ' - '::text) AS pasajeros,
    tboleto.id_punto_venta,
    tboleto.id_usuario_reg
   FROM obingresos.tboleto
  WHERE tboleto.localizador IS NOT NULL AND tboleto.localizador::text <> ''::text AND tboleto.estado::text <> 'pagado'::text
  GROUP BY tboleto.localizador, tboleto.id_moneda_boleto, tboleto.moneda, tboleto.origen, tboleto.destino, tboleto.fecha_emision, tboleto.id_punto_venta, tboleto.id_usuario_reg;

/********************************************F-DEP-FEA-OBINGRESOS-0-07/11/2018********************************************/
