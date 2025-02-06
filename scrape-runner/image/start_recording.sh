#!/bin/bash

rm -f /tmp/ffmpeg_recording.pid /tmp/recording.log /tmp/recording.webm

# Start FFmpeg in the background
nohup ffmpeg -f x11grab -s ${WIDTH}x${HEIGHT} -r 25 -i :${DISPLAY_NUM}.0 -c:v libvpx-vp9 -b:v 100k -crf 30 -c:a libopus /tmp/recording.webm > /tmp/recording.log 2>&1 &

# Save the PID of the background process
echo $! > /tmp/ffmpeg_recording.pid

echo "Recording started. PID: $(cat /tmp/ffmpeg_recording.pid)"
