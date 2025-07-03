<?php


class ReportsController
{
    public static function actualInventoryValue()
    {
        include_once __DIR__ . '/../utils/HTMLToPDF.php';
        include_once __DIR__ . '/../utils/HTMLTableGenerator.php';
        include_once __DIR__ . '/../models/ReportsModel.php';

        $reportsModel = new ReportsModel();
        $rows = $reportsModel->actualInventoryValue();
        $total = array_reduce($rows, fn($sum, $row) => $sum + floatval($row['subtotal']), 0);

        $localDate = date('d-m-Y');
        $fullSubtitle = "Estado actual de Inventario - <span style='font-size: 12px; font-weight: normal;'>{$localDate}</span>";

        $headers = ['Id / Código de barras', 'Producto', 'Precio', 'IVA', 'Stock', 'Subtotal'];

        $rowRenderer = function ($row) {
            return '
                <tr>
                    <td>' . $row['id'] . '</td>
                    <td>' . $row['nombre'] . '</td>
                    <td>$' . number_format($row['precio'], 2, ',', '.') . '</td>
                    <td>' . $row['iva'] . '%</td>
                    <td>' . intval($row['stock']) . '</td>
                    <td>$' . number_format($row['subtotal'], 2, ',', '.') . '</td>
                </tr>
            ';
        };

        $totalRow = ['', '', '', '', 'Total:', '$' . number_format($total, 2, ',', '.')];

        $html = HTMLTableGenerator::generateTable(
            'Reporte Operacional',
            $fullSubtitle,
            $headers,
            $rows,
            $rowRenderer,
            $totalRow
        );

        $converter = new HTMLToPDF($html);
        $converter->convertToPDF();
    }

    public static function providersRanking()
    {
        include_once __DIR__ . '/../utils/HTMLToPDF.php';
        include_once __DIR__ . '/../utils/HTMLTableGenerator.php';
        include_once __DIR__ . '/../models/ReportsModel.php';
        include_once __DIR__ . '/../utils/Validator.php';

        // Obtener el intervalo de fechas
        $startDate = $_GET['from'] ?? null;
        $endDate = $_GET['to'] ?? null;

        if ($startDate != null) {
            $errs = new Validator($startDate, 'from')->required()->date()->getErrors();
            if (count($errs) > 0) {
                $startDate = null;
            }
        }
        if ($endDate != null) {
            $errs = new Validator($endDate, 'to')->required()->date()->getErrors();
            if (count($errs) > 0) {
                $endDate = null;
            }
        }

        $reportsModel = new ReportsModel();
        $rows = $reportsModel->providersRanking(from: $startDate, to: $endDate);

        $localDate = ($startDate ? "Desde: $startDate" : '') . ' - ' . ($endDate ? "Hasta: $endDate" : '');
        $fullSubtitle = "Ranking de proveedores por volúmen de compras <br/>|| <br/> <span style='font-size: 12px; font-weight: normal;'>{$localDate}</span>";

        $headers = ['Documento de Identidad del proveedor', 'Nombre del proveedor', 'Productos comprados'];

        $rowRenderer = function ($row) {
            return '
                <tr>
                    <td>' . $row['id_proveedor'] . '</td>
                    <td>' . $row['nombre'] . '</td>
                    <td>' . $row['volumen'] . '</td>
                </tr>
            ';
        };

        $html = HTMLTableGenerator::generateTable(
            'Reporte Gerencial',
            $fullSubtitle,
            $headers,
            $rows,
            $rowRenderer
        );

        $converter = new HTMLToPDF($html);
        $converter->convertToPDF();
    }

