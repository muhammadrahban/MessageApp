var app = require('express')();
var http = require('http').Server(app);
var io = require('socket.io')(http, {
    cors: { origin: "*"}
});
var users = [];

http.listen(8005, function () {
    console.log('Listening to port 8005');
});
io.on('connection', function (socket) {
    socket.on("user_connected", function (user_id) {
        users[user_id] = socket.id;
        io.emit('updateUserStatus', users);
        console.log("user connected "+ user_id);
    });

    socket.on("sendChatToServer", function (user_id, msg) {
        socket.broadcast.emit('sendChatToClient', user_id, msg);
        console.log("user connected send chat "+ user_id);
    });

    socket.on('disconnect', function() {
        var i = users.indexOf(socket.id);
        users.splice(i, 1, 0);
        io.emit('updateUserStatus', users);
        console.log(users);
    });
});
