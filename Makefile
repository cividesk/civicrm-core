CIVICRM = 5.19.4
DRUPAL = 7.x
PREVIOUS = /var/www/html/civicrm-stable-5.19.4

.PHONY: all build diff clean

all: build secure

IMPORTS=packages vendor bower_components

civicrm:

civicrm-drupal:
	rm civicrm-drupal.zip

.PHONY: build-imports build-drupal build-patch
build: build-imports build-drupal build-patch

build-imports: clean-imports
	wget -O - https://sourceforge.net/projects/civicrm/files/civicrm-stable/$(CIVICRM)/civicrm-$(CIVICRM)-drupal.tar.gz/download# | tar xfz -
	cd civicrm ; cp -R $(IMPORTS) ..
	rm -Rf civicrm

build-drupal: clean-drupal
	wget -O civicrm-drupal.zip https://github.com/cividesk/civicrm-drupal/archive/$(DRUPAL)-$(CIVICRM)-cividesk.zip
	unzip civicrm-drupal.zip && rm civicrm-drupal.zip
	mv civicrm-drupal-$(DRUPAL)-$(CIVICRM)-cividesk drupal

build-patch:
	cat patches/*.patch | patch -p1 -N -r - -V never

secure:
	rm -f vendor/pear/log/README.rst
	find . -type d -exec chmod 755 {} + 
	find . -type f -exec chmod go-w {} + 
	chown -R root:apache *

multisite:
	-cat patches/multisite/*.patch | patch -p1 -N -r - -V never
# detach the repository to not accidentally merge the patches in
	@echo
	@echo ATTENTION: Please type the following commands:
	@echo "  git add *"
	@echo "  git commit -m 'PATCHES for multisite added, NEVER PUSH to origin!'"
	@echo "  git remote set-url --push origin do_not_push_from_multisite"
	@echo OR, to revert the patches that were added:
	@echo "  git reset --hard"

diff:
	diff -rwq --exclude=".git" --exclude="?bower.json" . $(PREVIOUS)

.PHONY: clean-imports clean-drupal
clean: clean-imports clean-drupal

clean-imports:
	rm -Rf $(IMPORTS)

clean-drupal:
	rm -Rf drupal
