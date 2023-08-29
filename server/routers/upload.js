const upload = require('../helper/upload');
const helper = require('../helper/helper');
var router = global.router;

router.post('/api/upload', upload.uploadMedia.single('file'), async (req, res) => {    
    if (!req.file) {
        // No file was uploaded
        res.json({
            status:400,
            message: "No file was uploaded."                
        });
    }
    
    var data = await helper.insertMedia(req.file);
    if (data) {
        res.json({
            data,
            message: "File uploaded successfully."                
        });
    }else{
        res.json({
            status:400,
            message: "No file was uploaded."                
        }); 
    }
});
module.exports = router;