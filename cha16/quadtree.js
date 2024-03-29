function _quadtree(w,h,t,l,d){
	this.width = w;
	this.height = h;
	this.top = t || 0;
	this.left = l || 0;
	this.spaces = [];
	this.childs = [];
	this.depth = d || 0;
};
_quadtree.prototype.maxChilds = 10;
_quadtree.prototype.maxDepth = 10;
_quadtree.prototype.root = null;
_quadtree.prototype.getWidth = function(){return this.width;};
_quadtree.prototype.getHeight = function(){return this.height;};
_quadtree.prototype.getTop = function(){return this.top;};
_quadtree.prototype.getLeft = function(){return this.left;};
_quadtree.prototype.isPointInside = function(p){if(p.x > this.left-1 && p.x < (this.left+this.width+1) && p.y > this.top-1 && p.y < (this.top+this.height+1)){return true;}return false;};
_quadtree.prototype.isSquareInside = function(x,y,w,h){if(x > this.left-1 && x+w < (this.left+this.width+1) && y > this.top-1 && y+h < (this.top+this.height+1)){return true;}return false;};
_quadtree.prototype.isPointInsideElement = function(p,item){if(p.x > item.x-1 && p.x < (item.x+item.w+1) && p.y > item.y-1 && p.y < (item.y+item.h+1)){return true;}return false;};
_quadtree.prototype.getChilds = function(){
	if(!this.spaces.length){return this.childs;}
	var childs = [];
	for(i in this.spaces){
		var ch = this.spaces[i].getChilds();
		childs = childs.concat(ch);
	}
	return childs;
};
_quadtree.prototype.insert = function(item){
	if(this.spaces.length){
		var x1 = {x:item.x,y:item.y};
		var x2 = {x:item.x+item.w,y:item.y};
		var x3 = {x:item.x,y:item.y+item.h};
		var x4 = {x:item.x+item.w,y:item.y+item.h};
		for(i in this.spaces){
			if(this.spaces[i].isPointInside(x1) || this.spaces[i].isPointInside(x2) || this.spaces[i].isPointInside(x3)  || this.spaces[i].isPointInside(x4)){this.spaces[i].insert(item);}
		}
		return true;
	}

	this.childs.push(item);
	/* Split if necesary, always after insert the nodes */
	if(this.childs.length > this.maxChilds){this.split();}
};
_quadtree.prototype.split = function(){
	var d = this.depth+1;if(d > this.maxDepth){return false;}
	var halfWidth = this.width/2;
	var halfHeight = this.height/2;
	this.spaces.push(new _quadtree(halfWidth,halfHeight,this.top,this.left,d));
	this.spaces.push(new _quadtree(halfWidth,halfHeight,this.top,this.left+halfWidth,d));
	this.spaces.push(new _quadtree(halfWidth,halfHeight,this.top+halfHeight,this.left,d));
	this.spaces.push(new _quadtree(halfWidth,halfHeight,this.top+halfHeight,this.left+halfWidth,d));

	do{
		var item = this.childs.shift();
		var x1 = {x:item.x,y:item.y};
		var x2 = {x:item.x+item.w,y:item.y};
		var x3 = {x:item.x,y:item.y+item.h};
		var x4 = {x:item.x+item.w,y:item.y+item.h};
		for(i in this.spaces){
			if(this.spaces[i].isPointInside(x1) || this.spaces[i].isPointInside(x2) || this.spaces[i].isPointInside(x3)  || this.spaces[i].isPointInside(x4)){this.spaces[i].insert(item);}
		}
	}while(this.childs.length);
};
/*   
 *      (collision zone)    In order to detect if a collision zone affects a space, we could
 *        \                 check if there is almost a point of every square inside the other
 *    o---o---o---o
 *    |           |          o---o---o
 *    |   o---o   |          |       |
 *    o---|///|---o          o   o---o---o
 *        o---o              |   |   |   |
 *          \                o---o---o   o
 *        (space)                |       |
 *                               o---o---o
 */
_quadtree.prototype.retrieve = function(item){
	//FIXME: should identify or index childrens to avoid duplicates
	if(this.spaces.length){
		/* If the square we are checking match inside a child, we down a level */
		for(i in this.spaces){if(this.spaces[i].isSquareInside(item.x,item.y,item.w,item.h)){return this.spaces[i].retrieve(item);}}
		/* Now we need to check which childs are affected, it may not affect all of them */
		var x1 = {x:item.x,y:item.y};
		var x2 = {x:item.x+item.w,y:item.y};
		var x3 = {x:item.x,y:item.y+item.h};
		var x4 = {x:item.x+item.w,y:item.y+item.h};
		var childs = [];
		for(i in this.spaces){
			if(this.spaces[i].isPointInside(x1) || this.spaces[i].isPointInside(x2) || this.spaces[i].isPointInside(x3)  || this.spaces[i].isPointInside(x4)){
				childs = childs.concat(this.spaces[i].retrieve(item));continue;
			}
			/* If there is no point inside space, a space point may be inside item */
			var p = {x:this.spaces[i].left,y:this.spaces[i].top,w:this.spaces[i].width,h:this.spaces[i].height};
			var s1 = {x:p.x,	y:p.y};
			var s2 = {x:p.x+p.w,	y:p.y};
			var s3 = {x:p.x,	y:p.y+p.h};
			var s4 = {x:p.x+p.w,	y:p.y+p.h};
			if(this.spaces[i].isPointInsideElement(s1,item) || this.spaces[i].isPointInsideElement(s2,item) || this.spaces[i].isPointInsideElement(s3,item) || this.spaces[i].isPointInsideElement(s4,item)){
				childs = childs.concat(this.spaces[i].retrieve(item));continue;
			}
		}
		return childs;
	}
	return this.getChilds();
};
_quadtree.prototype.clear = function(){
	this.childs = [];
	for(var i=0;i<this.spaces.length;i++){
		this.spaces[i].clear();
		delete this.spaces[i];
	}
	this.spaces = [];
};

exports._quadtree = _quadtree;
