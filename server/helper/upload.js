const multer = require("multer");
const storage = multer.diskStorage({
    destination: (req, file, cb) => {
        cb(null, 'images/uploads/'); // Specify the destination folder for uploaded images
    },
    filename: (req, file, cb) => {
        cb(null, Date.now() + '-' + file.originalname); // Generate a unique filename for the uploaded image
    },
});
const upload = multer({ storage: storage });
module.exports.uploadMedia = upload