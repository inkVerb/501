#!/bin/bash

basepath="/var/www/html/web/media/"

if [ "$1" = "img" ]; then
  name="$2"
  ext="$3"
  img_xs="$4"
  img_sm="$5"
  img_md="$6"
  img_lg="$7"
  img_xl="$8"
  /usr/bin/convert "${basepath}uploads/${name}.${ext}" -resize ${img_xs} "${basepath}images/${name}_154.${ext}"
  /usr/bin/convert "${basepath}uploads/${name}.${ext}" -resize ${img_sm} "${basepath}images/${name}_484.${ext}"
  /usr/bin/convert "${basepath}uploads/${name}.${ext}" -resize ${img_md} "${basepath}images/${name}_800.${ext}"
  /usr/bin/convert "${basepath}uploads/${name}.${ext}" -resize ${img_lg} "${basepath}images/${name}_1280.${ext}"
  /usr/bin/convert "${basepath}uploads/${name}.${ext}" -resize ${img_xl} "${basepath}images/${name}_1920.${ext}"
  /bin/mv "${basepath}uploads/${name}.${ext}" "${basepath}original/images/${name}.${ext}"
elif [ "$1" = "svg" ]; then

echo "svg"
fi
