Jefe Comercial: Administra a las visitadoras médicas y puede ver en tiempo real la cantidad de cotizaciones generadas por cada doctor, así como el estado de los pagos. En caso de pagos en línea a través de Izipay, el sistema registra automáticamente la confirmación. Para pagos por transferencia o contra entrega, la visitadora médica puede cargar el comprobante (voucher) en formato PDF o imagen desde su módulo, validando así el pago para proceder con el pedido.

El sistema contempla la gestión integral del flujo de cotización, validación de pagos, asignación de roles y reportes en tiempo real, permitiendo una trazabilidad completa del proceso desde la elección de formulaciones hasta la confirmación del pedido.

############################################################################3333
Para la vista de report de visistadora medica se usa este script
CREATE OR REPLACE VIEW `reporte_visitadoras` AS
SELECT 
    v.id AS visitadora_id,
    v.name AS nombre_visitadora,
    c.id AS cliente_id,
    c.nombre AS nombre_cliente,
    COUNT(co.id) AS numero_cotizaciones,
    COALESCE(SUM(co.total), 0) AS total_cotizado,
    MAX(co.created_at) AS ultima_cotizacion_fecha,
    (
        SELECT cotizaciones.total 
        FROM cotizaciones 
        WHERE cotizaciones.cliente_id = c.id 
        ORDER BY cotizaciones.created_at DESC 
        LIMIT 1
    ) AS ultima_cotizacion_total,
    COUNT(CASE 
        WHEN co.tipo_pago IN ('contra_entrega', 'transferencia') 
        THEN 1 ELSE NULL 
    END) AS cotizaciones_requieren_confirmacion,
    COUNT(DISTINCT conf.id) AS numero_confirmaciones
FROM 
    clientes c
JOIN 
    users v ON c.visitadora_id = v.id
LEFT JOIN 
    cotizaciones co ON co.cliente_id = c.id
LEFT JOIN 
    confirmaciones conf ON conf.cotizacion_id = co.id
GROUP BY 
    v.id, v.name, c.id, c.nombre;
