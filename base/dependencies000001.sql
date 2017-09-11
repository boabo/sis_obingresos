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
