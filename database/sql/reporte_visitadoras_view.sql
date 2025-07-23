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
