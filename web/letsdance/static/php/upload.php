<?php
namespace Project\DataBundle\Module;
class FormCheck
{
	public function uploadFile($pre_img_name,$num_img_name)
	{
		$uploaddir = getcwd().'/letsdance/media/img/';
		$fn = 'filename';
		$name = $_FILES[$fn]['error'];
		//echo($name);
		$uploadfile = $uploaddir.'img_'.$pre_img_name.'_'.$num_img_name;//basename($_FILES[$fn]['name']);
		move_uploaded_file($_FILES[$fn]['tmp_name'], $uploadfile);
		return $uploadfile;
	}
	public function test(){
		echo ("ok");
	}
}
