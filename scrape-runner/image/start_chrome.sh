#!/bin/bash

export DISPLAY=:${DISPLAY_NUM}

nohup google-chrome-stable \
    --disable-dev-shm-usage \
    --disable-gpu \
    --disable-software-rasterizer \
    --no-first-run \
    --no-default-browser-check \
    --remote-debugging-port=9222 \
    --disable-background-networking \
    --disable-background-timer-throttling \
    --disable-backgrounding-occluded-windows \
    --disable-client-side-phishing-detection \
    --disable-crash-reporter \
    --disable-features=Translate,MediaRouter \
    --mute-audio \
    --disable-sync \
    --load-extension=/home/stickee/hatchery \
    "$1" \
    > /tmp/chrome.log 2>&1 \
    &
