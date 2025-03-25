# AI Scrape Demo 2

## Setup

Set up the Laravel app as normal:

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan storage:link

nvm use
npm install
npm run dev
```

Build the scrape-runner docker container and run one or more copies of it.
Add the scrape-runner container(s) to the .env

```bash
cd scrape-runner/
docker build -t stickee/ai-scrape-demo-2-runner .

cd ..
docker run --rm -p6080:6080 -p3000:3000 -v $(pwd)://app -d stickee/ai-scrape-demo-2-runner

# Scrapes run via the web UI go into the queue
php artisan queue:work

# Run scrapes from the command line to skip the queue
php artisan app:run-scrape 1
```
