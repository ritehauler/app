// nodejs vars
var nodejs_port = 5005;
// db vars
var db_port = 3306;

/*var db_host = 'localhost';
var db_user = 'root';
var db_password = '';
var db_database = 'mob_outdoors';*/
var db_host = 'cubixsource.com';
var db_user = 'likehoop_qauser';
var db_password = 'likehoopqa123';
var db_database = 'likehoop_qa';

/*var db_host = '198.20.103.178';
var db_user = 'devusr_apps1';
var db_password = 'dev_@#sdF543@3';
var db_database = 'dev_apps1';*/

// API credentials
var api_url   = "http://cubixsource.com/mobile/likehoop/api/";
//var api_email = "salman.khimani@cubixlabs.com";
var api_user  = "cubixapiuser"; 
var api_pass  = "apipass123";

var auth = "Basic " + new Buffer(api_user + ":" + api_pass).toString("base64");

// tables
var table_message = "message";

// frameworks / libs
var app = require('express')();
var mysql = require('mysql');
//var execPhp = require('exec-php');
var moment = require('moment');

// configs
var server = require('http').createServer(app);
var io = require('socket.io').listen(server);
server.listen(nodejs_port);

console.log("MSG FROM SERVER : In node");

var connection = null;

/*execPhp('index.php', function(error, php, outprint) {
	php.get_details(function(err, result, output, printed) {
		// nodejs details
		nodejs_port = result.nojejs.port;
		// db details
		db_host = result.db.host;
		db_user = result.db.user;
		db_password = result.db.password;
		db_database = result.db.db;
		db_port = result.db.port;
		//server.listen(nodejs_port, 'localhost');
	});
});*/

// prepare database connection
connection = mysql.createConnection({
    host: db_host,
    user: db_user,
    password: db_password,
    database: db_database
});

connection.connect();

// test query ends
connection.on('error', function(err) {
    console.log('db error', err);
	if (err.code === 'PROTOCOL_CONNECTION_LOST') { // Connection to the MySQL server is usually
		connection = mysql.createConnection({
			host: db_host,
			user: db_user,
			password: db_password,
			database: db_database
        });
        connection.connect();
        // lost due to either server restart, or a
	} else { // connnection idle timeout (the wait_timeout
		throw err; // server variable configures this)
    }
});

// keep connection alive
function keepalive() {
    connection.query('select 1', [], function(err, result) {
        if (err) return console.log(err);
        var mysql_time = new Date();
        var mysql_time = moment(mysql_time).format("YYYY-MM-DD HH:mm:ss Z");
        console.log('select 1 - ' + mysql_time);
        // Successul keepalive
    });
}
setInterval(keepalive, (1000 * 60) * 15);

// routing to view
app.get('/', function(req, res) {
    res.sendfile(__dirname + '/index.html');
});


// usernames which are currently connected to the chat
var usernames = {};
var user_ids = [];
// rooms which are currently available in chat
var rooms = ['room1'];
var connected_users = [];
var chatting_with = [];

