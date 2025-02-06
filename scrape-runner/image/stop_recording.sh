#!/bin/bash

# Check if the PID file exists
if [ -f /tmp/ffmpeg_recording.pid ]; then
    PID=$(cat /tmp/ffmpeg_recording.pid)
    echo "Stopping recording (PID: $PID)..."

    # Kill the FFmpeg process
    kill $PID

    # Remove the PID file
    rm /tmp/ffmpeg_recording.pid
else
    echo "No recording found!"
fi
