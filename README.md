# php-ramlMerge
RAML Merge lets you merge in any included RAML files (!include) into a single RAML file via the command line.

`php ramlMerge.php /Users/mikestowe/Desktop/api_raml_files/api.raml > /Users/mikestowe/Desktop/compiledAPIRAML.raml`

Note - this should not be used as a replacement for your primary RAML files, but rather as a tool for services in which RAML includes may not work, such as Postman.

Requires PHP 5+ installed on machine.
