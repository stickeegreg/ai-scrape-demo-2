const http = require('http');
// const { Monitor } = require('node-screenshots');
const { spawn } = require('child_process');

// let monitor = Monitor.fromPoint(100, 100);
// console.log(monitor, monitor.id);

http.createServer((req, res) => {
  if (req.method === 'GET' && req.url === '/screenshot-desktop') {
    try {
        const cmd = 'xwd -root -display :1 | convert xwd:- png:-';
        const child = spawn(cmd, { shell: true });

        res.writeHead(200, { 'Content-Type': 'image/png' });
        child.stdout.pipe(res);

        child.stderr.on('data', data => {
            console.error(`stderr: ${data}`);
        });

        child.on('error', err => {
            console.error('Process error:', err);
            res.writeHead(500, { 'Content-Type': 'text/plain' });
            res.end('Error taking screenshot');
        });

        // let image = monitor.captureImageSync();
        // let png = image.toPngSync();
        // res.writeHead(200, { 'Content-Type': 'image/png' });
        // res.end(png, 'binary');
    } catch (err) {
        console.error(err);
        res.writeHead(500, { 'Content-Type': 'text/plain' });
        return res.end('Error taking screenshot');
    }
  } else {
    res.writeHead(404, { 'Content-Type': 'text/plain' });
    res.end('Not Found');
  }
}).listen(3000, () => {
  console.log('Server listening on http://localhost:3000');
});
