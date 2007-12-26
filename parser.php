<?php
declare(encoding="utf-8");

function get_characters_in_charlist($charlist)
{
	$characters = array();
	$unicode_semantics = (version_compare(phpversion(), '6', '>=') && unicode_semantics()) ? true : false;
	$unicode_charlist = (version_compare(phpversion(), '6', '>=') && is_unicode($charlist)) ? true : false;
	
	// unicode_semantics=off yet $charlist is a unicode string
	if (!$unicode_semantics && $unicode_charlist)
	{
		$double_full_stop = unicode_decode("\x00\x00\x00\x2E\x00\x00\x00\x2E", 'UTF-32BE');
	}
	// unicode_semantics=on yet $charlist is a binary string
	elseif ($unicode_semantics && !$unicode_charlist)
	{
		$double_full_stop = '..';
		settype($double_full_stop, 'binary');
	}
	// $charlist follows unicode_semantics
	else
	{
		$double_full_stop = '..';
	}
	
	for ($i = 0, $len = strlen($charlist); $i < $len; $i++)
	{
		if ($i + 2 < $len && substr($charlist, $i + 1, 2) === $double_full_stop)
		{
			if ($i + 3 < $len)
			{
				$j = ord($charlist[$i]);
				$k = ord($charlist[$i + 3]);
				if ($j <= $k)
				{
					for (; $j <= $k; $j++)
					{
						// unicode_semantics=off yet $charlist is a unicode string
						if (!$unicode_semantics && $unicode_charlist)
						{
							$characters[] = unicode_decode(pack('N', $j), 'UTF-32BE');
						}
						// unicode_semantics=on yet $charlist is a binary string
						elseif ($unicode_semantics && !$unicode_charlist)
						{
							$chr = chr($j);
							settype($chr, 'binary');
							$characters[] = $chr;
						}
						// $charlist follows unicode_semantics
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
		elseif (substr($charlist, $i, 2) === $double_full_stop)
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