<?php


class ReportsController
{
    public static function actualInventoryValue()
    {
        include_once __DIR__ . '/../utils/HTMLToPDF.php';
        include_once __DIR__ . '/../models/ReportsModel.php';

        $localDate = date('d-m-Y');

        $reportsModel = new ReportsModel();

        $rows = $reportsModel->actualInventoryValue();
        $total = 0;

        // HTML Inicial
        $html = '
            <h1>Reporte Gerencial</h1>
            <hr/>
            <h3>Estado actual de Inventario - <span style="font-size: 12px; font-weight: normal;">' . $localDate . '</span></h3>
            <table>
        ';

        // Cabecera de la tabla
        $html .= '
            <tr>
                <th>Id / Código de barras</th>
                <th>Producto</th>
                <th>Precio</th>
                <th>IVA</th>
                <th>Stock</th>
                <th>Subtotal</th>
            </tr>
        ';

        // Cuerpo de la tabla
        foreach ($rows as $row) {
            $html .= '
                <tr>
                    <td>' . $row['id'] . '</td>
                    <td>' . $row['nombre'] . '</td>
                    <td>$' . number_format($row['precio'], 2, ',', '.') . '</td>
                    <td>' . $row['iva'] . '%</td>
                    <td>' . intval($row['stock']) . '</td>
                    <td>$' . number_format($row['subtotal'], 2, ',', '.') . '</td>
                </tr>
            ';

            $total += floatval($row['subtotal']);
        }
        if (count($rows) == 0) {
            $html .= '
                <tr>
                    <td colspan="3">No hay datos para mostrar...</td>
                </tr>
            ';
        }

        // Total
        $html .= '
            <tr class="total">
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td>Total:</td>
                <td>$' . number_format($total, 2, ',', '.') . '</td>
            </tr>
        ';

        // Cierre de la tabla
        $html .= '</table>';

        // Estilos
        $html .= '
            <style>
                table {
                    width: 100%;
                    border: 1px solid #000;
                    padding: 4px;
                }
                th{
                    background-color: #007a55;
                    border: 1px solid #000;
                    color: #fff;
                    font-weight: bold;
                }
                td {
                    border: 1px solid #000;
                    padding: 4px;
                }
                .total {
                    background-color: #007a55;
                    color: #fff;
                    font-weight: bold;
                }
            </style>
        ';


        $converter = new HTMLToPDF($html);
        $converter->convertToPDF();
    }

    public static function providersRanking()
    {
        include_once __DIR__ . '/../utils/HTMLToPDF.php';
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


        $localDate = ($startDate ? "Desde: $startDate" : '') . '-' . ($endDate ? "Hasta: $endDate" : '');

        $reportsModel = new ReportsModel();

        $rows = $reportsModel->providersRanking(from: $startDate, to: $endDate);

        // HTML Inicial
        $html = '
            <h1>Reporte Gerencial</h1>
            <hr/>
            <h3>Ranking de proveedores por volúmen de venta || <span style="font-size: 12px; font-weight: normal;">' . $localDate . '</span></h3>
            <table>
        ';

        // Cabecera de la tabla
        $html .= '
            <tr>
                <th>Documento de Identidad del proveedor</th>
                <th>Nombre del proveedor</th>
                <th>Productos comprados</th>
            </tr>
        ';

        // Cuerpo de la tabla
        foreach ($rows as $row) {
            $html .= '
                <tr>
                    <td>' . $row['id_proveedor'] . '</td>
                    <td>' . $row['nombre'] . '</td>
                    <td>' . $row['volumen'] . '</td>
                </tr>
            ';
        }
        if (count($rows) == 0) {
            $html .= '
                <tr>
                    <td colspan="3">No hay datos para mostrar...</td>
                </tr>
            ';
        }

        // Cierre de la tabla
        $html .= '</table>';

        // Estilos
        $html .= '
            <style>
                table {
                    width: 100%;
                    border: 1px solid #000;
                    padding: 4px;
                }
                th{
                    background-color: #007a55;
                    border: 1px solid #000;
                    color: #fff;
                    font-weight: bold;
                }
                td {
                    border: 1px solid #000;
                    padding: 4px;
                }
                .total {
                    background-color: #007a55;
                    color: #fff;
                    font-weight: bold;
                }
            </style>
        ';


        $converter = new HTMLToPDF($html);
        $converter->convertToPDF();
    }

