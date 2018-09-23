<?php
session_start();

$ntseq = $_POST["ntseq"];
$name = $_POST["name"];

//format an input
if (preg_match('/[^AaCcGgTtUu|0-9|" *"|\r\n]/', $ntseq)):
    die('Wrong sequence format.');
else:
    $seq_lower = strtolower($ntseq);
    $dna_rna = str_replace('t', 'u', $seq_lower);
    $new_seq = preg_replace('/[^a-z]/', "", $dna_rna);
endif;

//split a sequence to create codons
$codons = str_split($new_seq, 3);

//translate codons to amino acids (aa)
echo 'Amino acids sequence:<br><br>';

$aa = [
    'uuu' => 'F',
    'uuc' => 'F',
    'uua' => 'L',
    'uug' => 'L',
    'cuu' => 'L',
    'cuc' => 'L',
    'cua' => 'L',
    'cug' => 'L',
    'auu' => 'I',
    'auc' => 'I',
    'aua' => 'I',
    'aug' => 'M',
    'guu' => 'V',
    'guc' => 'V',
    'gua' => 'V',
    'gug' => 'V',
    'ucu' => 'S',
    'ucc' => 'S',
    'uca' => 'S',
    'ucg' => 'S',
    'ccu' => 'P',
    'ccc' => 'P',
    'cca' => 'P',
    'ccg' => 'P',
    'acu' => 'T',
    'acc' => 'T',
    'aca' => 'T',
    'acg' => 'T',
    'gcu' => 'A',
    'gcc' => 'A',
    'gca' => 'A',
    'gcg' => 'A',
    'uau' => 'Y',
    'uac' => 'Y',
    'uaa' => 'stop',
    'uag' => 'stop',
    'cau' => 'H',
    'cac' => 'H',
    'caa' => 'Q',
    'cag' => 'Q',
    'aau' => 'N',
    'aac' => 'N',
    'aaa' => 'K',
    'aag' => 'K',
    'gau' => 'D',
    'gac' => 'D',
    'gaa' => 'E',
    'gag' => 'E',
    'ugu' => 'C',
    'ugc' => 'C',
    'uga' => 'stop',
    'ugg' => 'W',
    'cgu' => 'R',
    'cgc' => 'R',
    'cga' => 'R',
    'cgg' => 'R',
    'agu' => 'S',
    'agc' => 'S',
    'aga' => 'R',
    'agg' => 'R',
    'ggu' => 'G',
    'ggc' => 'G',
    'gga' => 'G',
    'ggg' => 'G',
];

$aa_seq = [];

echo '<p style="word-break: break-all;width: 625px;">';
foreach ($codons as $k1 => $v1):
    foreach ($aa as $k2 => $v2):
        if ($v1 === $k2):
            array_push($aa_seq, $v2);
            echo $v2;
        endif;
    endforeach;
endforeach;
echo '</p>';

$seq = implode('', $aa_seq);

//nucleotides left
$last = strlen(end($codons));

if($last === 1):
    echo '<br><br>Note: ' . $last . ' nucleotide left';
elseif($last === 2):
    echo '<br><br>Note: ' . $last . ' nucleotides left';
endif;

//calculate number of amino acids
$stop = count(array_keys($aa_seq, 'stop'));
if ($stop > 1): echo '<br><br>Note: The sequence includes more than one stop codons!';
endif;

$count_seq = count($aa_seq) - $stop;
echo '<br><br>Number of amino acids: ' . $count_seq;

//calculate molecular weight of a peptide
$mv = [
    'A' => 89,
    'R' => 174,
    'N' => 132,
    'D' => 133,
    'C' => 121,
    'E' => 146,
    'Q' => 147,
    'G' => 75,
    'H' => 155,
    'I' => 131,
    'L' => 131,
    'K' => 146,
    'M' => 149,
    'F' => 165,
    'P' => 115,
    'S' => 105,
    'T' => 119,
    'W' => 204,
    'Y' => 181,
    'V' => 117,
];

$mv_seq = [];

foreach ($aa_seq as $k3 => $v3):
    foreach ($mv as $k4 => $v4):
        if ($v3 === $k4):
            array_push($mv_seq, $v4);
        endif;
    endforeach;
endforeach;

$mv_value = (array_sum($mv_seq)) / 1000;
echo '<br>Molecular weight: ' . $mv_value . ' kDa';

//calculate amino acids by properties - basic
$k = count(array_keys($aa_seq, 'K'));
$r = count(array_keys($aa_seq, 'R'));
$h = count(array_keys($aa_seq, 'H'));

$basic = $k + $r + $h;
$contr_1 = round($basic * 100 / $count_seq, 2);
echo '<br><br>Basic amino acids: ' . $basic . ' (' . $contr_1 . '%)';

//calculate amino acids by properties - acidic
$d = count(array_keys($aa_seq, 'D'));
$e = count(array_keys($aa_seq, 'E'));

$acidic = $d + $e;
$contr_2 = round($acidic * 100 / $count_seq, 2);
echo '<br>Acidic amino acids: ' . $acidic . ' (' . $contr_2 . '%)';

//calculate amino acids by properties - hydrophilic
$c = count(array_keys($aa_seq, 'C'));
$s = count(array_keys($aa_seq, 'S'));
$t = count(array_keys($aa_seq, 'T'));
$q = count(array_keys($aa_seq, 'Q'));
$n = count(array_keys($aa_seq, 'N'));
$y = count(array_keys($aa_seq, 'Y'));

$hydrophilic = $c + $s + $t + $q + $n + $y;
$contr_3 = round($hydrophilic * 100 / $count_seq, 2);
echo '<br>Hydrophilic amino acids: ' . $hydrophilic . ' (' . $contr_3 . '%)';

//calculate amino acids by properties - hydrophobic
$g = count(array_keys($aa_seq, 'G'));
$a = count(array_keys($aa_seq, 'A'));
$v = count(array_keys($aa_seq, 'V'));
$l = count(array_keys($aa_seq, 'L'));
$i = count(array_keys($aa_seq, 'I'));
$m = count(array_keys($aa_seq, 'M'));
$f = count(array_keys($aa_seq, 'F'));
$p = count(array_keys($aa_seq, 'P'));
$w = count(array_keys($aa_seq, 'W'));

$hydrophobic = $g + $a + $v + $l + $i + $m + $f + $p + $w;
$contr_4 = round($hydrophobic * 100 / $count_seq, 2);
echo '<br>Hydrophobic amino acids: ' . $hydrophobic . ' (' . $contr_4 . '%)';

$_SESSION['name'] = $name;
$_SESSION['seq'] = $seq;
$_SESSION['aa number'] = $count_seq;
$_SESSION['weight'] = $mv_value;
$_SESSION['basic'] = $basic;
$_SESSION['acidic'] = $acidic;
$_SESSION['hydrophilic'] = $hydrophilic;
$_SESSION['hydrophobic'] = $hydrophobic;
$_SESSION['contr 1'] = $contr_1;
$_SESSION['contr 2'] = $contr_2;
$_SESSION['contr 3'] = $contr_3;
$_SESSION['contr 4'] = $contr_4;
$_SESSION['stop'] = $stop;