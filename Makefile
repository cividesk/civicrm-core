.PHONY: all build diff clean

all: build secure

IMPORTS=packages vendor bower_components drupal

civicrm:
	wget -O - https://sourceforge.net/projects/civicrm/files/civicrm-stable/5.19.4/civicrm-5.19.4-drupal.tar.gz/download# | tar xfz -

build: civicrm
	cd civicrm ; cp -R $(IMPORTS) ..
	cat patches/*.patch | patch -p1 -N -r - -V never
	rm -Rf civicrm

secure:
	rm vendor/pear/log/README.rst
	chown -R root:apache *

diff:
	diff -rwq --exclude=".git" . /var/www/html/civicrm-stable-5.19.4

clean:
	rm -Rf $(IMPORTS)
