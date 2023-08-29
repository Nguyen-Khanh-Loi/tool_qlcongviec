export const uploadMedia = {
    upload,
    uploadServer
};
async function upload(data){
    var results = await axios.post(`/api/media/upload`, data, {
        headers: {
            'Content-Type': 'multipart/form-data',
        },
    });
    if (results) {
        return results.data.name_file;
    }else{
        return false;
    }
}
async function uploadServer(data){
    var results = await axios.post(`${process.env.SERVER+":"+process.env.PORT_SEVER}/api/upload`, data, {
        headers: {
            'Content-Type': 'multipart/form-data',
        },
    });
    if (results) {
        console.log('results', results)
        return results.data.data.name_file;
    }else{
        return false;
    }
}