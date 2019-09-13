#!/bin/bash
set -e

# apply all patches
patch -p1 -N -r - -V never < patches/*.patch
