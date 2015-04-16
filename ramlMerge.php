<?php
function doInclude ($file, $tabIndex = '') {
	$contents = @file_get_contents($file);
	if (!$contents) {
		$contents = @file_get_contents(BASE_PATH . $file);
	}
	
	if (!$contents) {
		return "\n\n# Unable to Include" . $file . "\n\n";
	}
	
	if ($tabIndex) {
		$contents = $tabIndex . str_replace("\n", "\n" . $tabIndex, $contents);
	}

	$contents = preg_replace_callback('/(([\s\t]*)[a-z0-9]+):[\s]+\!include ([^\s]+)/i', 
		function($matches) {
			$property = $matches[1];
			$spacing = $matches[2];
			$file = $matches[3];
			
			if (!preg_match("/^((https?:\/\/)|\/)/i", $file)) {
				$file = BASE_PATH . "/" . $file;
			}
			
			return $spacing . $property . ":\n" . doInclude($file, $spacing . "  ");
		}, 
		$contents);
			
	return $contents;
}


$file = $argv[1];
define(BASE_PATH, dirname($file));

echo doInclude($file) . "\n\n\n\n# -----------\n# Merged with ramlMerge.php\n# http://www.mikestowe.com\n\n";
?>
