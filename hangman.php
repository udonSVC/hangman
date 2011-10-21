<?php
//
//
//-------------------------------------------------------
//-- HANGMAN                                           --
//-- PHP GAME Por Valdir Coxev                         --
//-------------------------------------------------------
//
//

$file_parole = 'parole.txt';   //arquivo que contém todas as palavras
$template = 'base.html';       //arquivo de modelo que será analisado

//funções e variáveis
$alfabeto = array ('A' => 'a','B' => 'b','C' => 'c','D' => 'd','E' => 'e',
 'F' => 'f','G' => 'g','H' => 'h','I' => 'i','J' => 'j','K' => 'k','L' => 'l',
 'M' => 'm','N' => 'n','O' => 'o','P' => 'p','Q' => 'q','R' => 'r','S' => 's',
 'T' => 't','U' => 'u','V' => 'v','W' => 'w','X' => 'x','Y' => 'y','Z' => 'z');
session_start ();

//escolher uma palavra aleatória a partir do arquivo
function ScegliParola ($file_parole) {
 $content = file ($file_parole);
 $numero_parole = (count ($content)-1);
 $posizione_parola = rand (0, ($numero_parole));
 $linea = $content[$posizione_parola];
 $parola = rtrim ($linea);
 return ($parola);
}

// changes all the unguessed letters with _
function CalcolaIndovinata ($parola, $escludi, $alfabeto) {
 $alfabeto = array_flip ($alfabeto);
 $escluse = strtr ($escludi, $alfabeto);
 $escluse = '['.$escluse.']';
 $escluse = ereg_replace ($escluse, '', '[ABCDEFGHIJKLMNOPQRSTUVWXYZ]');
 $indovinata = ereg_replace ($escluse, '_', $parola);
 return $indovinata;
}

// seeks the chosen letter in the word
function TrovaLettera ($lettera, $parola, $scelte, $alfabeto) {
 $alfabeto = array_flip ($alfabeto);
 $lettera = substr ($lettera, 0, 1);
 $lettera = strtr ($lettera, $alfabeto);
 $scelte.= $lettera;
 if (ereg ($lettera, $parola)) {
  $controllo = true;
 }
 else {
  $controllo = false;
 }
 $risposta = array ($scelte, $controllo);
 return $risposta;
}

// and this is the programme!

if (isset($_GET['letter']) and isset($_SESSION['gchangman'])) {
 $variab = explode ('/', $_SESSION['gchangman']);
 $indovina = $variab[0];
 $scelte = $variab[1];
 $tentativi = $variab[2];
 $checklet = TrovaLettera ($_GET['letter'], $indovina, $scelte, $alfabeto);
 $scelte = $checklet[0];
 if (!$checklet[1]) {
  $tentativi = $tentativi +1;
 }
 $indovinata = CalcolaIndovinata ($indovina, $scelte, $alfabeto);
}
else {
 $tentativi=0;
 $scelte = ('_');
 $indovina = ScegliParola ($file_parole);
 $indovinata = CalcolaIndovinata ($indovina, $scelte, $alfabeto);
}

$_SESSION['gchangman'] = ($indovina.'/'.$scelte.'/'.$tentativi);

if ($tentativi>6) $tentativi=6;

$pagina = file($template);
$totrighe = count($pagina);
$i = 0;
while (rtrim($pagina[$i])!="<!--HANGMAN-->") {
 print $pagina[$i];
 $i++;
}

print ('<p><img src="images/hang_'.($tentativi+1).'.gif" style="border:0;width:100px;height:100px" alt="Miss '.$tentativi.'/6" /></p>');
echo ("\n<p>\n");
$caratteri = preg_split('//', $indovinata, -1, PREG_SPLIT_NO_EMPTY);
foreach ($caratteri as $lettalf) {
 $lettalf = strtr ($lettalf, $alfabeto);
 if ($lettalf==' ') {
  print (' <img src="images/lb_~.gif" style="border:0;width:20px;height:20px" alt="" />');
 }
 else {
  print (' <img src="images/lb_'.$lettalf.'.gif" style="border:0;width:20px;height:20px" alt="'.$lettalf.'" />');
 }
 echo ("\n");
 print (' <img src="images/lb_~.gif" style="border:0;width:20px;height:20px" alt="" />');
 echo ("\n");
}
print ('</p>');

if ($indovina!=$indovinata) {
 if ($tentativi>=6) {
  echo ("\n<p>Sorry, you hanged yourself. The word you had to guess was: ".$indovina."</p>\n");
 }
 else {
  $scelt = preg_split('//', $scelte, -1, PREG_SPLIT_NO_EMPTY);
  echo ("\n<p>\n");
  foreach ($alfabeto as $lettalf) {
   $contrl = false;
   foreach ($scelt as $lett) {
    if (!strcasecmp ($lettalf, $lett)) {
     $contrl = true;
    }
   }
   if ($contrl) {
    print (' <img src="images/lr_'.$lettalf.'.gif" style="border:0;width:20px;height:20px" alt="'.$lettalf.'" />');
   }
   else {
    print (' <a href="'.$_SERVER['PHP_SELF'].'?letter='.$lettalf.'"><img src="images/lb_'.$lettalf.'.gif" style="border:0;width:20px;height:20px" alt="'.$lettalf.'" /></a>');
   }
   if ($lettalf=='m') echo ("\n <br />");
   echo ("\n");
  }
  echo ("</p>\n"); 
 }
}
else {
 echo ("\n<p>Parabéns! Você adivinhou a palavra.</p>\n");
}

print ('<p><a href="'.$_SERVER['PHP_SELF'].'">Iniciar Jogo</a></p>');
$i++;
while ($i<$totrighe) {
 print $pagina[$i];
 $i++;
}
