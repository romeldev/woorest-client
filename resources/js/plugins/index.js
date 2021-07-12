const req = require.context('./modules', true, /\.js$/);

req.keys().forEach(file => {
    req(file);
})