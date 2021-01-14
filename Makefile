# makefile to automatize simple operations

install:
	composer install

serve:
	C:\wamp64\bin\php\php8.0.0\php.exe -d memory_limit=-1 -S localhost:8065 -t . index.php
