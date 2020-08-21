#!/bin/bash

basepath="/var/www/html/web/media/"
name="$1"
ext="$2"

# Get video dimensions & orientation
v_height=$(ffmpeg -i "${basepath}uploads/${name}.${ext}" 2>&1 | grep Video: | grep -Po '\d{3,5}x\d{3,5}' | cut -d'x' -f2)
v_width=$(ffmpeg -i "${basepath}uploads/${name}.${ext}" 2>&1 | grep Video: | grep -Po '\d{3,5}x\d{3,5}' | cut -d'x' -f1)
# 960 maximum size, ideal for web, anything larger use a CDN
if [ "$v_height" -gt "$v_width" ]; then
  # Don't up-scale a small video
  if [ "$v_height" -gt "960" ]; then
    v_scale="-1:960"
  else
    v_scale="-1:$v_height"
  fi
else
  # Don't up-scale a small video
  if [ "$v_height" -gt "960" ]; then
    v_scale="960:-1"
  else
    v_scale="$v_height:-1"
  fi
fi

# Convert flv, mkd, or avi to mp4 in our process
if [ "$2" = "flv" ] || [ "$2" = "avi" ] || [ "$2" = "mkv" ] || [ "$2" = "mov" ]; then

  /usr/bin/ffmpeg -y -i "${basepath}uploads/${name}.${ext}" -filter:v scale=${v_scale} -c:a copy "${basepath}video/${name}.mp4"
  wait
  # Move and remove original
  /usr/bin/ffmpeg -y -i "${basepath}uploads/${name}.${ext}" -c:a copy "${basepath}original/video/${name}.mp4"
  wait
  /bin/rm "${basepath}uploads/${name}.${ext}"

# Process normal videos
else

  # Test for accepted types, redundant and secure
  if [ "$2" != "webm" ] && [ "$2" != "ogg" ] && [ "$2" != "mp4" ]; then
    exit 0; # Quiet exit, no need for STDERR
  fi

  /usr/bin/ffmpeg -y -i "${basepath}uploads/${name}.${ext}" -filter:v scale=${v_scale} -c:a copy "${basepath}video/${name}.${ext}"
  wait
  # Move original
  /bin/mv "${basepath}uploads/${name}.${ext}" "${basepath}original/video/${name}.${ext}"

fi
