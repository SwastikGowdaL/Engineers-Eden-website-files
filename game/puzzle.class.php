<?php

/****************************************************************************************
	Function Name	: create_puzzle
	Argument		: $array_words	-> containing actual words
				 	: $array_table	-> containing random characters
				 	: $PUZ_L		-> Length of table
				 	: $PUZ_W		-> width of table
	Returns:		: $array_words	-> Contains Array(Array()) of table letters, which
					: 					has random letters + words in it
					: $badwordarray	-> Contains Array(Array()) of words not inserted
					:					in Table ($array_words).
	Function	 	: Function to display words randomly from option 1 to 8 in a table 
	Copyrights   	: AwesomePHP.com -> To be used with refrence to AwesomePHP.com only.
*****************************************************************************************/
function create_puzzle($array_words,$array_table,$PUZ_L,$PUZ_W){
	$rev_sort = array();	
	$output_array = array();
	$fill_array = array();
	global $g_var;
	$flag_max_limit = 0;
	$badwordarray = array();
	$flag = 0;
	$array_words_row = count($array_words);
	
	if(($array_words_row > $PUZ_L) || ($array_words_row > $PUZ_W))
	{
		echo "Word list elements are more than length or breadth set.\n";
		return $array_table;
	}
	
	for($i=1; $i<=$array_words_row; $i++)
	{
			srand((double)microtime()*1000000);
			$random = (rand(1,8));
			$rev_sort[$i] = $random;
		
	}
	rsort($rev_sort);
	for($i=1; $i<=$array_words_row; $i++)
	{
		$j=0;
		if($flag_max_limit == 1)
		{
			
			if(($rnum) == 1)
			{
				for($badword=$i;$badword<=$array_words_row;$badword++)
				{
					$badwordarray[$j] = $array_words[$badword];
					$j++;
				}
				
				break;
			}
			
			for($y = $t; $y<=$array_words_row;$y++)
			{
				srand((double)microtime()*1000000);
				$random = (rand(1,($rnum-1)));
				$rev_sort[$y-1] = $random;
				
				$i = $t;
			}
			rsort($rev_sort);
			$flag_max_limit = 0;
		}
		$t = $i;
		$array_words_col = count($array_words[$i]);
		if(($array_words_col > $PUZ_L) || ($array_words_col > $PUZ_W))
		{	
			echo "One of the word list elements are more than length or breadth set.\n";
			return $array_table;
		}
		$rnum = $rev_sort[$i-1];

		switch($rnum)
		{
			case 1:	/* Display word From top to bottom on any column*/
				$var = 1;
				$start_1 = 1;
				while(1)
				{
					if($var >= 30)
					{
						$flag_max_limit = 1;
						$i--;
						
						break 2;
					}
					if($var >=20 && $start_1 <=$PUZ_W)
					{
						$random_num = $start_1;
						
						$start_1 = $start_1 + 1;
					}
					else
					{
						$random_num = generate_random_number(1,$PUZ_W,0);//get random num
					}
					$rand = $random_num;
					$flag =0;
					for($j=0; $j<$array_words_col;$j++)
					{
					
						if($fill_array[$random_num] == NULL)
						{
							$random_num = $random_num + $PUZ_W;

						}
						else
						{
							$var ++;
							$flag = 1;
							break;
						}
					}
					if($flag ==0 )
						break;
				}
				$random_num = $rand;
				$fill_random = $random_num;
				$count = 1;
				for($r=0 ; $r<$array_words_col; $r++)
				{
					$array_table[$count][$random_num] = $array_words[$i][$r];
					$count = $count + 1;
					$fill_array[$fill_random] = 1;
					$fill_random = $fill_random + $PUZ_W;
					
				}	
				break;
			
			case 2: /*Display word from bottom to top on any column*/
				$var = 1;
				$start_1 = 1;
				while(1)
				{
					if($var >= 30)
					{
						$flag_max_limit = 1;
						
						$i--;
						break 2;
					}
					if($var >=20 && $start_1 <=$PUZ_W)
					{
						
						$random_num = $start_1;
						$start_1 = $start_1 + 1;
					}
					else
					{
						$random_num = generate_random_number(1,$PUZ_W,0);//get random num
					}
					$rand = $random_num;
					
					$random_num = $random_num + ($PUZ_L * ($PUZ_W-1));
					
					$flag =0;
					for($j=0; $j<$array_words_col;$j++)
					{
					
						if($fill_array[$random_num] == NULL)
						{
							$random_num = $random_num - $PUZ_W;

						}
						else
						{
							$var ++;
							$flag = 1;
							break;
						}
					}
					if($flag ==0 )
						break;
				}
				$random_num = $rand;
				$count = $PUZ_L;
				$width = $PUZ_W - 1;
				for($r=0 ; $r<$array_words_col; $r++)
				{
					$array_table[$count][$random_num] = $array_words[$i][$r];
					$count = $count - 1;
					$fill_array[$rand + ($PUZ_L * ($width))] = 1;
					$width = $width - 1;
					
				}	
				break;

			case 3:	/* Display word from right to left on any row */
			$var = 1;
			$start_1 = 1;
				while(1)
				{
					if($var >= 30)
					{
						$flag_max_limit = 1;
						$i--;
						
						break 2;
					}
					if($var >=20 && $start_1 <=$PUZ_W)
					{
						$random_num = $start_1;
						$start_1 = $start_1 + 1;
						
					}
					else
					{
						$random_num = generate_random_number(1,$PUZ_W,0);//get random num
					}
					$rand = $random_num;
					
					$random_num = $random_num * $PUZ_W;
					
					$flag =0;
					for($j=0; $j<$array_words_col;$j++)
					{
					
						if($fill_array[$random_num] == NULL)
						{
							$random_num = $random_num - 1;

						}
						else
						{
							$var++;
							$flag = 1;
							break;
						}
					}
					if($flag ==0 )
						break;
				}
				$random_num = $rand;
				$count = $PUZ_W;
				$width = $rand * $PUZ_W;
				for($r=0 ; $r<$array_words_col; $r++)
				{
					$array_table[$random_num][$count] = $array_words[$i][$r];
					$count = $count - 1;
					$fill_array[$width] = 1;
					$width = $width - 1;
					
				}	
				break;
			
			case 4:	/* Display word from left to right on any row */
				$var = 1;
				$start_1 = 1;
				while(1)
				{
					if($var >= 30)
					{
						$flag_max_limit = 1;
						$i--;
						break 2;
					}
					if($var >=20 && $start_1 <=$PUZ_W)
					{
						$random_num = $start_1;
						$start_1 = $start_1 + 1;
					}
					else
					{
						$random_num = generate_random_number(1,$PUZ_W,0);//get random num
					}
					$rand = $random_num;
					
					$random_num = (($random_num - 1 ) * $PUZ_W) + 1;
					
					$flag =0;
					for($j=0; $j<$array_words_col;$j++)
					{
					
						if($fill_array[$random_num] == NULL)
						{
							$random_num = $random_num + 1;

						}
						else
						{
							$var = $var + 1;
							$flag = 1;
							break;
						}
					}
					if($flag ==0 )
						break;
				}
				$random_num = $rand;
				$count = 1;
				$width = (($rand - 1 ) * $PUZ_W) + 1;
				for($r=0 ; $r<$array_words_col; $r++)
				{
					$array_table[$random_num][$count] = $array_words[$i][$r];
					$count = $count + 1;
					$fill_array[$width] = 1;
					$width = $width + 1;
					
				}	
				break;
			
			case 5:	/* Display word from top to bottom diagonally [left to right] */
				$var = 1;
				while(1)
				{
					if($var >= 30)
					{
						$flag_max_limit = 1;
						break 2;
					}
					$random_num = generate_random_number(1,($PUZ_L - ($array_words_col - 1)),0);//get random num
					$rand = $random_num;
					
					$random_num = (($random_num - 1 ) * $PUZ_W) + 1;
					
					$flag =0;
					for($j=0; $j<$array_words_col;$j++)
					{
					
						if($fill_array[$random_num] == NULL)
						{
							$random_num = $random_num + $PUZ_W + 1;

						}
						else
						{
							$var ++;
							$flag = 1;
							break;
						}
					}
					if($flag ==0 )
						break;
				}
				$random_num = $rand;
				$count = 1;
				$width = (($rand - 1 ) * $PUZ_W) + 1;
				for($r=0 ; $r<$array_words_col; $r++)
				{
					$array_table[$random_num][$count] = $array_words[$i][$r];
					$count = $count + 1;
					$random_num = $random_num + 1;
					$fill_array[$width] = 1;
					$width = $width + $PUZ_W + 1;
					
				}	
				break;


			case 6:	/* Display word from top to bottom diagonally [right to left] */
				$var = 1;
				while(1)
				{
					if($var >= 30)
					{
						$flag_max_limit = 1;
						break 2;
					}
					$random_num = generate_random_number(1,($PUZ_L - ($array_words_col-1)),0);//get random num
					$rand = $random_num;
					
					$random_num = $random_num  * $PUZ_W;
					
					$flag =0;
					for($j=0; $j<$array_words_col;$j++)
					{
					
						if($fill_array[$random_num] == NULL)
						{
							$random_num = $random_num + $PUZ_W - 1;

						}
						else
						{
							$var ++;
							$flag = 1;
							break;
						}
					}
					if($flag ==0 )
						break;
				}
				$random_num = $rand;
				$count = $PUZ_W;
				$width = $rand  * $PUZ_W;
				for($r=0 ; $r<$array_words_col; $r++)
				{
					$array_table[$random_num][$count] = $array_words[$i][$r];
					$count = $count - 1;
					$random_num = $random_num + 1;
					$fill_array[$width] = 1;
					$width = $width + $PUZ_W - 1;
					
				}	
				break;

			case 7:	/*Display word from bottom to top diagonally [left to right]*/
				$var = 1;
				while(1)
				{
					if($var >= 30)
					{
						$flag_max_limit = 1;
						break 2;
					}
					$random_num = generate_random_number($array_words_col,$PUZ_L,0);//get random num
					$rand = $random_num;
					
					$random_num = (($random_num - 1 ) * $PUZ_W) + 1;
					
					$flag =0;
					for($j=0; $j<$array_words_col;$j++)
					{
					
						if($fill_array[$random_num] == NULL)
						{
							$random_num = $random_num - ($PUZ_W - 1);

						}
						else
						{
							$var ++;
							$flag = 1;
							break;
						}
					}
					if($flag ==0 )
						break;
				}
				$random_num = $rand;
				$count = 1;
				$width = (($rand - 1 ) * $PUZ_W) + 1;
				for($r=0 ; $r<$array_words_col; $r++)
				{
					$array_table[$random_num][$count] = $array_words[$i][$r];
					$count = $count + 1;
					$random_num = $random_num - 1;
					$fill_array[$width] = 1;
					$width = $width - ($PUZ_W - 1);
					
				}	
				break;

			case 8:	/* Display word from bottom to top diagonally [right to left]  */
				$var = 1;
				while(1)
				{
					if($var >= 30)
					{
						$flag_max_limit = 1;
						break 2;
					}
					$random_num = generate_random_number($array_words_col,$PUZ_L,0);//get random num
					$rand = $random_num;
					
					$random_num = $random_num  * $PUZ_W;
					
					$flag =0;
					for($j=0; $j<$array_words_col;$j++)
					{
					
						if($fill_array[$random_num] == NULL)
						{
							$random_num = $random_num - ($PUZ_W + 1);

						}
						else
						{
							$var ++;
							$flag = 1;
							break;
						}
					}
					if($flag ==0 )
						break;
				}
				$random_num = $rand;
				$count = $PUZ_W;
				$width = $random_num  * $PUZ_W;
				for($r=0 ; $r<$array_words_col; $r++)
				{
					$array_table[$random_num][$count] = $array_words[$i][$r];
					$count = $count - 1;
					$random_num = $random_num - 1;
					$fill_array[$width] = 1;
					$width = $width - ($PUZ_W + 1);
					
				}	
				break;
		}
	}
	// $
	return array($array_table,$badwordarray);;
}

/* Helper Function to generate random number */
function generate_random_number($start, $end, $flag){
	global $g_var;
	srand((double)microtime()*1000000);
	$random = (rand($start,$end));
	
	return $random;
}
?>