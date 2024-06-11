import { Application, Controller } from "https://unpkg.com/@hotwired/stimulus/dist/stimulus.js"
        window.Stimulus = Application.start();

// Main menu controller
Stimulus.register("mainmenu", class extends Controller {
    static targets = [ "modal" ];

    open() {
        document.getElementById('menu-modal').style.display = 'block';
    }

    close() {
        document.getElementById('menu-modal').style.display = 'none';
    }
});


// Cropper Controller
Stimulus.register("cropper", class extends Controller {
    static targets = ["modal", "cropimage", "profileimage"]

    initialize() {

        this.cropper;
    }

    uploadImage() {

        this.modalTarget.style.display = 'block';

        if (isEmpty(this.cropper)) {
            this.cropper = new Cropper(this.cropimageTarget, {
                aspectRatio: 1 / 1,
                zoomable: false
            });
        }

        function isEmpty(obj) {
            for (const prop in obj) {
                if (Object.hasOwn(obj, prop)) {
                    return false;
                }
            }

            return true;
        }
    }

    cropImage() {

        const croppedImage = this.cropper.getCroppedCanvas();
        const roundedImage = getRoundedCanvas(croppedImage).toDataURL("image/png");
        this.profileimageTarget.src = roundedImage;

        this.modalTarget.style.display = 'none';

        function getRoundedCanvas(sourceCanvas) {
            const canvas = document.createElement('canvas');
            const context = canvas.getContext('2d');
            const width = sourceCanvas.width;
            const height = sourceCanvas.height;

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
    }

    closeModal() {

        this.modalTarget.style.display = 'none';
    }
});
