<?php

class GetMostCommonColors
{
	var $PREVIEW_WIDTH = 150;
	var $PREVIEW_HEIGHT = 150;
	var $error;

	function Get_Color($img)
	{
		//Is image readable?
		if (is_readable($img))
		{
            // Resazing image using nearest beighbor image scaling and calculating the new width and heigth
			$size = GetImageSize($img);
			$scale = 0;

			if ($size[0] > 0)
            {
				$scale = min($this -> PREVIEW_WIDTH / $size[0], $this-> PREVIEW_HEIGHT / $size[1]);
            }
			if ($scale < 1)
            {
				$width = floor($scale * $size[0]);
				$height = floor($scale * $size[1]);
			}
			else
			{
				$width = $size[0];
				$height = $size[1];
			}

			$image_resized = imagecreatetruecolor($width, $height);

			if ($size[2] == 1)
				$image_orig = imagecreatefromgif($img);
			if ($size[2] == 2)
				$image_orig = imagecreatefromjpeg($img);
			if ($size[2] == 3)
				$image_orig = imagecreatefrompng($img);

			imagecopyresampled($image_resized, $image_orig, 0, 0, 0, 0, $width, $height, $size[0], $size[1]);
			$im = $image_resized;
			$imgWidth = imagesx($im);
			$imgHeight = imagesy($im);
			$total_pixel_count = 0;

            //Counting pixeles and collecting color data (RGB)
			// Key = RGB code
			// Value = Number of times the color appeared
			for ($y = 0; $y < $imgHeight; $y++)
			{
				for ($x = 0; $x < $imgWidth; $x++)
				{
					$total_pixel_count++;
					$index = imagecolorat($im, $x, $y);
					$colors = imagecolorsforindex($im,$index);

					$hex = substr("0".dechex($colors['red']),-2).substr("0".dechex($colors['green']),-2).substr("0".dechex($colors['blue']),-2);

					if (!isset($hexarray[$hex]))
						$hexarray[$hex] = 1;
					else
						$hexarray[$hex]++;
				}
			}


            //Convert color counts to percentages
            foreach ($hexarray as $key => $value) {
                $hexarray[$key] = (float) $value / $total_pixel_count * 100;
            }

			//Check if number of colors is less then 5
			if (count($hexarray) <= 5) {
            	return $hexarray;
            }

			//Get top 5 colors
            $top5colors = array();
			for ($i = 0; $i < 5; $i++)
            {
                $maxValueKey = $this -> GetMaxValue($hexarray);
                $top5colors[$maxValueKey] = $hexarray[$maxValueKey];
                $hexarray[$maxValueKey] = 0;
            }

            return $top5colors;
		}
		else
		{
			$this -> error = "Image ".$img." does not exist or is unreadable";
			return false;
		}
	}

	function GetMaxValue($arr)
    {
		$max = PHP_INT_MIN;

		foreach ($arr as $key => $value)
        {
			if ($value > $max) {
            	$max = $value;
				$maxKey = $key;
            }
        }
		return $maxKey;
    }

	//Convert HEX to RGB...
	//https://wtools.io/php-snippet/convert-hex-to-rgb-in-php
	function hexToRgb($hex) {
        $hex      = str_replace('#', '', $hex);
        $length   = strlen($hex);
        $rgb['r'] = hexdec($length == 6 ? substr($hex, 0, 2) : ($length == 3 ? str_repeat(substr($hex, 0, 1), 2) : 0));
        $rgb['g'] = hexdec($length == 6 ? substr($hex, 2, 2) : ($length == 3 ? str_repeat(substr($hex, 1, 1), 2) : 0));
        $rgb['b'] = hexdec($length == 6 ? substr($hex, 4, 2) : ($length == 3 ? str_repeat(substr($hex, 2, 1), 2) : 0));

        return 'R:'.$rgb['r'].' G:'.$rgb['g'].' B:'.$rgb['b'];
    }
}
