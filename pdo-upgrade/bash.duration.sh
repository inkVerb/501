#!/bin/bash

# Instructions
## File must be correctly named and in ./media/uploads/
## Directories must exist: ./media/uploads ./media/audio ./media/original/audio

# How to use:
## bash.duration.sh [file basename] [file extension]
# Eg:
## bash.duration.sh file_name ogg

basepath="./media/"
name="$1"
ext="$2"

# Test for accepted types, redundant and secure
if [ "$ext" == "mp3" ] || [ "$ext" == "ogg" ] || [ "$ext" == "wav" ] || [ "$ext" == "flac" ]; then
  media_type="audio"
elif  [ "$ext" == "webm" ] || [ "$ext" == "mp4" ] || [ "$ext" == "flv" ] || [ "$ext" == "avi" ] || [ "$ext" == "mkv" ] || [ "$ext" == "mov" ]; then
  media_type="video"
else
  exit 0; # Quiet exit, no need for STDERR
fi

# Run the probe
ffprobe -i "${basepath}${media_type}/${name}.${ext}" -show_entries format=duration -v quiet -of csv="p=0" -sexagesimal | cut -f1 -d '.'
