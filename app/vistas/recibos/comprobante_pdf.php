<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recibo de Pago Nro <?php echo $datos['comprobante_nro']; ?></title>
    <style>
        /* CSS optimizado y totalmente compatible con el motor de renderizado de Dompdf */
        body {
            font-family: 'Helvetica', Arial, sans-serif;
            color: #1e293b;
            margin: 0;
            padding: 0;
            font-size: 13px;
            line-height: 1.5;
        }
        .container {
            width: 100%;
            padding: 10px;
        }
        .table-master {
            width: 100%;
            border-collapse: collapse;
        }
        .logo-text {
            font-size: 26px;
            font-weight: bold;
            color: #F28C28; /* Color naranja corporativo de OPTICCOM */
            letter-spacing: 1px;
            margin: 0;
        }
        .subtitle {
            font-size: 11px;
            color: #64748b;
            margin: 2px 0 0 0;
        }
        .recibo-badge-box {
            border: 2px solid #0f172a;
            border-radius: 8px;
            padding: 12px;
            text-align: center;
            background-color: #f8fafc;
        }
        .recibo-ruc {
            font-size: 12px;
            font-weight: bold;
            color: #334155;
            letter-spacing: 0.5px;
        }
        .recibo-titulo {
            font-size: 15px;
            font-weight: bold;
            color: #C02F2F;
            margin: 5px 0;
        }
        .recibo-numero {
            font-size: 14px;
            font-weight: bold;
            color: #0f172a;
        }
        .section-title {
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            color: #1e40af;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 5px;
            margin-top: 30px;
            margin-bottom: 10px;
            letter-spacing: 0.5px;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
        }
        .info-table td {
            padding: 5px 0;
            vertical-align: top;
        }
        .label {
            font-weight: bold;
            color: #475569;
            width: 18%;
        }
        .value {
            color: #0f172a;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        .items-table th {
            background-color: #f1f5f9;
            color: #475569;
            font-weight: bold;
            padding: 10px;
            text-align: left;
            font-size: 12px;
            border-bottom: 2px solid #cbd5e1;
        }
        .items-table td {
            padding: 12px 10px;
            border-bottom: 1px solid #e2e8f0;
            color: #334155;
        }
        .total-wrapper {
            width: 100%;
            margin-top: 20px;
        }
        .total-box {
            float: right;
            width: 40%;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            background-color: #f8fafc;
            padding: 10px;
            text-align: right;
            font-size: 16px;
            font-weight: bold;
            color: #0f172a;
        }
        .alert-box {
            margin-top: 140px; /* Margen para evitar que el texto flotado choque */
            background-color: #fefce8;
            border: 1px solid #fef08a;
            border-radius: 6px;
            padding: 12px;
            color: #713f12;
            font-size: 11.5px;
        }
        .footer {
            margin-top: 40px;
            border-top: 1px dashed #cbd5e1;
            padding-top: 15px;
            text-align: center;
            font-size: 11px;
            color: #94a3b8;
        }
    </style>
</head>
<body>

<div class="container">

    <table class="table-master">
        <tr>
            <td style="vertical-align: top;">
                <h1 class="logo-text">OPTICCOM S.A.C.</h1>
                <p class="subtitle">Fibra Óptica de Alta Velocidad e Internet Dedicado</p>
                <p class="subtitle" style="margin-top: 5px;">Pj. Rosario Nro. 582, El Tambo, Huancayo</p>
                <p class="subtitle">Soporte Técnico: +51 918 845 960 | opticcom@outlook.com</p>
            </td>
            <td style="width: 40%; vertical-align: top; text-align: right;">
                <div class="recibo-badge-box">
                    <div class="recibo-ruc">R.U.C. 20612345678</div>
                    <div class="recibo-titulo">RECIBO DE PAGO</div>
                    <div class="recibo-numero">N° <?php echo $datos['comprobante_nro']; ?></div>
                </div>
            </td>
        </tr>
    </table>

    <div class="section-title">Datos del Abonado</div>
    <table class="info-table">
        <tr>
            <td class="label">Señor(a):</td>
            <td class="value"><?php echo htmlspecialchars($datos['cliente_nombre']); ?></td>
            <td class="label" style="width: 12%;">Fecha:</td>
            <td class="value" style="width: 20%;"><?php echo $datos['fecha_pago']; ?></td>
        </tr>
        <tr>
            <td class="label">DNI / RUC:</td>
            <td class="value"><?php echo htmlspecialchars($datos['cliente_dni']); ?></td>
            <td class="label">Método:</td>
            <td class="value"><?php echo htmlspecialchars($datos['metodo_pago'] ?? 'Efectivo/Validado'); ?></td>
        </tr>
        <tr>
            <td class="label">Dirección:</td>
            <td class="value" colspan="3"><?php echo htmlspecialchars($datos['cliente_direccion']); ?></td>
        </tr>
    </table>

    <div class="section-title">Detalle del Comprobante</div>
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 55%;">Descripción del Concepto</th>
                <th style="text-align: center; width: 25%;">Período / Mes</th>
                <th style="text-align: right; width: 20%;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Servicio de Internet por Fibra Óptica - Plan <strong><?php echo htmlspecialchars($datos['plan_nombre']); ?></strong></td>
                <td style="text-align: center;"><?php echo htmlspecialchars($datos['mes_servicio']); ?></td>
                <td style="text-align: right;">S/ <?php echo $datos['monto']; ?></td>
            </tr>
        </tbody>
    </table>

    <table class="table-master" style="margin-top: 15px;">
        <tr>
            <td style="width: 60%;"></td>
            <td>
                <div class="total-box">
                    TOTAL RECIBIDO: S/ <?php echo $datos['monto']; ?>
                </div>
            </td>
        </tr>
    </table>

    <div class="alert-box">
        <strong>Información del Sistema:</strong> Este documento electrónico constituye un comprobante definitivo de la recepción del dinero en nuestros servidores centrales. La transacción financiera ha sido validada, por lo que su cuenta se encuentra actualmente en estado <span style="font-weight: bold; color: #15803d;">AL DÍA / ACTIVO</span>.
    </div>

    <div class="footer">
        OPTICCOM S.A.C. - Conectando tus sueños con la máxima estabilidad.<br>
        Huancayo, Perú - <?php echo date('Y'); ?>
    </div>

</div>

</body>
</html>