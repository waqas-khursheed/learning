// Node.js vs Express(Server + API)

// |Feature | Node.js(http) | Express |
// | -------------- | -------------------------------------- | ------------------------- |
// | Server | `http.createServer` | `app.listen` |
// | Routing | Manual check(`req.url`, `req.method`) | `app.get`, `app.post` |
// | Body Parsing | Manual(`req.on('data')`) | `express.json()` |
// | Middleware | Custom | Built -in + custom |
// | Boilerplate | Zyada | Kam |
// | Learning Curve | Basic | Easy if Node.js samajh ho |
