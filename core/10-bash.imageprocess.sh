#!/bin/bash

basepath="/var/www/html/web/media/"
name="$1"
ext="$2"
type="$3"

# Convert bmp to png in our process
if [ "$type" = "img" ] && [ "$ext" = "bmp" ]; then

  img_xs="$4"
  img_sm="$5"
  img_md="$6"
  img_lg="$7"
  img_xl="$8"
  # Always create a thumbnail
  if [ "$img_sm" != "thum" ]; then
    /usr/bin/convert -size 154x154 "${basepath}uploads/${name}.${ext}" "${basepath}images/${name}_154.png"
  else
    /usr/bin/convert -size ${img_xs} "${basepath}uploads/${name}.${ext}" "${basepath}images/${name}_154.png"
  fi
  # Check the image is large enough for each size
  if [ "$img_sm" != "none" ]; then
    /usr/bin/convert -size ${img_sm} "${basepath}uploads/${name}.${ext}" "${basepath}images/${name}_484.png"
  fi
  if [ "$img_md" != "none" ]; then
    /usr/bin/convert -size ${img_md} "${basepath}uploads/${name}.${ext}" "${basepath}images/${name}_800.png"
  fi
  if [ "$img_lg" != "none" ]; then
    /usr/bin/convert -size ${img_lg} "${basepath}uploads/${name}.${ext}" "${basepath}images/${name}_1280.png"
  fi
  if [ "$img_xl" != "none" ]; then
    /usr/bin/convert -size ${img_xl} "${basepath}uploads/${name}.${ext}" "${basepath}images/${name}_1920.png"
  fi
  # Move & copy conversion, remove original
  /usr/bin/convert "${basepath}uploads/${name}.${ext}" "${basepath}original/images/${name}.png"
  /bin/cp "${basepath}original/images/${name}.png" "${basepath}images/${name}.png"
  /bin/rm "${basepath}uploads/${name}.${ext}"

# Process normal images
elif [ "$type" = "img" ]; then

  # Test for accepted types, redundant and secure
  if [ "$ext" != "jpg" ] && [ "$ext" != "jpeg" ] && [ "$ext" != "png" ] && [ "$ext" != "gif" ]; then
    exit 0; # Quiet exit, no need for STDERR
  fi

  # imagemagick will detect formats automatically, so mimetypes need no further checking

  img_xs="$4"
  img_sm="$5"
  img_md="$6"
  img_lg="$7"
  img_xl="$8"
  # Always create a thumbnail
  if [ "$img_sm" != "thum" ]; then
    /usr/bin/convert -size 154x154 "${basepath}uploads/${name}.${ext}" "${basepath}images/${name}_154.${ext}"
  else
    /usr/bin/convert "${basepath}uploads/${name}.${ext}" -resize ${img_xs} "${basepath}images/${name}_154.${ext}"
  fi
  # Check the image is large enough for each size
  if [ "$img_sm" != "none" ]; then
    /usr/bin/convert "${basepath}uploads/${name}.${ext}" -resize ${img_sm} "${basepath}images/${name}_484.${ext}"
  fi
  if [ "$img_md" != "none" ]; then
    /usr/bin/convert "${basepath}uploads/${name}.${ext}" -resize ${img_md} "${basepath}images/${name}_800.${ext}"
  fi
  if [ "$img_lg" != "none" ]; then
    /usr/bin/convert "${basepath}uploads/${name}.${ext}" -resize ${img_lg} "${basepath}images/${name}_1280.${ext}"
  fi
  if [ "$img_xl" != "none" ]; then
    /usr/bin/convert "${basepath}uploads/${name}.${ext}" -resize ${img_xl} "${basepath}images/${name}_1920.${ext}"
  fi
  # Move & copy original
  /bin/mv "${basepath}uploads/${name}.${ext}" "${basepath}original/images/${name}.${ext}"
  /bin/cp "${basepath}original/images/${name}.${ext}" "${basepath}images/${name}.${ext}"

# Convert svg for thumbnail
elif [ "$type" = "svg" ] && [ "$ext" = "svg" ]; then

  # Always create a thumbnail
  /usr/bin/convert -background none -size 154x154 "${basepath}uploads/${name}.${ext}" "${basepath}images/${name}_154_svg.png"
  # Move original
  /bin/mv "${basepath}uploads/${name}.${ext}" "${basepath}images/${name}.${ext}"

fi
