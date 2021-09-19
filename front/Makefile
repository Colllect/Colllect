.PHONY: install
install: node_modules

node_modules: package.json $(wildcard package-lock.json)
	npm install
	# Create a file to force update the folder date
	@touch node_modules/__make-colllect-front-update-date
