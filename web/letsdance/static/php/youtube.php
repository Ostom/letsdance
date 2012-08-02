<?php

namespace Project\DataBundle\Module;
class Tube
{
	public function Generate($path_prist = 'eMDznwbDuJc')
	{		
		$path = 'http://www.youtube.com/embed/'.$path_prist;
		//$youtube = '<iframe width="560" height="315" src="'.$path.'" frameborder="0" allowfullscreen></iframe>';
		//echo ($youtube);
		return $path;
	}
	public function Get($path)
	{		
		$a = str_replace( 'http://youtu.be/', '', $path);
		return $a;
	}
	public function test(){
		echo ("ok");
	}
}
