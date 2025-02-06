#!/bin/bash

export DISPLAY=:${DISPLAY_NUM}

nohup google-chrome-stable \
    --no-sandbox \
    --disable-dev-shm-usage \
    --disable-gpu \
    --disable-software-rasterizer \
    --disable-extensions \
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
    "$1" \
    > /tmp/chrome.log 2>&1 \
    &
