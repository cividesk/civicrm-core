#!/bin/bash

### ATTENTION ###
### to be run from repository root as patches/multisite.sh

# This script will install the multisite patches in the current directory
patch -p1 --forward -r - -i patches/multisite-changes-for-5.19.patch
# and detach the push repository so you do not accidentally merge these in
git remote set-url --push origin no_push_from_multisite