    public static function ingressesEgresses()
    {
        include_once __DIR__ . '/../utils/HTMLToPDF.php';
        include_once __DIR__ . '/../utils/HTMLTableGenerator.php';
        include_once __DIR__ . '/../models/ReportsModel.php';
        include_once __DIR__ . '/../utils/Validator.php';

        // Obtener el intervalo de fechas
        $startDate = $_GET['from'] ?? null;
        $endDate = $_GET['to'] ?? null;

        if ($startDate != null) {
            $errs = new Validator($startDate, 'from')->required()->date()->getErrors();
            if (count($errs) > 0) {
                $startDate = null;
            }
        }
        if ($endDate != null) {
            $errs = new Validator($endDate, 'to')->required()->date()->getErrors();
            if (count($errs) > 0) {
                $endDate = null;
            }
        }

        $reportsModel = new ReportsModel();
        $rows = $reportsModel->ingressesEgresses(from: $startDate, to: $endDate);

        $localDate = ($startDate ? "Desde: $startDate" : '*') . ' / ' . ($endDate ? "Hasta: $endDate" : '*');
        $fullSubtitle = "Historial de pagos en compras y ventas <br/> || <br/> <span style='font-size: 12px; font-weight: normal;'>{$localDate}</span>";

        $headers = ['Fecha de movimiento', 'Monto', 'Tipo', 'Método', 'Referencia'];

        $rowRenderer = function ($row) {
            if (intval($row['id_pedido']) == 0) {
                return '
                    <tr>
                        <td>' . explode(' ', $row['fecha_cuota'])[0] . '</td>
                        <td style="color: #f00; font-weight:bold;"> -' . $row['monto'] . '</td>
                        <td> Egreso </td>
                        <td>' . $row['nombre_metodo'] . '</td>
                        <td>' . $row['nro_referencia'] . '</td>
                    </tr>
                ';
            } else {
                return '
                    <tr>
                        <td>' . explode(' ', $row['fecha_cuota'])[0] . '</td>
                        <td style="color: #0a0; font-weight:bold;"> +' . $row['monto'] . '</td>
                        <td> Ingreso </td>
                        <td>' . $row['nombre_metodo'] . '</td>
                        <td>' . $row['nro_referencia'] . '</td>
                    </tr>
                ';
            }
        };

        $html = HTMLTableGenerator::generateTable(
            'Reporte Operacional',
            $fullSubtitle,
            $headers,
            $rows,
            $rowRenderer
        );

        $converter = new HTMLToPDF($html);
        $converter->convertToPDF();
    }

    public static function waitingOrders()
    {
        include_once __DIR__ . '/../utils/HTMLToPDF.php';
        include_once __DIR__ . '/../utils/HTMLTableGenerator.php';
        include_once __DIR__ . '/../models/ReportsModel.php';

        $reportsModel = new ReportsModel();
        $rows = $reportsModel->waitingOrders();

        $localDate = date('d/m/Y');
        $fullSubtitle = "Listado de pedidos en espera || <span style='font-size: 12px; font-weight: normal;'>{$localDate}</span>";

        $headers = ['Usuario del cliente', 'Id. pedido', 'Fecha', 'Productos Totales', 'Valor total'];

        $rowRenderer = function ($row) {
            return '
                <tr>
                    <td>' . $row['nombre_usuario'] . '</td>
                    <td>' . $row['id_pedido'] . '</td>
                    <td>' . explode(' ', $row['fecha_pedido'])[0] . '</td>
                    <td>' . $row['total_productos'] . '</td>
                    <td>$' . number_format($row['valor'], 2, ',', '.') . '</td>
                </tr>
            ';
        };

        $html = HTMLTableGenerator::generateTable(
            'Reporte Operacional',
            $fullSubtitle,
            $headers,
            $rows,
            $rowRenderer
        );

        $converter = new HTMLToPDF($html);
        $converter->convertToPDF();
    }

