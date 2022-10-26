UID = $$(id -u)
PWD = $$(pwd)

@all:
	cat makefile

# QA
phpqa:
	vendor/bin/phpqa --config .ci/qa