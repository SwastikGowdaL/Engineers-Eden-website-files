<?php
/************* WordSearchPuzzle V1.0 *******************/
/*
Released by AwesomePHP.com, under the GPL License, a
copy of it should be attached to the zip file, or
you can view it on http://AwesomePHP.com/gpl.txt
*/
/************* WordSearchPuzzle V1.0 *******************/	

/*
This File Should be runned every time
a new dictionary is put in place (newline "\n" delimited);

What this file does is leave words in
[a-z] only.

You can run this file from the browser (one file at a time).
To do so change $run_browser = true;
*/

//List of files to edit - CHMODED to 755
$file_list = array('12dicts5','ispell.0','ispell.1','ispell.2','ispell.3','jargon','scowl');

//Word Separator
$word_delimiter = "\n";

//To remove list
$remove_items = array(' ','-','--','&quot;','!','@','#','$','%','^','&','*','(',')','_','+','{','}',
'|',':','"','<','>','?','[',']','\\',';',"'",',','.','/','*','+','~','`','=');

//Min word character
$min_len = 2;

//Run from browser?
$run_browser = false;

//Check run from browser
if($run_browser == true){
	if($_GET['f_sub'] != NULL){
		$start = $_GET['f_sub'];
		if($start > count($file_list)){ die('You are done');}
	} else {
		$start = 0;
	}
}

//Loop through every file
foreach($file_list as $this_sub => $dict_file){
	if($this_sub >= $start){
		//Read Dictionary
		$f = fopen($dict_file,'r');
		while($t = fread($f,1026547)){
			$content .= $t;
		}
		fclose($f);
		
		echo "Done Reading $dict_file<br/>\n<hr><br/>\n";
		
		//Store it
		$lines = explode($word_delimiter,$content);
		unset($content);
		
		$x = 1;
		
		foreach($lines as $sub => $word){
			if(strlen(trim($word)) >= $min_len){
				$lines[$sub] = trim(str_replace($remove_items,'',strtolower($word)));
				echo "Updated Word: ($x) [$word] To [".$lines[$sub]."]<br/>\n";
			} else {
				$lines[$sub] = NULL;
			}
			$x++;
			if($x == '56250'){
				echo "<font color='red'>Chances are this page will crash due to memory allocation. To fix this, increase max_excution_time and memory_limit in php.ini.<br/>";
			}
		}
	
		//Write to file again
		$f = fopen($dict_file,'w');
		fwrite($f,implode($word_delimiter,$lines));
		fclose($f);
		
		echo "Done Writing To $dict_file<br/>\n<hr><br/>\n";
		
		//If running from the browser
		if($run_browser == true){
			die('Please proceed to second file <a href="?f_sub='.($start+1).'">Gooo..</a>');
		}
	}
}

?>