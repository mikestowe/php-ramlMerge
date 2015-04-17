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

	$contents = preg_replace_callback('/(([\s\t]*)([a-z0-9_\/\-]+)):[\s]+\!include ([^\s]+)/i', 
		function($matches) {
			$property = $matches[3];
			$spacing = $matches[2];
			$file = $matches[4];
			
			if (!preg_match("/^((https?:\/\/)|\/)/i", $file)) {
				$file = BASE_PATH . "/" . $file;
			}
			
			$i = 0;
			$cap = ": | \n";
			$subContent = doInclude($file, $spacing . "    ");
			$subLines = explode("\n", $subContent);
			
			while (isset($subLines[$i]) && !preg_match("/[^\s]/i", $subLines[$i])) {
				$i++;
			}
			
			if (strpos($subLines[$i], ':') && preg_match("/(:\s*('|\")(.+)('|\"))*/", $subLines[$i])) {
				$cap = ":\n";
			}			
			
			return $spacing . $property . $cap . $subContent;

		}, 
		$contents);
			
	return $contents;
}


$file = $argv[1];
define('BASE_PATH', dirname($file));

echo doInclude($file) . "\n\n\n\n# -----------\n# Merged with ramlMerge.php\n# http://www.mikestowe.com\n\n";
?>
