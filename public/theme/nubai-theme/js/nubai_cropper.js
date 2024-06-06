const image = document.getElementById('photo-to-crop');
const cropper = new Cropper(image, {
    aspectRatio: 1 / 1
});

document.querySelector('#button-crop').addEventListener('click', function () {
    var croppedImage = cropper.getCroppedCanvas();
    var roundedImage = getRoundedCanvas(croppedImage).toDataURL("image/png");
    document.getElementById('profile-photo').src = roundedImage;
});

// ------------------
function getRoundedCanvas(sourceCanvas) {
    var canvas = document.createElement('canvas');
    var context = canvas.getContext('2d');
    var width = sourceCanvas.width;
    var height = sourceCanvas.height;

    canvas.width = width;
    canvas.height = height;
    context.imageSmoothingEnabled = true;
    context.drawImage(sourceCanvas, 0, 0, width, height);
    context.globalCompositeOperation = 'destination-in';
    context.beginPath();
    context.arc(width / 2, height / 2, Math.min(width, height) / 2, 0, 2 * Math.PI, true);
    context.fill();
    return canvas;
}