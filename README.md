```bash
cd scrape-runner/
docker build -t stickee/ai-scrape-demo-2-runner .

cd ..
docker run --rm -p6080:6080 -p3000:3000 -v $(pwd)://app stickee/ai-scrape-demo-2-runner

nvm use
npm run dev
```
