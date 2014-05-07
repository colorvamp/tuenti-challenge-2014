#!/usr/bin/env node

	/* Challenge 16 - ÑAPA
	 * by Marcos Fernández (sombra2eternity@gmail.com)
	 * Using my own impl of quadtree: https://github.com/sombra2eternity/quadtree
	 */

	/* INI-input data */
	var whitespace = [' ','\n','\r','\t']
	var Input = function(){this.data = require('fs').readFileSync('/dev/stdin').toString('utf8');this.pos = 0;}
	Input.prototype.read = function(){
		var s = '';
		if(this.pos >= this.data.length){return false;}
		while(whitespace.indexOf(this.data.charAt(this.pos)) >= 0){this.pos++;}
		while(this.pos < this.data.length && whitespace.indexOf(this.data.charAt(this.pos)) < 0){ s += this.data.charAt(this.pos++);}
		return s;
	}
	var input = new Input();
	var params = '';
	if(str = input.read()){params = str;}
	params = params.split(',');
	/* END-input data */


	var quadtree = require('./quadtree.js');
	var fs = require('fs'),
	    readline = require('readline'),
	    stream = require('stream');

	var instream = fs.createReadStream('points');
	var outstream = new stream;
	outstream.readable = true;
	outstream.writable = true;
	var min = parseInt(params[0])-1;
	var max = min+parseInt(params[1])-1;

	var objects = {};
	var objectsCount = 0;
	var q = new quadtree._quadtree(100000,100000);
	var collisions = {};
	var collisionsCount = 0;
	var processed = {};

	var rl = readline.createInterface({input:instream,output:outstream,terminal:false});
	var lines = -1;
	rl.on('line',function(line){
		lines++;
		if(lines < min){return;}
		if(lines > max){rl.close();return;}
		line = line.replace(/[ ]+/g,',');
		line = line.split(',');
		if(line.length < 3){return;}
		var r = parseInt(line[3]);
		var x = parseInt(line[1]);
		var y = parseInt(line[2]);
		objects[lines] = {'x':x-r,'y':y-r,'w':r*2,'h':r*2,'ix':x,'iy':y,'ir':r,'l':lines};
		q.insert(objects[lines]);
		objectsCount++;
		//console.log(line);
		//process.exit(1);
	});
	rl.on('close',function(){
		//console.log(objects);return;
		var c = 0;
		for(l in objects){
			c++;if((c % 500) == 0){console.log(c);}
			//if((c % 5000) == 0){process.exit(1);}

			var h = objects[l];
			var o = q.retrieve(h);
			for(i in o){
				if(o[i]['l'] == l){continue;}
				var tmp = [l,o[i]['l']].sort().join('.');

				if(processed[tmp]){continue;}
				processed[tmp] = 1;

				if(collisions[tmp]){continue;}

				var r = collide2(h,o[i]);
				if(r){
					collisions[tmp] = 1;
					collisionsCount++;
					//console.log(tmp);
					//console.log(o[i]);
				}
				//console.log(r);
			}
		}
		//console.log(o);
		console.log(collisionsCount);
	});

	/* Functions */
	function circleToSquare(c){
		return {'x':c.x-c.r,'y':c.y-c.r,'w':c.r*2,'h':c.r*2,'ix':c.x,'iy':c.y,'ir':c.r};
	}
	function collide2(p1,p2){
		/* (x2-x1)^2 + (y1-y2)^2 < (r1+r2)^2 */
		return Math.pow((p2['ix']-p1['ix']),2) + Math.pow((p1['iy']-p2['iy']),2) < Math.pow((p1['ir']+p2['ir']),2)
	}
	function collide(p1,p2){
		/* (x2-x1)^2 + (y1-y2)^2 <= (r1+r2)^2 */
		return Math.pow((p2['x']-p1['x']),2) + Math.pow((p1['y']-p2['y']),2) <= Math.pow((p1['r']+p2['r']),2)
	}
