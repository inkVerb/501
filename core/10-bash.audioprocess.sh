#!/bin/bash

# Instructions
## File must be correctly named and in ./media/uploads/
## Directories must exist: ./media/uploads ./media/audio ./media/original/audio

# How to use:
## bash.audioprocess.sh [file basename] [file extension]
# Eg:
## bash.audioprocess.sh file_name ogg

basepath="./media/"
name="$1"
ext="$2"

# Test for accepted types, redundant and secure
if [ "$ext" != "mp3" ] && [ "$ext" != "ogg" ] && [ "$ext" != "wav" ] && [ "$ext" != "flac" ]; then
  exit 0; # Quiet exit, no need for STDERR
fi

# Process accepted audio for podcasting
/usr/bin/ffmpeg -i "${basepath}uploads/${name}.${ext}" -map_metadata 0 -acodec libmp3lame -vn -ar 44100 -ac 1 -b:a 96k "${basepath}audio/${name}.mp3"
wait
# Move original
/bin/mv "${basepath}uploads/${name}.${ext}" "${basepath}original/audio/${name}.${ext}"
