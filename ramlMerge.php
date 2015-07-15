<?php
function doInclude ($folder, $file, $tabIndex = '') {
	$contents = @file_get_contents($file);
	
	if (!$contents) {
		return "\n\n# Unable to include " . $file . "\n\n";
	}
	
	if ($tabIndex) {
		$contents = $tabIndex . str_replace("\n", "\n" . $tabIndex, $contents);
	}

	$contents = preg_replace_callback('/(([ \t]*)([a-z0-9_\/\-]+)):[\s]+\!include ((.+)\/([^\s]+))/i', 
		function($matches) use ($folder) {
			$property = $matches[3];
			$spacing = $matches[2];
			$resource = $matches[4];
			$containerFolder = $matches[5];
			$file = $matches[6];
			
			if (!preg_match("/^((https?:\/\/)|\/)/i", $file)) {
				// File resource
				$containerFolder = realpath($folder . "/" . $containerFolder);
				$file = realpath($containerFolder . "/" . $file);
			} else {
				// URL resource
				$containerFolder = $folder;
				$file = $resource;
			}
			
			$i = 0;
			$cap = ": | \n";
			$subContent = doInclude($containerFolder, $file, $spacing . "    ");
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

echo doInclude(dirname($file), $file) . "\n\n\n\n# -----------\n# Merged with ramlMerge.php\n# http://www.mikestowe.com\n\n";
?>
