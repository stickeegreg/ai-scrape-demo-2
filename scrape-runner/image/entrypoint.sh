#!/bin/bash
set -euo pipefail

./start_all.sh
./novnc_startup.sh

#!/bin/bash
echo "starting screenshot-service"

cd $HOME/screenshot-service
node index.js > /tmp/screenshot-service.log 2>&1 &

echo "Screenshot service is now ready on port 3000"

# Keep the container running
tail -f /dev/null



# #!/bin/bash
# set -e

# ./start_all.sh
# ./novnc_startup.sh

# python http_server.py > /tmp/server_logs.txt 2>&1 &

# STREAMLIT_SERVER_PORT=8501 python -m streamlit run computer_use_demo/streamlit.py > /tmp/streamlit_stdout.log &

# echo "✨ Computer Use Demo is ready!"
# echo "➡️  Open http://localhost:8080 in your browser to begin"

# # Keep the container running
# tail -f /dev/null
