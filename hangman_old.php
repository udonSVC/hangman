<?php
//
//
//-------------------------------------------------------
//-- HANGMAN v1.1                                      --
//-- PHP game by Giovanni Crisci                       --
//-- http://giovi.supereva.it                          --
//-- Images taken from ASP code written by             --
//-- ASP101 http://www.asp101.com                      --
//-------------------------------------------------------
//
//

$file_parole = 'parole.txt';   //file that contains all the words

//functions and variables
$alfabeto = array ('A' => 'a','B' => 'b','C' => 'c','D' => 'd','E' => 'e',
 'F' => 'f','G' => 'g','H' => 'h','I' => 'i','J' => 'j','K' => 'k','L' => 'l',
 'M' => 'm','N' => 'n','O' => 'o','P' => 'p','Q' => 'q','R' => 'r','S' => 's',
 'T' => 't','U' => 'u','V' => 'v','W' => 'w','X' => 'x','Y' => 'y','Z' => 'z');
$paperino = session_name ("hangman");
session_register ("gchangman");

//choose a random word from the file
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

if ((isset($letter) and isset($gchangman))) {
 $variab = explode ('/', $gchangman);
 $indovina = $variab[0];
 $scelte = $variab[1];
 $tentativi = $variab[2];
 $checklet = TrovaLettera ($letter, $indovina, $scelte, $alfabeto);
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

$gchangman = ($indovina.'/'.$scelte.'/'.$tentativi);

if ($tentativi>6) $tentativi=6;

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
<HEAD>
<TITLE> Giovanni Crisci - Hangman </TITLE>
</HEAD>
<BODY>
<?php

print ('<IMG SRC="images/hang_'.($tentativi+1).'.gif" BORDER=0 WIDTH=100 HEIGHT=100 ALT="Miss '.$tentativi.'/6"><BR><BR>');
$caratteri = preg_split('//', $indovinata, -1, PREG_SPLIT_NO_EMPTY);
foreach ($caratteri as $lettalf) {
 $lettalf = strtr ($lettalf, $alfabeto);
 if ($lettalf==' ') {
  print ('<IMG SRC="images/lb_~.gif" BORDER=0 WIDTH=20 HEIGHT=20 ALT="">');
 }
 else {
  print ('<IMG SRC="images/lb_'.$lettalf.'.gif" BORDER=0 WIDTH=20 HEIGHT=20 ALT="'.$lettalf.'">');
 }
 print ('<IMG SRC="images/lb_~.gif" BORDER=0 WIDTH=20 HEIGHT=20 ALT="">');
}
print ('<BR><BR>');

if ($indovina!=$indovinata) {
 if ($tentativi>=6) {
  print ('Sorry, you hanged yourself. The word you had to guess is: '.$indovina);
 }
 else {
  $scelt = preg_split('//', $scelte, -1, PREG_SPLIT_NO_EMPTY);
  print ('<BR>');
  foreach ($alfabeto as $lettalf) {
   $contrl = false;
   foreach ($scelt as $lett) {
    if (!strcasecmp ($lettalf, $lett)) {
     $contrl = true;
    }
   }
   if ($contrl) {
    print ('<IMG SRC="images/lr_'.$lettalf.'.gif" BORDER=0 WIDTH=20 HEIGHT=20 ALT="'.$lettalf.'">');
   }
   else {
    print ('<A HREF="'.$PHP_SELF.'?letter='.$lettalf.'&'.SID.'"><IMG SRC="images/lb_'.$lettalf.'.gif" BORDER=0 WIDTH=20 HEIGHT=20 ALT="'.$lettalf.'"></A>');
   }
   if ($lettalf=='m') {
    print ('<BR>');
   }
  }
 }
}
else {
 print ('Congratulations! You guessed the word.');
}

?>
<BR>
<P><A HREF=<?php print ('"'.$PHP_SELF.'"'); ?>>New Game</A></P>
</BODY>
</HTML>
