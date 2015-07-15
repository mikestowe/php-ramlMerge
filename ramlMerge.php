<?php
function doInclude ($folder, $file, $tabIndex = '') {
	$contents = @file_get_contents($file);
	
	if (!$contents) {
		return "\n\n# Unable to include " . $file . "\n\n";
	}
	
	if ($tabIndex) {
		$contents = $tabIndex . str_replace("\n", "\n" . $tabIndex, $contents);
	}
	
	$lines = explode("\n", $contents);
	$result = "";
	foreach($lines as $line) {
		$line = preg_replace_callback('/^(([ \t]*)([^#\n]+)[ \t]*[ \t]*)\!include ((.+)\/([^\s]+))$/i', 
			function($matches) use ($folder) {
				$pre = $matches[1];
				$spacing = $matches[2];
				$resource = $matches[4];
				$containerFolder = $matches[5];
				$file = $matches[6];
				$cap = " \n";
								
				// Resolve where the included file is
				if (!preg_match("/^((https?:\/\/)|\/)/i", $file)) {
					// File resource
					$containerFolder = realpath($folder . "/" . $containerFolder);
					$file = realpath($containerFolder . "/" . $file);
				} else {
					// URL resource
					$containerFolder = $folder;
					$file = $resource;
				}
				
				// Load
				$subContent = doInclude($containerFolder, $file, $spacing . "    ");
				
				// Add proper connector to included file
				if (requiresPipe($pre, $subContent)) {
					$cap = "| \n";
				}
				
				// Join				
				return $pre . $cap . $subContent;
			}, 
			$line);

		$result .= $line . "\n";
	}
	return $result;
}

function requiresPipe($pre, $subContent) {
	if (strpos($pre, "example") || strpos($pre, "scheme") || strpos($pre, "content")) {
		return true;
	} else {
		$i = 0;
		$subLines = explode("\n", $subContent);
		while (isset($subLines[$i]) && !preg_match("/[^\s]/i", $subLines[$i])) {
			$i++;
		}
		if (strpos($subLines[$i], '{')) {
			return true;
		}	
	}
	return false;
}

$file = $argv[1];

echo doInclude(dirname($file), $file) . "\n\n\n\n# -----------\n# Merged with ramlMerge.php\n# http://www.mikestowe.com\n\n";
?>
