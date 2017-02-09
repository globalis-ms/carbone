# General configuration
# --------------------

# Phony targets
.PHONY: all build install fonts images scripts scripts_specific styles styles_specific watch

# Prefix for all commands using Node.js modules binaries
# Keep it empty if you prefer to use global modules
NPM_PREFIX = 'node_modules/.bin/'


# Build commands
# --------------------

# Install and build command
all:
	@ make install
	@ make build

# Install Node.js modules locally
install:
	@ echo "› Checking NPM dependencies..."
	@ npm install

# Build all assets into dist
build:
	@ echo "› Build everything..."
	@ make fonts
	@ make images
	@ make scripts
	@ make scripts_specific
	@ make styles
	@ make styles_specific

# Copy fonts into dist
fonts:
	@ echo "› Moving fonts:"
	@ rm -rf dist/fonts && mkdir -m 777 -p dist/fonts
	cp -r assets/fonts dist
	@ echo "› Done."

# Optimize images and copy them into dist
images:
	@ echo "› Optimizing images:"
	@ rm -rf dist/images && mkdir -m 777 -p dist/images
	@ for dir in $$(find assets/images -type d);do mkdir -m 777 -p dist/$${dir#*/} && echo dist/$${dir#*/} && $(NPM_PREFIX)imagemin $${dir}/* -o dist/$${dir#*/};done
	@ echo "› Done."

# Minify, compile and copy main scripts into dist
scripts:
	@ echo "› Building scripts:"
	@ rm -rf dist/scripts && mkdir -m 777 -p dist/scripts/cache
	@ make dist/scripts/main.js
	@ echo "› Done."

# Copy specific scripts into dist (without minification/compilation)
scripts_specific:
	@ echo "› Moving specific scripts:"
	@ rm -rf dist/scripts_specific && mkdir -m 777 -p dist/scripts_specific
	cp -r assets/scripts_specific dist
	@ echo "› Done."

# Minify, compile and copy main styles into dist
styles:
	@ echo "› Building styles:"
	@ rm -rf dist/styles && mkdir -m 777 -p dist/styles/cache
	@ make dist/styles/main.css
	@ echo "› Done."

# Copy specific styles into dist (without minification/compilation)
styles_specific:
	@ echo "› Moving specific styles:"
	@ rm -rf dist/styles_specific && mkdir -m 777 -p dist/styles_specific
	cp -r assets/styles_specific dist
	@ echo "› Done."

# Watch changes on main scripts and styles files
# NOTE On OSX, overwrite watch native command:
# brew install watch
# brew info watch # read watch path
# sudo mv <path_to_watch>/bin/watch /usr/local/bin
watch:
	@ watch -t -n 1 make dist/styles/main.css dist/scripts/main.js


# Internal commands
# --------------------

# Concat & minify main styles
dist/styles/main.css: assets/styles/*.scss assets/styles/*.css
	@ $(NPM_PREFIX)node-sass --output-style=compressed assets/styles/main.scss -o dist/styles/ --source-map dist/styles/
	@ echo "› $@ has been updated"

# Concat & minify main scripts
dist/scripts/main.js: assets/scripts/*.js
	@ $(NPM_PREFIX)uglifyjs assets/scripts/*.js -o dist/scripts/main.js --source-map dist/scripts/main.js.map -p relative
	@ echo "› $@ has been updated"