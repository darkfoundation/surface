<?php

#----DEFINE FUNCTIONS----
function z($x, $y) { $r=sqrt($x*$x+$y*$y); $z=eval($_GET["plot"].'; return $z;'); return $z; }
function u($x, $y, $wd) { $sc=$wd/640; return $wd/2+$x*$sc-$y*$sc/2; }
function v($x, $y, $ht) { $sc=$ht/480; return $ht/2-z($x, $y)*$sc-$y*$sc/2; }

#----INITIALIZE----
$f=$_GET["res"]; $o=$_GET["lite"]-.5; $p=-$_GET["lite"]; $a=$_GET["wire"]; $b=$_GET["pic"];
$k=$f*$f; $q=1; $w=sqrt($o*$o+$p*$p+$q*$q); $pal=array();
$dims=explode('x', $b); $wd=$dims[0]; $ht=$dims[1];
$im=imagecreatetruecolor($wd, $ht); imageantialias($im, TRUE);
for ($clr=0; $clr<256; $clr++) { $pal[$clr]=imagecolorallocate($im, $clr, $clr, $clr); }
imagefill($im, 0, 0, $pal[0]);

#----CALCULATE ALL POINTS----
for ($y=200; $y>=-200; $y-=$f) {
	for ($x=200; $x>=-200; $x-=$f) {
		$u[$x][$y]=u($x, $y, $wd); $v[$x][$y]=v($x, $y, $ht); $z[$x][$y]=z($x, $y);
	}
}

#----PLOT ALL SURFACES----
for ($y=200; $y>-200; $y-=$f) {
	for ($x=200; $x>-200; $x-=$f) {
		$i=$f*($z[$x-$f][$y]-$z[$x][$y]); $j=$f*($z[$x][$y-$f]-$z[$x][$y]);
		$l=($i*$o+$j*$p+$k*$q)/sqrt($i*$i+$j*$j+$k*$k)/$w; $c=($l>=0)?(round(253*$l+1)):(1);
		$gridpts=array($u[$x][$y], $v[$x][$y], $u[$x-$f][$y], $v[$x-$f][$y], $u[$x-$f][$y-$f], $v[$x-$f][$y-$f], $u[$x][$y-$f], $v[$x][$y-$f]);
		if ($a==1) { imagepolygon($im, $gridpts, 4, $pal[$c]); }
		else { imagefilledpolygon($im, $gridpts, 4, $pal[$c]); }
	}
}

#----OUTPUT IMAGE & END----
$type_out="./consolab.ttf"; $funct_out=str_replace("$", "", $_GET["plot"]);
$txt_pos=imagettfbbox(13, 0, $type_out, $funct_out); $txt_ctr_x=$wd/2-($txt_pos[2]-$txt_pos[0])/2;
imagettftext($im, 13, 0, $txt_ctr_x, $ht-10, $pal[255], $type_out, $funct_out);
header("Content-type: image/png"); imagepng($im); imagedestroy($im);

