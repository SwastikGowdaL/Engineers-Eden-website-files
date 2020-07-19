<?php

@error_reporting(E_ERROR | E_PARSE);

/************* WordSearchPuzzle V1.0 *******************/
/*
Released by AwesomePHP.com, under the GPL License, a
copy of it should be attached to the zip file, or
you can view it on http://AwesomePHP.com/gpl.txt
*/
/************* WordSearchPuzzle V1.0 *******************/	

/* Start Manual Configuration */

// List of dictionaries
$file_list = array('12dicts5','ispell.0','ispell.1','jargon','scowl');

// Dictionary word separator
$line_sep = "\n";

// Dictionary Directory
$dictionary_dir = 'extras/';

// Characters to display between words
$array_characters = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');

// Puzzle length x height (in 5x)
$PUZ_L = '10';
$PUZ_W = '15';

// Or User submitted
if($_POST['PUZ_L'] != NULL){
	$PUZ_L = number_format($_POST['PUZ_L']);
	$PUZ_W = number_format($_POST['PUZ_W']);
}

// Maximum word length
$PUZ_ML = min('8',$PUZ_L,$PUZ_W);

// Complexity leve 1-10 (10 being the hardest)
/*
	Complexity level increases as the number or
	words decrease. Words are calculated as so:
		(length x width) / (4*level) - 1
	however,the result count must be < $PUZ_L/W
*/	
$PUZ_C = 1;

/***************************** End Manual Configuration **********************************/

// Check for submition
if($_POST['my_words'] != NULL){
	// My Words
	$my_words = explode("\n",$_POST['my_words']);
	foreach($my_words as $sub => $word){ $my_words[$sub] = trim(ucfirst(strtolower($word)));}
	
	
	// Computer Words
	$word_list = explode(' - ',$_POST['word_list']);
	$rights=0;
	foreach($word_list as $word){
		if(in_array($word,$my_words)){
			$rights++;
		}
	}
	$word_count = count($word_list);

	$percentage = number_format(($rights/$word_count)*100);
	$message = 'You got '.$rights.' words out of '.$word_count.' right.. '.$percentage.'%';
}
$g_var = 0;

// Lets see how many words to select
$max_words = min($PUZ_L,$PUZ_W,abs(($PUZ_L*$PUZ_W) / (4*$PUZ_C)))-1;

// Lets chose a random dictionary
$rand = rand(0,count($file_list)-1);

// Compose File Location
$file_name = $dictionary_dir.$file_list[$rand];

// Get filesize (in bytes)
$file_size = filesize($file_name);

// Lets keep randomly reading the file and extract words from it
$z=1;
while(count($array_words) < $max_words){
	// Start from a random location in file & read 1 KB (~50 words)
	$rand = rand(0,$file_size);
	$f = fopen($file_name,'r');
	fseek($f,$rand);
	$some_info = fread($f,1024);
	fclose($f);
	
	$temp_list = explode($line_sep,$some_info);

	// Remove first (incase we start in middle of word)
	array_pop($temp_list);
	// Compose Array of Words
	$word = array_pop($temp_list);
	$this_len = strlen($word);
	if(strlen($word) <= $PUZ_ML){
		$word = strtoupper($word);
		
		for($w = 0;$w < strlen($word); $w++){
			$array_words[$z][] = substr($word,$w,1);
		}
		$z++;
	}

	// Clear
	unset($some_info,$temp_list,$word);
}

$array_hints = array();

/* Compose Table */
for($x=1;$x<=$PUZ_L;$x++){	
	for($y=1;$y<=$PUZ_W;$y++){
		$rand = rand(0,count($array_characters)-1);		
		$array_table[$x][$y] = $array_characters[$rand];
	}
}

/* Create puzzle */
include('puzzle.class.php');

list($array_table,$badwordarray) = create_puzzle($array_words,$array_table,$PUZ_L,$PUZ_W);

/* Get Template */
include('extras/TEMPLATE');

/* Fix Header */
foreach($badwordarray as $sub => $word){ $badwordarray[$sub] = implode('',$word);}
foreach($array_words as $cur_word){
	$cur_word = implode($cur_word);
	if(!in_array($cur_word,$badwordarray)){
		array_push($array_hints,"'".substr($cur_word,0,1)."'");
		$WORD_LIST[] = ucfirst(strtolower($cur_word));
	}
}
list($top_cut,$middle_td,$end_cut) = explode('<!-- RESULT -->',$HEADER_TEMPLATE);

if($message != NULL){
	$middle_td = str_replace('%RESULTS%',$message,$middle_td);
} else {
	$middle_td = NULL;
}
$HEADER_TEMPLATE = $top_cut.$middle_td.$end_cut;

// Form Inputs
$x=5;
while($x <= 100){
	if($PUZ_W == $x){$sel=' selected';}else{$sel=NULL;}
	$PUZ_W_LIST .= '<option value="'.$x.'"'.$sel.'>'.$x.'</option>';			
	$x += 5;
}

$x=5;
while($x <= 100){
	if($PUZ_L == $x){$sel=' selected';}else{$sel=NULL;}
	$PUZ_L_LIST .= '<option value="'.$x.'"'.$sel.'>'.$x.'</option>';			
	$x += 5;
}


$HEADER_TEMPLATE = str_replace(
	array('%WORD_LIST%','%PUZ_W%','%HINT_OPTIONS%','%HINT_COUNT%','%PUZ_W_LIST%','%PUZ_L_LIST%'),
	array(implode(' - ',$WORD_LIST),$PUZ_W,implode(',',$array_hints),count($array_hints)-1,$PUZ_W_LIST,$PUZ_L_LIST),
	$HEADER_TEMPLATE);

/* Fix Body (lettering) */
list($top_cut,$middle_td,$end_cut) = explode('<!-- PUZ_W -->',$LETTER_TEMPLATE);

// Start Column
for($x=1;$x<=$PUZ_L;$x++){
	$LETTER .= $top_cut;
	// Start Rows
	for($y=1;$y<=$PUZ_W;$y++){
		$ID = "$x.$y.".$array_table[$x][$y];
		$LETTER .= str_replace(
			array('%LETTER%','%ID%'),
			array($array_table[$x][$y],$ID),
			$middle_td);
		// End Row
	}
	$LETTER .= $end_cut;
// End Column
}

/* Fix Footer */
$FOOTER_TEMPLATE = str_replace('%PUZ_W%',$PUZ_W,$FOOTER_TEMPLATE);

/* Done */
echo $HEADER_TEMPLATE.$LETTER.$FOOTER_TEMPLATE;
?>