<script>
  Phx.vista.RepTarifasDescuento=Ext.extend(Phx.baseInterfaz,{
        constructor:function(config) {
          this.maestro = config.maestro;
          Phx.vista.RepTarifasDescuento.superclass.constructor.call(this, config);
          this.panel.destroy();
          window.open('http://172.17.110.5:8082/BoAReports/report/Control%20Ingresos/Reporte%20Tarifas%20con%20Descuento', '_blank')
          }
    });
</script>
