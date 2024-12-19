export const  isImage = (attachment)=> {

    let mime = attachment.mime || attachment.type;
     mime = mime.split("/");
     return mime[0].toLowerCase() === "image";

}
export const  isVideo = (attachment)=> {
    console.log(attachment)
    let mime = attachment.mime || attachment.type;
     mime = mime.split("/");
     console.log(mime[0].toLowerCase() === "video");
     
     return mime[0].toLowerCase() === "video";

}