    public static function inventoryEntry()
    {
        include_once __DIR__ . '/../utils/HTMLToPDF.php';
        include_once __DIR__ . '/../utils/HTMLTableGenerator.php';
        include_once __DIR__ . '/../models/ReportsModel.php';
        include_once __DIR__ . '/../utils/Validator.php';

        // Obtener el intervalo de fechas
        $startDate = $_GET['from'] ?? null;
        $endDate = $_GET['to'] ?? null;

        if ($startDate != null) {
            $errs = new Validator($startDate, 'from')->required()->date()->getErrors();
            if (count($errs) > 0) {
                $startDate = null;
            }
        }
        if ($endDate != null) {
            $errs = new Validator($endDate, 'to')->required()->date()->getErrors();
            if (count($errs) > 0) {
                $endDate = null;
            }
        }

        $reportsModel = new ReportsModel();
        $rows = $reportsModel->inventoryEntry(from: $startDate, to: $endDate);

        $localDate = ($startDate ? "Desde: $startDate" : '*') . ' / ' . ($endDate ? "Hasta: $endDate" : '*');
        $fullSubtitle = "Entradas de inventario || <span style='font-size: 12px; font-weight: normal;'>{$localDate}</span>";

        $headers = ['ID. del Producto', 'Producto', 'Cantidad Ingresada', 'ID. de Compra', 'Fecha de Compra', 'Proveedor'];

        $rowRenderer = function ($row) {
            return '
                <tr>
                    <td>' . $row['id_producto'] . '</td>
                    <td>' . $row['nombre_producto'] . '</td>
                    <td style="font-weight: bold;">' . $row['cantidad_producto'] . '</td>
                    <td>' . $row['id_compra'] . '</td>
                    <td>' . explode(' ', $row['fecha_compra'])[0] . '</td>
                    <td>' . $row['nombre'] . '</td>
                </tr>
            ';
        };

        $html = HTMLTableGenerator::generateTable(
            'Reporte Operacional',
            $fullSubtitle,
            $headers,
            $rows,
            $rowRenderer
        );

        $converter = new HTMLToPDF($html);
        $converter->convertToPDF();
    }

    public static function inventoryExit()
    {
        include_once __DIR__ . '/../utils/HTMLToPDF.php';
        include_once __DIR__ . '/../utils/HTMLTableGenerator.php';
        include_once __DIR__ . '/../models/ReportsModel.php';
        include_once __DIR__ . '/../utils/Validator.php';

        // Obtener el intervalo de fechas
        $startDate = $_GET['from'] ?? null;
        $endDate = $_GET['to'] ?? null;

        if ($startDate != null) {
            $errs = new Validator($startDate, 'from')->required()->date()->getErrors();
            if (count($errs) > 0) {
                $startDate = null;
            }
        }
        if ($endDate != null) {
            $errs = new Validator($endDate, 'to')->required()->date()->getErrors();
            if (count($errs) > 0) {
                $endDate = null;
            }
        }

        $reportsModel = new ReportsModel();
        $rows = $reportsModel->inventoryExit(from: $startDate, to: $endDate);

        $localDate = ($startDate ? "Desde: $startDate" : '*') . ' / ' . ($endDate ? "Hasta: $endDate" : '*');
        $fullSubtitle = "Salidas de inventario || <span style='font-size: 12px; font-weight: normal;'>{$localDate}</span>";

        $headers = ['ID. del Producto', 'Producto', 'Cantidad Egresada', 'ID. de Pedido', 'Fecha de Pedido', 'Usuario del Cliente'];

        $rowRenderer = function ($row) {
            return '
                <tr>
                    <td>' . $row['id_producto'] . '</td>
                    <td>' . $row['nombre'] . '</td>
                    <td style="font-weight: bold;">' . $row['cantidad_producto'] . '</td>
                    <td>' . $row['id_pedido'] . '</td>
                    <td>' . explode(' ', $row['fecha_pedido'])[0] . '</td>
                    <td>' . $row['nombre_usuario'] . '</td>
                </tr>
            ';
        };

        $html = HTMLTableGenerator::generateTable(
            'Reporte Operacional',
            $fullSubtitle,
            $headers,
            $rows,
            $rowRenderer
        );

        $converter = new HTMLToPDF($html);
        $converter->convertToPDF();
    }

