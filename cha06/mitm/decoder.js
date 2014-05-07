#!/usr/bin/env node

var util = require('util');
var crypto = require('crypto');

	var client_prime = '9fa2616b3b8294db91c531289f220eb5342779a03477448397da35540c2a506b';
	var client_key = '88e248001b67adeb6394fb64146c2dc7102c0b30ea20fdeecb03303dc44e76c9';
	var client_keyphrase = '';
	var server_key = '74284a5be6f83176fb44a13b5e608e2b79689caf888e5d93f0b3a947d9678c44';

	var client = crypto.createDiffieHellman(client_prime,'hex');
	client.generateKeys();
	client.setPublicKey(client_key,'hex');
	secret = client.computeSecret(server_key,'hex');
	var cipher = crypto.createCipheriv('aes-256-ecb',secret,'');
	var decipher = crypto.createDecipheriv('aes-256-ecb',secret,'');

	var keyphrase = cipher.update('JungleGreenAppropriateMonkeyIsEfficient','utf8','hex')+cipher.final('hex');
	var string = decipher.update(keyphrase,'hex','utf8')+decipher.final('utf8');
console.log(keyphrase);
	//keyphrase = decipher.update('140673d371cf1ccfbcbaf35fb6fe7b568f31cf7b4751528bce1f2ab4afbbe4a9','hex','utf8')+decipher.final('utf8');

/*
	var server = crypto.createDiffieHellman(client_prime,'hex');
	server.generateKeys();
	var server_key = server.getPublicKey('hex');
	var server_prime = server.getPrime('hex');
//console.log(server_key);
//console.log(server_prime);

	var client = crypto.createDiffieHellman(client_prime,'hex');
	client.generateKeys();
	var secret2 = client.computeSecret(server_key,'hex');
	var cipher = crypto.createCipheriv('aes-256-ecb',secret2,'');
	var keyphrase = cipher.update('hola','utf8','hex')+cipher.final('hex');
	var decipher = crypto.createDecipheriv('aes-256-ecb',secret2,'');
	keyphrase = decipher.update(keyphrase,'hex','utf8')+decipher.final('utf8');
console.log(keyphrase);
*/
