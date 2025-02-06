const http = require('http');
// const { Monitor } = require('node-screenshots');
const { spawn } = require('child_process');
const path = require('path');
const fs = require('fs');

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
    } else if (req.method === 'GET' && req.url.startsWith('/execute')) {
        try {
            const cmd = new URL(`http://${req.headers.host}${req.url}`).searchParams.get('command');

            if (!cmd) {
                res.writeHead(400, { 'Content-Type': 'text/plain' });

                return res.end('Missing command parameter');
            }

            const child = spawn(cmd, { shell: true });

            let stdout = '';
            child.stdout.on('data', data => {
                console.log(`stdout: ${data}`);
                stdout += data;
            });

            let stderr = '';
            child.stderr.on('data', data => {
                console.error(`stderr: ${data}`);
                stderr += data;
            });

            child.on('close', exitCode => {
                console.log(`child process exited with code ${exitCode}`);
                res.writeHead(200, { 'Content-Type': 'application/json' });
                res.end(JSON.stringify({ exitCode, stdout, stderr }));
            });
        } catch (err) {
            console.error(err);
            res.writeHead(500, { 'Content-Type': 'text/plain' });

            return res.end('Error executing command');
        }
    }  else if (req.method === 'GET' && req.url.startsWith('/get-recording')) {
        try {
            const filePath = '/tmp/recording.webm';
            const fileName = path.basename(filePath);

            res.writeHead(200, {
                'Content-Disposition': `attachment; filename="${fileName}"`,
                'Content-Type': 'application/octet-stream'
            });

            const fileStream = fs.createReadStream(filePath);
            fileStream.pipe(res);
        } catch (err) {
            console.error(err);
            res.writeHead(500, { 'Content-Type': 'text/plain' });

            return res.end('Error sending file');
        }
    } else {
        res.writeHead(404, { 'Content-Type': 'text/plain' });
        res.end('Not Found');
    }
}).listen(3000, () => {
    console.log('Server listening on http://localhost:3000');
});
