#!/bin/bash

# Instructions
## File must be correctly named and in /srv/www/html/web/media/uploads/
## Directories must exist: /srv/www/html/web/media/uploads /srv/www/html/web/media/docs /srv/www/html/web/media/original/docs

# How to use:
## bash.documprocess.sh [file basename] [file extension] [output extension]
# Eg:
## bash.documprocess.sh file_name docx pdf

basepath="/srv/www/html/web/media/"
name="$1"
ext="$2"
out="$3"

# Test for accepted types, redundant and secure
if [ "$ext" != "txt" ] && [ "$ext" != "md" ] && [ "$ext" != "htm" ] && [ "$ext" != "html" ] && [ "$ext" != "doc" ] && [ "$ext" != "docx" ] && [ "$ext" != "odt" ] && [ "$ext" != "pdf" ]; then
  exit 0; # Quiet exit, no need for STDERR
fi

# Cannot convert a type to itself
if [ "$out" != "$ext" ] && [ "$out" != "txt" ] && [ "$ext" != "txt" ] && [ "$out" != "doc" ] && [ "$ext" != "doc" ]; then
  /usr/bin/pandoc -s "${basepath}uploads/${name}.${ext}" -o "${basepath}docs/${name}.${out}"
  wait
elif [ "$ext" = "txt" ] || [ "$ext" = "doc" ]; then
  /bin/cp "${basepath}uploads/${name}.${ext}" "${basepath}docs/${name}.${ext}"
fi

# Move original
/bin/mv "${basepath}uploads/${name}.${ext}" "${basepath}original/docs/${name}.${ext}"
