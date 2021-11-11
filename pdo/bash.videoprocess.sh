#!/bin/bash

# Instructions
## File must be correctly named and in /var/www/html/web/media/uploads/
## Directories must exist: /var/www/html/web/media/uploads /var/www/html/web/media/video /var/www/html/web/media/original/video

# How to use:
## bash.videoprocess.sh [file basename] [file extension]
# Eg:
## bash.videoprocess.sh file_name mp4

basepath="/var/www/html/web/media/"
name="$1"
ext="$2"

# Test for accepted types
if [ "$ext" != "webm" ] && [ "$ext" != "ogg" ] && [ "$ext" != "mp4" ] && [ "$ext" != "flv" ] && [ "$ext" != "avi" ] && [ "$ext" != "mkv" ] && [ "$ext" != "mov" ]; then
  exit 0; # Quiet exit, no need for STDERR
fi

# Get video dimensions & orientation
v_height=$(ffmpeg -i "${basepath}uploads/${name}.${ext}" 2>&1 | grep Video: | grep -Po '\d{3,5}x\d{3,5}' | cut -d'x' -f2)
v_width=$(ffmpeg -i "${basepath}uploads/${name}.${ext}" 2>&1 | grep Video: | grep -Po '\d{3,5}x\d{3,5}' | cut -d'x' -f1)
# ffmpeg requires height and width be divisible by 2 (divide by 2, the multiply by two; any remainder 1 will be lost)
v_height=$(expr $v_height / 2 \* 2 )
v_width=$(expr $v_width / 2 \* 2 )
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
  if [ "$v_width" -gt "960" ]; then
    v_scale="960:-1"
  else
    v_scale="$v_width:-1"
  fi
fi

# Convert flv, mkd, avi, or mov to mp4 in our process
if [ "$ext" = "flv" ] || [ "$ext" = "avi" ] || [ "$ext" = "mkv" ] || [ "$ext" = "mov" ]; then

  toext="mp4"

else

  toext="$ext"

fi

# Convert & backup large videos
if [ "$v_height" -gt "960" ] || [ "$v_width" -gt "960" ]; then
  /usr/bin/ffmpeg -y -i "${basepath}uploads/${name}.${ext}" -filter:v scale=${v_scale} -c:a copy "${basepath}video/${name}.${toext}"
  wait

# Keep original
  # Converted?
  if [ "$ext" != "$toext" ]; then
    # Convert and remove original
    /usr/bin/ffmpeg -y -i "${basepath}uploads/${name}.${ext}" -c:a copy "${basepath}original/video/${name}.${toext}"
    wait
    /bin/rm "${basepath}uploads/${name}.${ext}"

  else
    # Move original
    /bin/mv "${basepath}uploads/${name}.${ext}" "${basepath}original/video/${name}.${ext}"

  fi

# Just copy small videos
else

  # Converted?
  if [ "$ext" != "$toext" ]; then
    # Convert to new location & remove original
    /usr/bin/ffmpeg -y -i "${basepath}uploads/${name}.${ext}" -c:a copy "${basepath}video/${name}.${toext}"
    wait
    /bin/rm "${basepath}uploads/${name}.${ext}"

  else
    # Move original
    /bin/mv "${basepath}uploads/${name}.${ext}" "${basepath}video/${name}.${ext}"

  fi

fi
