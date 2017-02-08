# General configuration
# --------------------
NPM_PREFIX = 'node_modules/.bin/'

# Dev tools
# --------------------
.PHONY: all watch install

all: dist/styles/main.css dist/scripts/main.js

# Watch
# NOTE On OSX, overwrite watch native command:
# brew install watch
# brew info watch # read watch path
# sudo mv <path_to_watch>/bin/watch /usr/local/bin
watch:
	@ watch -t -n 1 make all

# Concat & minify main styles
dist/styles/main.css: assets/styles/*.scss
	@ $(NPM_PREFIX)node-sass --output-style=compressed assets/styles/main.scss -o dist/styles/ --source-map dist/styles/
	@ echo "› $@ has been updated"

# Concat & minify main scripts
dist/scripts/main.js: assets/scripts/*.js
	@ $(NPM_PREFIX)uglifyjs assets/scripts/*.js -o dist/scripts/main.js --source-map dist/scripts/main.js.map -p relative
	@ echo "› $@ has been updated"

# Install dependencies
install:
	@ echo "› Checking NPM dependencies..."
	@ npm install
	@ echo "› Installation done, start with \"make watch\"."

# Build commands
# --------------------
.PHONY: build fonts images scripts scripts_specific styles styles_specific

build:
	@ make fonts
	@ make images
	@ make scripts
	@ make scripts_specific
	@ make styles
	@ make styles_specific

fonts:
	@ echo "› Moving fonts:"
	@ rm -rf dist/fonts
	cp -r assets/fonts dist
	@ echo "› Done."

images:
	@ echo "› Optimizing images:"
	@ rm -rf dist/images
	@ for dir in $$(find assets/images -type d);do mkdir -m 777 -p dist/$${dir#*/} && echo dist/$${dir#*/} && $(NPM_PREFIX)imagemin $${dir}/* -o dist/$${dir#*/};done
	@ echo "› Done."

scripts:
	@ echo "› Building scripts:"
	@ rm -rf dist/scripts && mkdir -m 777 -p dist/scripts/cache
	@ make dist/scripts/main.js
	@ echo "› Done."

scripts_specific:
	@ echo "› Moving specific scripts:"
	@ rm -rf dist/scripts_specific && mkdir -m 777 -p dist/scripts_specific
	cp -r assets/scripts_specific dist
	@ echo "› Done."

styles:
	@ echo "› Building styles:"
	@ rm -rf dist/styles && mkdir -m 777 -p dist/styles/cache
	@ make dist/styles/main.css
	@ echo "› Done."

styles_specific:
	@ echo "› Moving specific styles:"
	@ rm -rf dist/styles_specific && mkdir -m 777 -p dist/styles_specific
	cp -r assets/styles_specific dist
	@ echo "› Done."