    public static function mostSellingProducts()
    {
        include_once __DIR__ . '/../utils/HTMLToPDF.php';
        include_once __DIR__ . '/../utils/HTMLTableGenerator.php';
        include_once __DIR__ . '/../models/ReportsModel.php';

        // Obtener el intervalo de fechas
        $startDate = $_GET['from'] ?? null;
        $endDate = $_GET['to'] ?? null;

        if ($startDate != null) {
            $errs = new Validator($startDate, 'from')->required()->date()->getErrors();
            if (count($errs) > 0) {
                $startDate = null;
            }
        }
        if ($endDate != null) {
            $errs = new Validator($endDate, 'to')->required()->date()->getErrors();
            if (count($errs) > 0) {
                $endDate = null;
            }
        }

        $reportsModel = new ReportsModel();
        $rows = $reportsModel->mostSellingProducts(from: $startDate, to: $endDate);

        $localDate = ($startDate ? "Desde: $startDate" : '*') . ' / ' . ($endDate ? "Hasta: $endDate" : '*');
        $fullSubtitle = "Productos más vendidos <br/> || <br/> <span style='font-size: 12px; font-weight: normal;'>{$localDate}</span>";

        $headers = ['ID. del Producto', 'Producto', 'Total Vendido', 'Total Recaudado'];

        $rowRenderer = function ($row) {
            return '
                <tr>
                    <td>' . $row['id_producto'] . '</td>
                    <td>' . $row['nombre'] . '</td>
                    <td>' . $row['total_vendido'] . '</td>
                    <td>' . number_format($row['total_recaudado'], 2, ',', '.') . '</td>
                </tr>
            ';
        };

        $html = HTMLTableGenerator::generateTable(
            'Reporte De Supervisión',
            $fullSubtitle,
            $headers,
            $rows,
            $rowRenderer
        );

        $converter = new HTMLToPDF($html);
        $converter->convertToPDF();
    }

    public static function lessSellingProducts()
    {
        include_once __DIR__ . '/../utils/HTMLToPDF.php';
        include_once __DIR__ . '/../utils/HTMLTableGenerator.php';
        include_once __DIR__ . '/../models/ReportsModel.php';

        // Obtener el intervalo de fechas
        $startDate = $_GET['from'] ?? null;
        $endDate = $_GET['to'] ?? null;

        if ($startDate != null) {
            $errs = new Validator($startDate, 'from')->required()->date()->getErrors();
            if (count($errs) > 0) {
                $startDate = null;
            }
        }
        if ($endDate != null) {
            $errs = new Validator($endDate, 'to')->required()->date()->getErrors();
            if (count($errs) > 0) {
                $endDate = null;
            }
        }

        $reportsModel = new ReportsModel();
        $rows = $reportsModel->lessSellingProducts(from: $startDate, to: $endDate);

        $localDate = ($startDate ? "Desde: $startDate" : '*') . ' / ' . ($endDate ? "Hasta: $endDate" : '*');
        $fullSubtitle = "Productos menos vendidos <br/> || <br/> <span style='font-size: 12px; font-weight: normal;'>{$localDate}</span>";

        $headers = ['ID. del Producto', 'Producto', 'Total Vendido', 'Total Recaudado'];

        $rowRenderer = function ($row) {
            return '
                <tr>
                    <td>' . $row['id_producto'] . '</td>
                    <td>' . $row['nombre'] . '</td>
                    <td>' . $row['total_vendido'] . '</td>
                    <td>' . number_format($row['total_recaudado'], 2, ',', '.') . '</td>
                </tr>
            ';
        };

        $html = HTMLTableGenerator::generateTable(
            'Reporte De Supervisión',
            $fullSubtitle,
            $headers,
            $rows,
            $rowRenderer
        );

        $converter = new HTMLToPDF($html);
        $converter->convertToPDF();
    }