    public static function ingressesEgresses()
    {
        include_once __DIR__ . '/../utils/HTMLToPDF.php';
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


        $localDate = ($startDate ? "Desde: $startDate" : '*') . ' / ' . ($endDate ? "Hasta: $endDate" : '*');

        $reportsModel = new ReportsModel();

        $rows = $reportsModel->ingressesEgresses(from: $startDate, to: $endDate);

        // HTML Inicial
        $html = '
            <h1>Reporte Gerencial</h1>
            <hr/>
            <h3>Ingresos y Egresos || <span style="font-size: 12px; font-weight: normal;">' . $localDate . '</span></h3>
            <table>
        ';

        // Cabecera de la tabla
        $html .= '
            <tr>
                <th>Fecha de movimiento</th>
                <th>Monto</th>
                <th>Tipo</th>
                <th>Método</th>
                <th>Referencia</th>
            </tr>
        ';

        // Cuerpo de la tabla
        foreach ($rows as $row) {

            if (intval($row['id_pedido']) == 0) {
                $html .= '
                    <tr>
                        <td>' . explode(' ', $row['fecha_cuota'])[0] . '</td>
                        <td style="color: #f00; font-weight:bold;"> -' . $row['monto'] . '</td>
                        <td> Egreso </td>
                        <td>' . $row['nombre_metodo'] . '</td>
                        <td>' . $row['nro_referencia'] . '</td>
                    </tr>
                ';
            } else {
                $html .= '
                    <tr>
                        <td>' . explode(' ', $row['fecha_cuota'])[0] . '</td>
                        <td style="color: #0a0; font-weight:bold;"> +' . $row['monto'] . '</td>
                        <td> Ingreso </td>
                        <td>' . $row['nombre_metodo'] . '</td>
                        <td>' . $row['nro_referencia'] . '</td>
                    </tr>
                ';
            }
        }
        if (count($rows) == 0) {
            $html .= '
                <tr>
                    <td colspan="3">No hay datos para mostrar...</td>
                </tr>
            ';
        }

        // Cierre de la tabla
        $html .= '</table>';

        // Estilos
        $html .= '
            <style>
                table {
                    width: 100%;
                    border: 1px solid #000;
                    padding: 4px;
                }
                th{
                    background-color: #007a55;
                    border: 1px solid #000;
                    color: #fff;
                    font-weight: bold;
                }
                td {
                    border: 1px solid #000;
                    padding: 4px;
                }
                .total {
                    background-color: #007a55;
                    color: #fff;
                    font-weight: bold;
                }
            </style>
        ';


        $converter = new HTMLToPDF($html);
        $converter->convertToPDF();
    }

    public static function waitingOrders()
    {
        include_once __DIR__ . '/../utils/HTMLToPDF.php';
        include_once __DIR__ . '/../models/ReportsModel.php';

        $localDate = date('d/m/Y');

        $reportsModel = new ReportsModel();

        $rows = $reportsModel->waitingOrders();

        // HTML Inicial
        $html = '
            <h1>Reporte Operacional</h1>
            <hr/>
            <h3>Listado de pedidos en espera || <span style="font-size: 12px; font-weight: normal;">' . $localDate . '</span></h3>
            <table>
        ';

        // Cabecera de la tabla
        $html .= '
            <tr>
                <th>Usuario del cliente</th>
                <th>Id. pedido</th>
                <th>Fecha</th>
                <th>Productos Totales</th>
                <th>Valor total</th>
            </tr>
        ';

        // Cuerpo de la tabla
        foreach ($rows as $row) {
            $html .= '
                    <tr>
                        <td>' . $row['nombre_usuario'] . '</td>
                        <td>' . $row['id_pedido'] . '</td>
                        <td>' . explode(' ', $row['fecha_pedido'])[0] . '</td>
                        <td>' . $row['total_productos'] . '</td>
                        <td>$' . number_format($row['valor'], 2, ',', '.') . '</td>
                    </tr>
                ';
        }
        if (count($rows) == 0) {
            $html .= '
                <tr>
                    <td colspan="5">No hay datos para mostrar...</td>
                </tr>
            ';
        }

        // Cierre de la tabla
        $html .= '</table>';

        // Estilos
        $html .= '
            <style>
                table {
                    width: 100%;
                    border: 1px solid #000;
                    padding: 4px;
                }
                th{
                    background-color: #007a55;
                    border: 1px solid #000;
                    color: #fff;
                    font-weight: bold;
                }
                td {
                    border: 1px solid #000;
                    padding: 4px;
                }
                .total {
                    background-color: #007a55;
                    color: #fff;
                    font-weight: bold;
                }
            </style>
        ';


        $converter = new HTMLToPDF($html);
        $converter->convertToPDF();
    }

