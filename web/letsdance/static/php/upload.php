<?php
namespace Project\DataBundle\Module;
class FormCheck
{
	public function uploadFile()
	{
		$uploaddir = getcwd().'/letsdance/static/files/';
		$fn = 'filename';
		$name = $_FILES[$fn]['error'];
		//echo($name);
		$uploadfile = $uploaddir.basename($_FILES[$fn]['name']);
		move_uploaded_file($_FILES[$fn]['tmp_name'], $uploadfile);
		return $uploadfile;
	}
	public function test(){
		echo ("ok");
	}
}
