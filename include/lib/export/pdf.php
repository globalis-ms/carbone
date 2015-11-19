<?php
//
// Export PDF
//

$legende=utf8_decode_mixed($legende);
$data=utf8_decode_mixed($data);
        
// Chargement de fpdf
require 'lib/fpdf/fpdf.php';
define('FPDF_FONTPATH', dirname(__FILE__).'/../fpdf/font/');

class PDF extends FPDF{
    //Chargement des données
    var $widths;
    var $aligns;

    function SetWidths($w) {
        // Tableau des largeurs de colonnes
        $this->widths=$w;
    }

    function SetAligns($a) {
        // Tableau des alignements de colonnes
        $this->aligns=$a;
    }

    function Row($data, $color=0) {

        //Calcule la hauteur de la ligne
        $nb=0;
        for($i=0;$i<count($data);$i++)  {
            $nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
        }
        $h=5*$nb;
        //Effectue un saut de page si nécessaire
        $this->CheckPageBreak($h);
        //Dessine les cellules
        for($i=0;$i<count($data);$i++) {
            $w=$this->widths[$i];
            $a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
            //Sauve la position courante
            $x=$this->GetX();
            $y=$this->GetY();
            //Dessine le cadre
            //$this->Rect($x,$y,$w,$h);

            // ajoute des "\n" si necessaire
            $nb_current = $this->NbLines($this->widths[$i],$data[$i]);
            if($nb !=$nb_current) {
                $data[$i].="\n";
                for($k=$nb_current; $k<$nb; $k++)
                    $data[$i].="\n";
            }
            //Imprime le texte
            $this->MultiCell($w,5,$data[$i], 'LRTB',$a, $color);
            //Repositionne à droite
            $this->SetXY($x+$w,$y);
        }
        //Va à la ligne
        $this->Ln($h);
    }

    function CheckPageBreak($h) {
        //Si la hauteur h provoque un débordement, saut de page manuel
        if($this->GetY()+$h>$this->PageBreakTrigger)
            $this->AddPage($this->CurOrientation);
    }

    function NbLines($w,$txt) {
        //Calcule le nombre de lignes qu'occupe un MultiCell de largeur w
        $cw=&$this->CurrentFont['cw'];
        if($w==0)
            $w=$this->w-$this->rMargin-$this->x;
        $wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
        $s=str_replace("\r",'',$txt);
        $nb=strlen($s);
        if($nb>0 and $s[$nb-1]=="\n")
            $nb--;
        $sep=-1;
        $i=0;
        $j=0;
        $l=0;
        $nl=1;
        while($i<$nb)
        {
            $c=$s[$i];
            if($c=="\n")
            {
                $i++;
                $sep=-1;
                $j=$i;
                $l=0;
                $nl++;
                continue;
            }
            if($c==' ')
                $sep=$i;
            $l+=$cw[$c];
            if($l>$wmax)
            {
                if($sep==-1)
                {
                    if($i==$j)
                        $i++;
                }
                else
                    $i=$sep+1;
                $sep=-1;
                $j=$i;
                $l=0;
                $nl++;
            }
            else
                $i++;
        }
        return $nl;
    }

    function AfficheLib($header, $couleur, $width) {
        //En-tête
        $couleur = str_replace('#', '', $couleur);
        $r_color = hexdec(substr($couleur, 0, 2));
        $g_color = hexdec(substr($couleur, 2, 2));
        $b_color = hexdec(substr($couleur, 4, 2));

        $this->SetFillColor($r_color,$g_color,$b_color);
        $this->SetTextColor(0);
        $x = $this->GetX();
        $y = $this->GetY();
        foreach($header as $k => $v){
            $this->SetXY($x,$y);
            $this->MultiCell($width,7,$header[$k]['label'], 'LTR', 'C', 1);
            $x += $width;
        }

    }

    function LoadData($header, $data) {
        $data2 = array();
        foreach($data as $k => $v) {
            $i =0;
            foreach($header as $k2 => $v2) {
                $data2[$k][$i] = $data[$k][$k2];
                $i++;
            }
        }
        return $data2;
    }
}

$header = array();
$i = 0;
foreach($legende as $k => $v){
    if($legende[$k]['export']) {
        $header[$legende[$k]['field']]['label'] = $legende[$k]['label'];
        $titre[$i] = $legende[$k]['label'];
        $i++;
    }
}

$width = array();
for($i=0;$i<sizeof($titre);$i++) {
    $width[] = 190/sizeof($titre);
}

$pdf = new PDF();
$pdf->Open();
$pdf->AddPage();
$pdf->SetFont('Arial','',9);

//Chargement des données
$data = $pdf->LoadData($header, $data);

$pdf->SetWidths($width);

// Affichage des libellés
$couleur = "eeeeee";
$couleur = str_replace('#', '', $couleur);
$r_color = hexdec(substr($couleur, 0, 2));
$g_color = hexdec(substr($couleur, 2, 2));
$b_color = hexdec(substr($couleur, 4, 2));
$pdf->SetFillColor($r_color,$g_color,$b_color);
$pdf->Row($titre, 1);

// Affichage des données
for($i=0;$i<sizeof($data);$i++) {
    $pdf->Row($data[$i], '');
}

//
// Envoi des entêtes et du flux
//

$pdf->Output($name.'_'.date('YmdHis').'.pdf', 'I');
?>