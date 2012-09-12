var express = require('express');
var fs = require('fs');

var app = express.createServer();
function isEmpty(obj) {
    for(var prop in obj) {
        if(obj.hasOwnProperty(prop))
            return false;
    }

    return true;
};
function merge_options(obj1,obj2){
    var obj3 = {};
    for (var attrname in obj1) { obj3[attrname] = obj1[attrname]; }
    for (var attrname in obj2) { obj3[attrname] = obj2[attrname]; }
    return obj3;
}
app.configure(function(){
    app.use(express.methodOverride());
    app.use(express.bodyParser());
});

app.post('/oauth/token', function(req, res) {
    var data = merge_options(req.query, req.body);
    if(data["grant_type"]=="authorization_code") {
        if(data["code"]=="bad code") {
            res.send({"message":"Error validando el parámetro code","error":"invalid_grant","status":400,"cause":[]}, 400);
        } else if(data["code"]=="valid code without refresh token") {
            res.send({
                   "access_token" : "valid token",
                   "token_type" : "bearer",
                   "expires_in" : 10800,
                   "scope" : "write read"
            });
        } else if(data["code"]=="valid code with refresh token") {
            res.send({
                   "access_token" : "valid token",
                   "token_type" : "bearer",
                   "expires_in" : 10800,
                   "refresh_token" : "valid refresh token",
                   "scope" : "write read"
            });
        } else if(data["code"]=="invalid token") {
            res.send({
                   "access_token" : "invalid token",
                   "token_type" : "bearer",
                   "expires_in" : 10800,
                   "refresh_token" : "valid refresh token",
                   "scope" : "write read"
            });
        } else if(data["code"]=="expired code with refresh token") {
            res.send({
                   "access_token" : "expired token",
                   "token_type" : "bearer",
                   "expires_in" : 1,
                   "refresh_token" : "valid refresh token",
                   "scope" : "write read"
            });
        } else {
            res.send(404);
        }
    } else if(data['grant_type']=='refresh_token') {
        if(data['refresh_token']=='valid refresh token') {
            res.send({
                   "access_token" : "valid token",
                   "token_type" : "bearer",
                   "expires_in" : 10800,
                   "scope" : "write read",
                   "refresh_token" : "valid refresh token",
            });
        }
    }
});

app.get('/sites', function(req, res) {
    res.send([{"id":"MLA","name":"Argentina"},{"id":"MLB","name":"Brasil"},{"id":"MCO","name":"Colombia"},{"id":"MCR","name":"Costa Rica"},{"id":"MEC","name":"Ecuador"},{"id":"MLC","name":"Chile"},{"id":"MLM","name":"Mexico"},{"id":"MLU","name":"Uruguay"},{"id":"MLV","name":"Venezuela"},{"id":"MPA","name":"Panamá"},{"id":"MPE","name":"Perú"},{"id":"MPT","name":"Portugal"},{"id":"MRD","name":"Dominicana"}]);
});

app.get('/sites/MLA/searchUrl', function(req, res) {
    res.send({"url": "http://listado.mercadolibre.com.ar/"});
});


app.get('/users/me', function(req, res) {
    if(req.query['access_token']=='valid token') {
        res.send({"id":123456,"nickname":"foobar"});
    } else if(req.query['access_token']=='expired token') {
        res.send(404);
    } else {
        res.send({"message":"The User ID must match the consultant's","error":"forbidden","status":403,"cause":[]}, 403);
    }
});


app.post('/items', function(req, res) {
    if(req.query['access_token']=='valid token') {
        if(req.body && req.body.foo == "bar") {
            res.send(201);
        } else {
            res.send(400);
        }
    } else if(req.query['access_token']=='expired token') {
        res.send(404);
    } else {
        res.send(403);
    }
});


app.put('/items/123', function(req, res) {
    if(req.query['access_token']=='valid token') {
        if(req.body && req.body.foo == "bar") {
            res.send(200);
        } else {
            res.send(400);
        }
    } else if(req.query['access_token']=='expired token') {
        res.send(404);
    } else {
        res.send(403);
    }
});

app.delete('/items/123', function(req, res) {
    if(req.query['access_token']=='valid token') {
        res.send(200);
    } else if(req.query['access_token']=='expired token') {
        res.send(404);
    } else {
        res.send(403);
    }
});
app.get('/applications/123456', function(req, res) {
    res.send({"id":123456,"site_id":"MLA"});
    
});


app.listen(3000);

fs.writeFileSync('/tmp/mockapi.pid', process.pid);
