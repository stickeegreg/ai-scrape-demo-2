#!/bin/bash

# Start FFmpeg in the background
nohup ffmpeg -f x11grab -s ${WIDTH}x${HEIGHT} -r 25 -i :${DISPLAY_NUM}.0 -qscale 20 recording.mpg > /tmp/recording.log 2>&1 &

# Save the PID of the background process
echo $! > /tmp/ffmpeg_recording.pid

echo "Recording started. PID: $(cat /tmp/ffmpeg_recording.pid)"
