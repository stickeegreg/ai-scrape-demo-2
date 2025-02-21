```bash
cd scrape-runner/
docker build -t stickee/ai-scrape-demo-2-runner .

cd ..
docker run --rm -p6080:6080 -p3000:3000 -v $(pwd)://app stickee/ai-scrape-demo-2-runner

nvm use
npm run dev

# Scrapes run via the web UI go into the queue
php artisan queue:work

# Run scrapes from the command line to skip the queue
php artisan scrape:run 1
```
