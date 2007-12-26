<?php
declare(encoding="utf-8");

function get_characters_in_charlist($charlist)
{
	$characters = array();
	$complex_unicode_chr = (version_compare(phpversion(), '6', '>=') && !unicode_semantics() && is_unicode($charlist)) ? true : false;
	
	for ($i = 0, $len = strlen($charlist); $i < $len; $i++)
	{
		if ($i + 2 < $len && substr($charlist, $i + 1, 2) === '..')
		{
			if ($i + 3 < $len)
			{
				$j = ord($charlist[$i]);
				$k = ord($charlist[$i + 3]);
				if ($j <= $k)
				{
					for (; $j <= $k; $j++)
					{
						if ($complex_unicode_chr)
						{
							$characters[] = unicode_decode(pack('N', $j), 'UTF-32BE');
						}
						else
						{
							$characters[] = chr($j);
						}
					}
					
					$i += 3;
				}
				else
				{
					trigger_error("Invalid '..'-range, '..'-range needs to be incrementing", E_USER_WARNING);
					$characters[] = $charlist[$i];
				}
			}
			else
			{
				trigger_error("Invalid '..'-range, no character to the right of '..'", E_USER_WARNING);
				$characters[] = $charlist[$i];
			}
		}
		elseif (substr($charlist, $i, 2) === '..')
		{
			if ($i === 0)
			{
				trigger_error("Invalid '..'-range, no character to the left of '..'", E_USER_WARNING);
			}
			else
			{
				trigger_error("Invalid '..'-range", E_USER_WARNING);
			}
		}
		else
		{
			$characters[] = $charlist[$i];
		}
	}
	
	return array_unique($characters);
}

?>