    public static function inventoryEntry()
    {
        include_once __DIR__ . '/../utils/HTMLToPDF.php';
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


        $localDate = ($startDate ? "Desde: $startDate" : '*') . ' / ' . ($endDate ? "Hasta: $endDate" : '*');

        $reportsModel = new ReportsModel();

        $rows = $reportsModel->inventoryEntry(from: $startDate, to: $endDate);

        // HTML Inicial
        $html = '
            <h1>Reporte Gerencial</h1>
            <hr/>
            <h3>Entradas de inventario || <span style="font-size: 12px; font-weight: normal;">' . $localDate . '</span></h3>
            <table>
        ';

        // Cabecera de la tabla
        $html .= '
            <tr>
                <th>ID. del Producto</th>
                <th>Producto</th>
                <th>Cantidad Ingresada</th>
                <th>ID.  de Compra</th>
                <th>Fecha de Compra</th>
                <th>Proveedor</th>
            </tr>
        ';

        // Cuerpo de la tabla
        foreach ($rows as $row) {
            $html .= '
                    <tr>
                        <td>' . $row['id_producto'] . '</td>
                        <td>' . $row['nombre_producto'] . '</td>
                        <td style="font-weight: bold;">' . $row['cantidad_producto'] . '</td>
                        <td>' . $row['id_compra'] . '</td>
                        <td>' . explode(' ', $row['fecha_compra'])[0] . '</td>
                        <td>' . $row['nombre'] . '</td>
                    </tr>
                ';
        }
        if (count($rows) == 0) {
            $html .= '
                <tr>
                    <td colspan="3">No hay datos para mostrar...</td>
                </tr>
            ';
        }

        // Cierre de la tabla
        $html .= '</table>';

        // Estilos
        $html .= '
            <style>
                table {
                    width: 100%;
                    border: 1px solid #000;
                    padding: 4px;
                }
                th{
                    background-color: #007a55;
                    border: 1px solid #000;
                    color: #fff;
                    font-weight: bold;
                }
                td {
                    border: 1px solid #000;
                    padding: 4px;
                }
                .total {
                    background-color: #007a55;
                    color: #fff;
                    font-weight: bold;
                }
            </style>
        ';


        $converter = new HTMLToPDF($html);
        $converter->convertToPDF();
    }

    public static function inventoryExit()
    {
        include_once __DIR__ . '/../utils/HTMLToPDF.php';
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


        $localDate = ($startDate ? "Desde: $startDate" : '*') . ' / ' . ($endDate ? "Hasta: $endDate" : '*');

        $reportsModel = new ReportsModel();

        $rows = $reportsModel->inventoryExit(from: $startDate, to: $endDate);

        // HTML Inicial
        $html = '
            <h1>Reporte Gerencial</h1>
            <hr/>
            <h3>Salidas de inventario || <span style="font-size: 12px; font-weight: normal;">' . $localDate . '</span></h3>
            <table>
        ';

        // Cabecera de la tabla
        $html .= '
            <tr>
                <th>ID. del Producto</th>
                <th>Producto</th>
                <th>Cantidad Egresada</th>
                <th>ID. de Pedido</th>
                <th>Fecha de Pedido</th>
                <th>Usuario del Cliente</th>
            </tr>
        ';

        // Cuerpo de la tabla
        foreach ($rows as $row) {
            $html .= '
                    <tr>
                        <td>' . $row['id_producto'] . '</td>
                        <td>' . $row['nombre'] . '</td>
                        <td style="font-weight: bold;">' . $row['cantidad_producto'] . '</td>
                        <td>' . $row['id_pedido'] . '</td>
                        <td>' . explode(' ', $row['fecha_pedido'])[0] . '</td>
                        <td>' . $row['nombre_usuario'] . '</td>
                    </tr>
                ';
        }
        if (count($rows) == 0) {
            $html .= '
                <tr>
                    <td colspan="3">No hay datos para mostrar...</td>
                </tr>
            ';
        }

        // Cierre de la tabla
        $html .= '</table>';

        // Estilos
        $html .= '
            <style>
                table {
                    width: 100%;
                    border: 1px solid #000;
                    padding: 4px;
                }
                th{
                    background-color: #007a55;
                    border: 1px solid #000;
                    color: #fff;
                    font-weight: bold;
                }
                td {
                    border: 1px solid #000;
                    padding: 4px;
                }
                .total {
                    background-color: #007a55;
                    color: #fff;
                    font-weight: bold;
                }
            </style>
        ';


        $converter = new HTMLToPDF($html);
        $converter->convertToPDF();
    }
}