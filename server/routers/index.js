var express = require('express');
global.router = express.Router();
var router = global.router;
router = require('./upload');
/* GET home page. */
router.get('/', function(req, res, next) {
    res.render('index', { title: 'Welcome Gosu' });
});
module.exports = router;