<?php


class HTMLToPDF
{
    private string $html;
    public function __construct(string $html)
    {
        $this->html = $html;
    }

    public function convertToPDF()
    {
        include_once __DIR__ . '/../TCPDF-main/tcpdf.php';

        // create new PDF document
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Provisys');
        $pdf->SetTitle('Reporte');
        $pdf->SetSubject('Reporte');
        $pdf->SetKeywords('...');

        // set default header data
        $pdf->SetHeaderData(
            "",
            0,
            "Reporte - Provisys",
            "Sistema de Gestión de Pedidos"
        );

        // set header and footer fonts
        $pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        // set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // set some language-dependent strings (optional)
        if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
            require_once(dirname(__FILE__) . '/lang/eng.php');
            $pdf->setLanguageArray($l);
        }

        // ---------------------------------------------------------

        // set font
        $pdf->SetFont('dejavusans', '', 10);

        // add a page
        $pdf->AddPage();

        // output the HTML content
        $pdf->writeHTML($this->html, true, false, true, false, '');


        // reset pointer to the last page
        $pdf->lastPage();

        //Close and output PDF document
        $pdf->Output('reporte-' . uniqid() . '.pdf', 'I');
    }
}