    public static function unverifiedPayments()
    {
        include_once __DIR__ . '/../utils/HTMLToPDF.php';
        include_once __DIR__ . '/../utils/HTMLTableGenerator.php';
        include_once __DIR__ . '/../models/ReportsModel.php';

        $reportsModel = new ReportsModel();
        $rows = $reportsModel->unverifiedPayments();

        $fullSubtitle = "Cuotas sin verificar";

        $headers = ['ID. de la cuota', 'Fecha', 'Monto', 'Método de pago'];

        $rowRenderer = function ($row) {
            return '
                <tr>
                    <td>' . $row['id_cuota'] . '</td>
                    <td>' . $row['fecha_cuota'] . '</td>
                    <td>' . $row['monto'] . '</td>
                    <td>' . $row['nombre_metodo'] . '</td>
                </tr>
            ';
        };

        $html = HTMLTableGenerator::generateTable(
            'Reporte De Supervisión',
            $fullSubtitle,
            $headers,
            $rows,
            $rowRenderer
        );

        $converter = new HTMLToPDF($html);
        $converter->convertToPDF();
    }

    public static function unverifiedPays()
    {
        include_once __DIR__ . '/../utils/HTMLToPDF.php';
        include_once __DIR__ . '/../utils/HTMLTableGenerator.php';
        include_once __DIR__ . '/../models/ReportsModel.php';

        $reportsModel = new ReportsModel();
        $rows = $reportsModel->unverifiedPays();

        $fullSubtitle = "Pagos sin verificar";

        $headers = ['ID. del pago', 'Fecha', 'Monto a pagar', 'Monto pagado', 'Fuente'];

        $rowRenderer = function ($row) {
            return '
                <tr>
                    <td>' . $row['id_pago'] . '</td>
                    <td>' . $row['fecha_pago'] . '</td>
                    <td>' . number_format(floatval($row['monto_total']), 2, ',', '.') . '$</td>
                    <td>' . number_format(floatval($row['total_pagado']), 2, ',', '.') . '$</td>
                    <td>' . ($row['id_pedido'] ? ('Pedido ' . $row['id_pedido']) : ('Compra ' . $row['id_compra'])) . '</td>
                </tr>
            ';
        };

        $html = HTMLTableGenerator::generateTable(
            'Reporte De Supervisión',
            $fullSubtitle,
            $headers,
            $rows,
            $rowRenderer
        );

        $converter = new HTMLToPDF($html);
        $converter->convertToPDF();
    }

    public static function cashFlowReport()
    {
        include_once __DIR__ . '/../utils/HTMLToPDF.php';
        include_once __DIR__ . '/../utils/HTMLTemplate.php';
        include_once __DIR__ . '/../models/ReportsModel.php';
        include_once __DIR__ . '/../utils/Validator.php';

        // Get date range
        $startDate = $_GET['from'] ?? null;
        $endDate = $_GET['to'] ?? null;

        if ($startDate != null) {
            $errs = new Validator($startDate, 'from')->required()->date()->getErrors();
            if (count($errs) > 0) {
                $startDate = null;
            }
        }
        if ($endDate != null) {
            $errs = new Validator($endDate, 'to')->required()->date()->getErrors();
            if (count($errs) > 0) {
                $endDate = null;
            }
        }

        $reportsModel = new ReportsModel();
        $data = $reportsModel->cashFlow(from: $startDate, to: $endDate);

        $ingresses = floatval($data['ingresses']);
        $egresses = floatval($data['egresses']);
        $balance = $ingresses - $egresses;

        $localDate = ($startDate ? "Desde: $startDate" : 'Inicio') . ' - ' . ($endDate ? "Hasta: $endDate" : 'Hoy');
        $fullSubtitle = "Flujo de Caja <br/> || <br/> <span style='font-size: 12px; font-weight: normal;'>{$localDate}</span>";

        $balanceColor = $balance >= 0 ? '#0a0' : '#f00';

        $htmlContent = "
            <div style='text-align: center; margin-top: 20px;'>
                <div style='margin-bottom: 20px;'>
                    <h2>Ingresos Totales</h2>
                    <p style='font-size: 24px; color: #0a0; font-weight: bold;'>$" . number_format($ingresses, 2, ',', '.') . "</p>
                </div>
                <div style='margin-bottom: 20px;'>
                    <h2>Egresos Totales</h2>
                    <p style='font-size: 24px; color: #f00; font-weight: bold;'>$" . number_format($egresses, 2, ',', '.') . "</p>
                </div>
                <hr style='margin: 30px 0;'/>
                <div style='margin-bottom: 20px;'>
                    <h2>Balance General</h2>
                    <p style='font-size: 28px; color: {$balanceColor}; font-weight: bold;'>$" . number_format($balance, 2, ',', '.') . "</p>
                </div>
            </div>
        ";

        $html = HTMLTemplate::getTemplate(
            'Reporte Gerencial',
            $fullSubtitle,
            $htmlContent
        );

        $converter = new HTMLToPDF($html);
        $converter->convertToPDF();
    }

