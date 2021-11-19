#!/bin/bash

# Instructions
## File must be correctly named and in ./media/uploads/
## Directories must exist: ./media/uploads ./media/images ./media/original/images

# How to use:
## bash.imageprocess.sh [file basename] [file extension] [img/svg] [orientation: tall/wide/squr] [xs max] [sm max] [md max] [lg max] [xl max]
# Eg:
## bash.imageprocess.sh file_name png tall 154x154 484 800 1280 1920
## bash.imageprocess.sh file_name svg

thumb="50x50"
basepath="./media/"
name="$1"
ext="$2"

# Convert bmp to png in our process
if [ "$ext" = "bmp" ]; then
  toext="png"

# Other rasterized images
elif [ "$ext" != "svg" ]; then

  # Test for accepted types, redundant and secure
  if [ "$ext" != "jpg" ] && [ "$ext" != "jpeg" ] && [ "$ext" != "png" ] && [ "$ext" != "gif" ]; then
    exit 0; # Quiet exit, no need for STDERR
  fi

  toext="$ext"

fi

# Rasterized images
if [ "$ext" != "svg" ]; then

  # Dimensions
  orientation=$3
  img_xs="$4"
  img_sm="$5"
  img_md="$6"
  img_lg="$7"
  img_xl="$8"

  # Pull orientation for consistent file names
  if [ "$orientation" = "squr" ] || [ "$orientation" = "wide" ]; then
    name_xs=$(echo $img_xs | sed 's/x.*//')
    name_sm=$(echo $img_sm | sed 's/x.*//')
    name_md=$(echo $img_md | sed 's/x.*//')
    name_lg=$(echo $img_lg | sed 's/x.*//')
    name_xl=$(echo $img_xl | sed 's/x.*//')

  elif [ "$orientation" = "tall" ]; then
    name_xs=$(echo $img_xs | sed 's/.*x//')
    name_sm=$(echo $img_sm | sed 's/.*x//')
    name_md=$(echo $img_md | sed 's/.*x//')
    name_lg=$(echo $img_lg | sed 's/.*x//')
    name_xl=$(echo $img_xl | sed 's/.*x//')
  fi

  # imagemagick will detect formats automatically, so mimetypes need no further checking

  # Always create a thumbnail
  /usr/bin/convert "${basepath}uploads/${name}.${ext}" -resize ${thumb} "${basepath}images/${name}_thumb.${toext}"

  # Check that each image size is set
  if [ "$img_xs" != "none" ]; then
    /usr/bin/convert "${basepath}uploads/${name}.${ext}" -resize ${img_xs} "${basepath}images/${name}_${name_xs}.${toext}"
  fi
  if [ "$img_sm" != "none" ]; then
    /usr/bin/convert "${basepath}uploads/${name}.${ext}" -resize ${img_sm} "${basepath}images/${name}_${name_sm}.${toext}"
  fi
  if [ "$img_md" != "none" ]; then
    /usr/bin/convert "${basepath}uploads/${name}.${ext}" -resize ${img_md} "${basepath}images/${name}_${name_md}.${toext}"
  fi
  if [ "$img_lg" != "none" ]; then
    /usr/bin/convert "${basepath}uploads/${name}.${ext}" -resize ${img_lg} "${basepath}images/${name}_${name_lg}.${toext}"
  fi
  if [ "$img_xl" != "none" ]; then
    /usr/bin/convert "${basepath}uploads/${name}.${ext}" -resize ${img_xl} "${basepath}images/${name}_${name_xl}.${toext}"
  fi

  # Converted?
  if [ "$ext" != "$toext" ]; then
    # Convert, copy & delete upload
    /usr/bin/convert "${basepath}uploads/${name}.${ext}" "${basepath}images/${name}.${toext}"
    /bin/cp "${basepath}images/${name}.${toext}" "${basepath}original/images/${name}.${toext}"
    /bin/rm "${basepath}uploads/${name}.${ext}"
  else
    # Copy & move upload
    /bin/cp "${basepath}uploads/${name}.${ext}" "${basepath}original/images/${name}.${toext}"
    /bin/mv "${basepath}uploads/${name}.${ext}" "${basepath}images/${name}.${toext}"
  fi

# SVG
elif [ "$ext" = "svg" ]; then

  # Move upload
  /bin/mv "${basepath}uploads/${name}.${ext}" "${basepath}images/${name}.${ext}"

  # Always create a thumbnail
  /usr/bin/convert -background none -resize ${thumb} "${basepath}images/${name}.${ext}" "${basepath}images/${name}_thumb_svg.png"

fi
