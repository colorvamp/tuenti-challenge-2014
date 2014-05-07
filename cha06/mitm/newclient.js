#!/usr/bin/env node

if ((process.version.split('.')[1]|0) < 10) {
	console.log('Please, upgrade your node version to 0.10+');
	process.exit();
}

var params = {};
process.argv.forEach(function(val,index,array){params[index] = val;});
return;

var net = require('net');
var util = require('util');
var crypto = require('crypto');

var options = {
	'port': 6767,
	'host': '0.0.0.0',
}
var options = {
	'port': 6969,
	'host': '54.83.207.90',
}

const KEYPHRASE = params[2];

var dh, secret, state, keyp = 0;

var socket = net.connect(options, function() {
	socket.write('hello?');
	state++;
});

socket.on('data',function(data){

	//console.log(state);
	var source = data.toString().substr(0,6);
	data = data.toString().trim().substr(15).split('|');

	switch(true){
		case (source == 'CLIENT' && data[0] == 'hello?'):
			socket.write('hello!');
			break;
		case (source == 'SERVER' && data[0] == 'hello!'):
			dh = crypto.createDiffieHellman(256);
			dh.generateKeys();
			socket.write(util.format('key|%s|%s\n', dh.getPrime('hex'), dh.getPublicKey('hex')));
			break;
		case (source == 'CLIENT' && data[0] == 'key'):
			
			break;
		case (source == 'SERVER' && data[0] == 'key'):
			socket.write(util.format('key|%s\n',data[1]));
			secret = dh.computeSecret(data[1],'hex');
			cipher = crypto.createCipheriv('aes-256-ecb',secret, '');
			keyp = cipher.update(KEYPHRASE, 'utf8', 'hex') + cipher.final('hex');
			break;
		case (source == 'CLIENT' && data[0] == 'keyphrase'):
			socket.write(util.format('keyphrase|%s\n',keyp));
			break;
		case (source == 'SERVER' && data[0] == 'result'):
			decipher = crypto.createDecipheriv('aes-256-ecb', secret, '');
			message = decipher.update(data[1], 'hex', 'utf8') + decipher.final('utf8');
			console.log(message);
			socket.end();
			break;
		default:
			console.log(source);
			console.log('   -> '+data.toString().trim());
			break;
	}
});
