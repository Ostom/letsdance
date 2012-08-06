<?php

namespace Project\DataBundle\Module;

use Exception;
/*class MyException extends Exception {
    function display() {
        echo 'MyException: ' . $this->getMessage() . "\n";
    }
}
*/
class ProjectException extends Exception {
   public function __construct($message, $errorLevel = 0, $errorFile = '', $errorLine = 0) {
	  parent::__construct($message, $errorLevel);
	  $this->file = $errorFile;
	  $this->line = $errorLine;
   }
}
