CIVICRM = 5.46.3
CORE_DIST = https://download.civicrm.org/civicrm-$(CIVICRM)-drupal.tar.gz
CORE_REPO = https://github.com/civicrm/civicrm-core.git

.PHONY: all clean build secure

all: clean build secure

clean:
	cat .gitignore | xargs rm -Rf

build: build-distro build-extra build-patch

build-distro:
	wget -q -O - $(CORE_DIST) | tar xfz -
	cd civicrm && ls -A > ../.gitignore && cp -r . ..
	rm -Rf civicrm

# Since the test directory is NOT in the official realease, but can be patched
EXTRA_DIRS = tests tools
GIT_OPTS = -c advice.detachedHead=false --quiet 
build-extra:
	git clone $(GIT_OPTS) -b $(CIVICRM) $(CORE_REPO)
	cd civicrm-core && cp -r $(EXTRA_DIRS) ..
	cd civicrm-core && cat ../.gitignore | xargs rm -Rf
	echo $(EXTRA_DIRS) | xargs -n 1 echo >> .gitignore
	rm -Rf civicrm-core

build-patch:
	cat patches/*.patch | patch -p1 -N -r - -V never

secure:
	rm -f vendor/pear/log/README.rst
	find . -type d -exec chmod 755 {} + 
	find . -type f -exec chmod go-w {} + 
