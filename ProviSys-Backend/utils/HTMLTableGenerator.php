<?php

class HTMLTableGenerator
{
        public static function generateTable(string $title, string $fullSubtitle, array $headers, array $data, callable $rowRenderer, ?array $totalRow = null): string
    {
        // HTML Inicial
        $html = "
            <h1>{$title}</h1>
            <hr/>
            <h3>{$fullSubtitle}</h3>
            <table>
        ";

        // Cabecera de la tabla
        $html .= '<tr>';
        foreach ($headers as $header) {
            $html .= "<th>{$header}</th>";
        }
        $html .= '</tr>';

        // Cuerpo de la tabla
        if (empty($data)) {
            $html .= '<tr><td colspan="' . count($headers) . '">No hay datos para mostrar...</td></tr>';
        } else {
            foreach ($data as $row) {
                $html .= $rowRenderer($row);
            }
        }

        // Total
        if ($totalRow) {
            $html .= '<tr class="total">';
            foreach ($totalRow as $cell) {
                $html .= "<td>{$cell}</td>";
            }
            $html .= '</tr>';
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

        return $html;
    }
}
