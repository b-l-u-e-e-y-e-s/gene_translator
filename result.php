<?php
session_start();

$ntseq = $_POST["ntseq"];
$name = $_POST["name"];

/* input validation:
 * not valid if empty
 * not valid if other letters than nucleotides
 * not valid if less than three nucleotides
 * numbers and spaces allowed */

if ((empty($ntseq)) || (empty($name)) || (preg_match('/[^AaCcGgTtUu0-9 \n\r]/', $ntseq)) || (!preg_match('/[AaCcGgTtUu]{3,}/', $ntseq))) {
    die('Wrong sequence format.');
} else {
    $seq_lower = strtolower($ntseq);
    $dna_rna = str_replace('t', 'u', $seq_lower);
    $new_seq = preg_replace('/[^a-z]/', "", $dna_rna);
    $codons = str_split($new_seq, 3);
}

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

$basic = ['K', 'R', 'H'];
$acidic = ['D', 'E'];
$hydrophilic = ['C', 'S', 'T', 'Q', 'N', 'Y'];
$hydrophobic = ['G', 'A', 'V', 'L', 'I', 'M', 'F', 'P', 'W'];

function compareArrays($arr1, $arr2) {
    foreach ($arr1 as $v) {
        if (isset($arr2[$v])) {
            $arr3[] = $arr2[$v];
        }
    }
    return $arr3;
}

function countSeq($arr1, $arr2) {
    foreach ($arr1 as $v) {
        $keys[] = count(array_keys($arr2, $v));
        $sum_keys = array_sum($keys);
    }
    return $sum_keys;
}

function contribution($total, $arr1, $arr2) {
    $a = countSeq($arr1, $arr2);
    $round = round($a * 100 / $total, 2);
    return $round;
}

//amino acids sequence
$aa_seq = compareArrays($codons, $aa);
$seq = implode('', $aa_seq);

//number of amino acids
$last = strlen(end($codons));
$stop = count(array_keys($aa_seq, 'stop'));
$count_seq = count($aa_seq) - $stop;

//molecular weight
$mv_seq = compareArrays($aa_seq, $mv);
$mv_value = (array_sum($mv_seq)) / 1000;

//sessions: data needed to pdf report
$_SESSION['name'] = $name;
$_SESSION['seq'] = $seq;
$_SESSION['aa number'] = $count_seq;
$_SESSION['weight'] = $mv_value;
$_SESSION['basic'] = countSeq($basic, $aa_seq);
$_SESSION['acidic'] = countSeq($acidic, $aa_seq);
$_SESSION['hydrophilic'] = countSeq($hydrophilic, $aa_seq);
$_SESSION['hydrophobic'] = countSeq($hydrophobic, $aa_seq);
$_SESSION['contr 1'] = contribution($count_seq, $basic, $aa_seq);
$_SESSION['contr 2'] = contribution($count_seq, $acidic, $aa_seq);
$_SESSION['contr 3'] = contribution($count_seq, $hydrophilic, $aa_seq);
$_SESSION['contr 4'] = contribution($count_seq, $hydrophobic, $aa_seq);
$_SESSION['stop'] = $stop;
?>

<html>
    <head>
        <title>DNA Code</title>
    </head>
    <body>
        <p>
            Amino acids sequence:<br><br>
        </p>
        <p style="word-break:break-all; width:625px;">
            <?php echo $seq ?>
        </p>
        <p>
            <?php
            if ($last === 1):
                echo 'Note: ' . $last . ' nucleotide left<br>';
            elseif ($last === 2):
                echo 'Note: ' . $last . ' nucleotides left<br>';
            endif
            ?>
            <?php 
            if ($stop > 1):
                echo 'Note: The sequence includes more than one stop codons!';
            endif
            ?>  
        </p>
        <p>
            Number of amino acids: <?php echo $count_seq ?><br>
            Molecular weight: <?php echo $mv_value ?> kDa<br><br>
            Basic amino acids: <?php echo countSeq($basic, $aa_seq) . ' (' . contribution($count_seq, $basic, $aa_seq) . '%)' ?><br>
            Acidic amino acids: <?php echo countSeq($acidic, $aa_seq) . ' (' . contribution($count_seq, $acidic, $aa_seq) . '%)' ?><br>
            Hydrophilic amino acids: <?php echo countSeq($hydrophilic, $aa_seq) . ' (' . contribution($count_seq, $hydrophilic, $aa_seq) . '%)' ?><br>
            Hydrophobic amino acids: <?php echo countSeq($hydrophobic, $aa_seq) . ' (' . contribution($count_seq, $hydrophobic, $aa_seq) . '%)' ?><br>
        </p>
        <a href="pdf.php" target="_blank">PDF Report</a>
    </body>
</html>