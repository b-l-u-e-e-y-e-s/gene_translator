<?php
session_start();
require('library/FPDF/fpdf.php');

//title
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Helvetica', '', 20);
$pdf->MultiCell(190, 10, $_SESSION['name']);
$pdf->Ln(30);

//sequence
$pdf->SetFont('Helvetica', 'B', 12);
$pdf->Cell(190, 5, 'Amino acid sequence:');
$pdf->Ln(10);
$pdf->SetFont('Helvetica', '', 10);
$pdf->MultiCell(190, 6, $_SESSION['seq']);
$pdf->Ln(10);

//more stops
if($_SESSION['stop'] > 1):
    $pdf->SetFont('Helvetica', '', 12);
    $pdf->Cell(190, 6, 'Note: The sequence includes more than one stop codons!');
    $pdf->Ln(20);
endif;

//number and weight
$pdf->SetFont('Helvetica', '', 12);
$pdf->Cell(60, 6, 'Number of amino acids:');
$pdf->Cell(130, 6, $_SESSION['aa number'], 0, 1);
$pdf->Cell(60, 6, 'Molecular weight:');
$pdf->Cell(130, 6, $_SESSION['weight'] . ' kDa', 0, 1);
$pdf->Ln(10);

//properties
$pdf->Cell(60, 6, 'Basic amino acids:');
$pdf->Cell(130, 6, $_SESSION['basic'] . ' (' . $_SESSION['contr 1'] . '%)', 0, 1);
$pdf->Cell(60, 6, 'Acidic amino acids:');
$pdf->Cell(130, 6, $_SESSION['acidic'] . ' (' . $_SESSION['contr 2'] . '%)', 0, 1);
$pdf->Cell(60, 6, 'Hydrophilic amino acids:');
$pdf->Cell(130, 6, $_SESSION['hydrophilic'] . ' (' . $_SESSION['contr 3'] . '%)', 0, 1);
$pdf->Cell(60, 6, 'Hydrophobic amino acids:');
$pdf->Cell(130, 6, $_SESSION['hydrophobic'] . ' (' . $_SESSION['contr 4'] . '%)', 0, 1);

//output
$pdf->Output();
