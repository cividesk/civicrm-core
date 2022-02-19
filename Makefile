CIVICRM = 5.19.4
DRUPAL = 7.x

CORE_DIST = https://download.civicrm.org/civicrm-$(CIVICRM)-drupal.tar.gz
CORE_IMPORTS=packages vendor bower_components

# civicrm-drupal is not IMPORTed above so we can substitute our own repo when necessary
DRUPAL_NAME = $(DRUPAL)-$(CIVICRM)
DRUPAL_DIST = https://github.com/civicrm/civicrm-drupal/archive/$(DRUPAL_NAME).zip

.PHONY: all build secure clean

all: build secure

.PHONY: build-imports build-drupal build-patch
build: build-imports build-drupal build-patch

build-imports: clean-imports
	wget -q -O - $(CORE_DIST) | tar xfz -
	cd civicrm ; cp -R $(CORE_IMPORTS) ..
	rm -Rf civicrm

build-drupal: clean-drupal
	wget -q -O $(DRUPAL_NAME).zip $(DRUPAL_DIST)
	unzip -q $(DRUPAL_NAME).zip && rm -Rf $(DRUPAL_NAME).zip
	mv civicrm-drupal-$(DRUPAL_NAME) drupal && rm -Rf civicrm-drupal-$(DRUPAL_NAME)

build-patch:
	cat patches/*.patch | patch -p1 -N -r - -V never

secure:
	rm -f vendor/pear/log/README.rst
	find . -type d -exec chmod 755 {} + 
	find . -type f -exec chmod go-w {} + 

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

.PHONY: clean-imports clean-drupal
clean: clean-imports clean-drupal

clean-imports:
	rm -Rf $(CORE_IMPORTS)

clean-drupal:
	rm -Rf drupal civicrm-drupal-$(DRUPAL_NAME)