    private static function generateProfitabilityReport($reportType)
    {
        include_once __DIR__ . '/../utils/HTMLToPDF.php';
        include_once __DIR__ . '/../utils/HTMLTemplate.php';
        include_once __DIR__ . '/../models/ReportsModel.php';
        include_once __DIR__ . '/../utils/Validator.php';
        include_once __DIR__ . '/../utils/HTMLTableGenerator.php';

        $startDate = $_GET['from'] ?? null;
        $endDate = $_GET['to'] ?? null;

        if ($startDate) {
            $errs = (new Validator($startDate, 'from'))->required()->date()->getErrors();
            if (count($errs) > 0) $startDate = null;
        }
        if ($endDate) {
            $errs = (new Validator($endDate, 'to'))->required()->date()->getErrors();
            if (count($errs) > 0) $endDate = null;
        }

        $reportsModel = new ReportsModel();
        $data = $reportsModel->profitabilityAnalysis($reportType, $startDate, $endDate);

        $title = 'Reporte Gerencial';
        $subtitle = '';
        $headers = [];

        if ($reportType === 'category') {
            $subtitle = 'Análisis de Rentabilidad por Categoría';
            $headers = ['Categoría', 'Ventas Totales', 'Costo Total', 'Ganancia Bruta', 'Margen (%)'];
        } else {
            $subtitle = 'Análisis de Rentabilidad por Fabricante';
            $headers = ['Fabricante', 'Ventas Totales', 'Costo Total', 'Ganancia Bruta', 'Margen (%)'];
        }

        $dateRange = ($startDate ? "Desde: $startDate" : '') . ' - ' . ($endDate ? "Hasta: $endDate" : '');
        $fullSubtitle = "$subtitle <br/> <span style='font-size: 12px; font-weight: normal;'>$dateRange</span>";

        $rowRenderer = function ($row) {
            $totalSales = floatval($row['total_sales']);
            $totalCost = floatval($row['total_cost']);
            $grossProfit = $totalSales - $totalCost;
            $profitMargin = ($totalSales > 0) ? ($grossProfit / $totalSales) * 100 : 0;
            $profitColor = $grossProfit >= 0 ? 'color: #0a0;' : 'color: #f00;';

            return '<tr>'
                . '<td>' . htmlspecialchars($row['group_name']) . '</td>'
                . '<td>$' . number_format($totalSales, 2, ',', '.') . '</td>'
                . '<td>$' . number_format($totalCost, 2, ',', '.') . '</td>'
                . '<td style="font-weight: bold; ' . $profitColor . '">$' . number_format($grossProfit, 2, ',', '.') . '</td>'
                . '<td style="font-weight: bold; ' . $profitColor . '">' . number_format($profitMargin, 2, ',', '.') . '%</td>'
                . '</tr>';
        };

        $tableHtml = HTMLTableGenerator::generateTable('', '', $headers, $data, $rowRenderer);

        $html = HTMLTemplate::getTemplate($title, $fullSubtitle, $tableHtml);

        $converter = new HTMLToPDF($html);
        $converter->convertToPDF();
    }

    public static function profitabilityByCategory()
    {
        self::generateProfitabilityReport('category');
    }

    public static function profitabilityByManufacturer()
    {
        self::generateProfitabilityReport('manufacturer');
    }
}