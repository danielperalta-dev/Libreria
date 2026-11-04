<?php
// Generador de PDF mínimo (sin librerías externas)
class MiniPDF {
    private $c = '';
    private function esc($s) {
        $s = @iconv('UTF-8', 'windows-1252//TRANSLIT', $s);
        return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $s);
    }
    function text($x, $y, $s, $size = 11, $bold = false) {
        $this->c .= sprintf("BT /%s %d Tf %.2f %.2f Td (%s) Tj ET\n", $bold ? 'F2' : 'F1', $size, $x, $y, $this->esc($s));
    }
    function line($x1, $y1, $x2, $y2) { $this->c .= sprintf("%.2f %.2f m %.2f %.2f l S\n", $x1, $y1, $x2, $y2); }
    function output($nombre) {
        $o = [];
        $o[1] = '<< /Type /Catalog /Pages 2 0 R >>';
        $o[2] = '<< /Type /Pages /Kids [3 0 R] /Count 1 >>';
        $o[3] = '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Resources << /Font << /F1 4 0 R /F2 5 0 R >> >> /Contents 6 0 R >>';
        $o[4] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica /Encoding /WinAnsiEncoding >>';
        $o[5] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold /Encoding /WinAnsiEncoding >>';
        $o[6] = '<< /Length '.strlen($this->c)." >>\nstream\n".$this->c.'endstream';
        $pdf = "%PDF-1.4\n"; $off = [];
        foreach ($o as $i => $b) { $off[$i] = strlen($pdf); $pdf .= "$i 0 obj\n$b\nendobj\n"; }
        $x = strlen($pdf);
        $pdf .= "xref\n0 ".(count($o) + 1)."\n0000000000 65535 f \n";
        foreach ($off as $f) $pdf .= sprintf("%010d 00000 n \n", $f);
        $pdf .= "trailer\n<< /Size ".(count($o) + 1)." /Root 1 0 R >>\nstartxref\n$x\n%%EOF";
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="'.$nombre.'"');
        header('Content-Length: '.strlen($pdf));
        echo $pdf;
    }
}