// - FIX - Apache 2.2.25 issue : disconnecting client on connect
io.set("transports", ["xhr-polling", "jsonp-polling"]);
// socket io
io.sockets.on('connection', function(socket) {
	console.log("MSG FROM SERVER : CONNECTED");
    // when the client emits 'adduser', this listens and executes
    socket.on('adduser', function(user_data) {
		console.log("in adduser");
		// disconnect user from socket if already exists
		if (connected_users[user_data.user_id]) {
            console.log(' user_id : ' + user_data.user_name + ' <- User is already connected');
			
			// send "kick" to old socket
			connected_users[user_data.user_id].emit('kick_user', {user_id : user_data.user_id}); //  for devices
			
			// older socket should leave room
			connected_users[user_data.user_id].leave(connected_users[user_data.user_id].room);
			
			// remove old socket chatting with
			delete chatting_with[user_data.user_id];
			
			// disconnect client
			connected_users[user_data.user_id].disconnect();
			
            // update list of users in chat, client-side
            // io.sockets.emit('updateusers', user_ids);
        }
		else {
			// add in online user ids
			user_ids.push(user_data.user_id);
		}
		//else {
            // store the username in the socket session for this client
            socket.user_id = user_data.user_id;
            // store the room name in the socket session for this client
            socket.room = 'room1';
            // add the client's username to the global list
            usernames[user_data.user_id] = user_data.user_name;
            //??usernames123.push(username);
            connected_users[user_data.user_id] = socket;
			

            // send client to room 1
            socket.join('room1');
            //??console.log(usernames123);
			
			// by SK starts
			// echo to client they've connected
			//socket.emit('updatechat', {sender_id : "", message : 'you are connected'}); - for only browsers
			socket.emit('useradded', {user_id : user_data.user_id}); //  for devices
			// echo globally (all clients) that a person has connected
			//socket.broadcast.emit('updatechat', {sender_id : "", message : user_data.user_name + ' has joined'}); - browser only
			// update the list of users in chat, client-side
			io.sockets.emit('updateusers', unique(user_ids));
			// by SK ends
        //}

       

        // echo to client they've connected
        //socket.emit('updatechat', 'SERVER', 'you have connected to room1');
        //console.log("usernames"+usernames.username);
        // echo to room 1 that a person has connected to their room
        //socket.broadcast.to('room1').emit('updatechat', 'SERVER', username);
        // socket.emit('updaterooms', rooms, 'room1', user_data.user_name); // - commented by SK
        //socket.emit('updaterooms',usernames123);
		
		 console.log('MSG FROM SERVER : Socket Connected')
    });

    // when the client emits 'sendchat', this listens and executes
    socket.on('sendchat', function(data) {
		console.log("SEND CHAT : ", data);
        // we tell the client to execute 'updatechat' with 2 parameters
        //??io.sockets.in(socket.room).emit('updatechat', socket.username, data);
        //??var message_type = data.type;
        var sender_id = socket.user_id;
        var receiver_id = data.receiver_id;
        var sender_name = usernames[socket.user_id];
        var msg = (data.message) ? (data.message) : "";
        //var image = (data.image) ? data.image : "";
		var msg_type = (data.message_type) ? data.message_type : 2;
		var data_packet = (data.data_packet) ? (data.data_packet) : "";
		var is_unread = 1;
		
		console.log("SAL TEST", data.receiver_id);
		
        //--
        var now = new Date();
        //??var time = moment(now).format("YYYY-MM-DD HH:mm:ss Z");
        var time = moment.utc(now).format("YYYY-MM-DD HH:mm:ss");
		//var time = time = Math.floor(moment.utc(now) / 1000);
		
		// check if receiver is chatting with current user, mark is_unread = 0
		/*if(typeof chatting_with[receiver_id] !== 'undefined') {
			//is_unread = chatting_with[receiver_id] == socket.user_id ? 0 : 1;
			is_unread = 0;
		}*/
		
		if(typeof chatting_with[receiver_id] !== 'undefined') {
			is_unread = chatting_with[receiver_id] == socket.user_id ? 0 : 1;
		}
		
		console.log("params", data);
		

        //--
        var response_data = {
            "sender_id": sender_id,
            "receiver_id": receiver_id,
            "sender_name": sender_name,
            "message": msg,
			"message_id": 0,
            //"image": image,
			"is_unread" : is_unread,
			"message_type" : msg_type,
			"data_packet" : data_packet,
            "created_at": time
        };

		/*
        if (connected_users[receiver_id]) {
            connected_users[receiver_id].emit('updatechat', response_data);
        }

        if (connected_users[sender_id]) {
            connected_users[sender_id].emit('updatechat', response_data);
        } else {
            console.log('ERROR : SENDER NOT FOUND <-SHOAIB');
            //--
            return;
        }
		*/
		
		var sql = "SELECT user_block_id FROM user_block WHERE user_id = '"+receiver_id+"' AND target_user_id = '"+sender_id+"' AND deleted_at IS NULL;";
 
		connection.query(sql, function(err, rows, fields) {
			if (err) throw err;
		 
			/*for (var i in rows) {
				console.log('Post Titles: ', rows[i].post_title);
			}*/
			// if user is blocked
			if(rows.length > 0) {
				// msg to sender
				/*if (connected_users[sender_id]) {
					connected_users[sender_id].emit('updatechat', response_data);
				} else {
					console.log('ERROR : SENDER NOT FOUND <-SHOAIB');
					return;
				}*/
			}
			else if(parseInt(receiver_id) == 0 || parseInt(sender_id) == 0) {
				// nothing
			}
			else {
				// save message start
				var mysql_data = {
					sender_id : sender_id,
					receiver_id : receiver_id,
					message : msg,
					is_unread : is_unread,
					message_type : msg_type,
					data_packet : data_packet,
					created_at : time
				};
				
				var sql = 'INSERT INTO ' + table_message + ' SET ?';
				console.log("SQL : ", sql);
				
				connection.query(sql, mysql_data, function(err, rows) {
					if (err) {
						//throw err;
						console.log("MYSQL ERR : ", err);
					} else {
						
						// add last id to response
						response_data.message_id = rows.insertId;
						// message sent, emit now
						// - to receiver
						if (connected_users[receiver_id]) {
							console.log("UPDATE CHAT to RECEIVER", response_data);
							connected_users[receiver_id].emit('updatechat', response_data);
						}
						
						// - to sender
						if (connected_users[sender_id]) {
							console.log("UPDATE CHAT to SENDER", response_data);
							connected_users[sender_id].emit('updatechat', response_data);
						} else {
							console.log('ERROR : SENDER NOT FOUND <-SHOAIB');
							return;
						}
						
						
						// send push start
						if(is_unread == 1) {
						   
							var request = require('request');
							console.log("API CALL URL : ",api_url+'message/sendNotification');
							var options = {
								uri : api_url+'message/sendNotification',
								method : 'POST',
								/*headers: {
									'api_email': api_email,
									'api_password' : api_pass
								},*/
								headers : { 
									"Authorization" : auth 
								},
								form : {
									user_id: sender_id,
									target_user_id: receiver_id,
									message : msg,
									time : time,
									is_unread : is_unread,
									message_id : response_data.message_id
								}
							}; 
							var res = '';
							request(options, function (error, response, body) {
								//console.log("Request Error", error);
								//console.log("Request Response", response);
								if (!error && response.statusCode == 200) {
									res = body;
								}
								else {
									res = 'Not Found';
								}
								console.log("response", res);
							});
						}
						// send push end
		
					}
				});
				// save message end
			}
		});

    });
	
	
	// set which receiver user is chatting with
    socket.on('on_screen', function(data) {
		console.log("ON SCCREEN: ", data);
        chatting_with[socket.user_id] = data.receiver_id;
    });
	
	socket.on('off_screen', function(data) {
		console.log("OFF SCCREEN: ", data);
        //delete chatting_with[socket.user_id];
		//delete chatting_with[socket.receiver_id];
		chatting_with[socket.user_id] = 0;
    });

    /*socket.on('switchRoom', function(newroom){
		// leave the current room (stored in session)
		socket.leave(socket.room);
		// join new room, received as function parameter
		socket.join(newroom);
		socket.emit('updatechat', 'SERVER', 'you have connected to '+ newroom);
		// sent message to OLD room
		socket.broadcast.to(socket.room).emit('updatechat', 'SERVER', socket.username+' has left this room');
		// update socket session room title
		socket.room = newroom;
		socket.broadcast.to(newroom).emit('updatechat', 'SERVER', socket.username+' has joined this room');
		socket.emit('updaterooms', rooms, newroom);
	});*/

    // when the user disconnects.. perform this
    socket.on('disconnect', function() {
		
		console.log("MSG FROM SERVER : BYE BYE");

        if (connected_users[socket.user_id]) {
            console.log('delete user : ' + usernames[socket.user_id]);
            // remove the username from global usernames list
            delete usernames[socket.user_id];
            //??usernames123.pop(socket.username);

            delete connected_users[socket.user_id];
			
			// remove chatting with
			delete chatting_with[socket.user_id];
			
			// remove from online user ids
			var ind = user_ids.indexOf(socket.user_id);
			if (ind > -1) { user_ids.splice(ind, 1); }

			socket.emit('userremoved', {user_id : socket.user_id}); // for device
			
            // update list of users in chat, client-side
            io.sockets.emit('updateusers', unique(user_ids));
            // echo globally that this client has left
            //socket.broadcast.emit('updatechat', {sender_id : "", message : socket.user_id + ' has left'}); // browser only
			
			
            socket.leave(socket.room);
        }
    });
	
});


function unique(a) {
    var seen = {};
    var out = [];
    var len = a.length;
    var j = 0;
    for(var i = 0; i < len; i++) {
         var item = a[i];
         if(seen[item] !== 1) {
               seen[item] = 1;
               out[j++] = item;
         }
    }
    return out;
}
