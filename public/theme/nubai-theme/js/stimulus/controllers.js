import { Application, Controller } from "https://unpkg.com/@hotwired/stimulus/dist/stimulus.js"
        window.Stimulus = Application.start();

// Main menu controller
Stimulus.register("mainmenu", class extends Controller {
    static targets = ["modal"];

    open() {
        document.getElementById('menu-modal').style.display = 'block';
    }

    close() {
        document.getElementById('menu-modal').style.display = 'none';
    }
});

// Cropper Controller
Stimulus.register("cropper", class extends Controller {
    static targets = ["modal", "cropimage", "profileimage", "inputimage"]
    static values = { url: String }

    initialize() {

        this.cropper;
        this.uploadProfileImage = function () {
            const xhttp = new XMLHttpRequest();
            const url = this.urlValue;
            xhttp.onload = function () {
                alert(this.responseText);
            };
            xhttp.open('post', url);
            xhttp.send('profile_image=theimage&fr=po');
        };
    }

    changeImage() {

        if (!this.inputimageTarget.files[0]) {
            alert('Error');
            return;
        }

        //Determina extensao do ficheiro carregado
        const fileExtension = this.inputimageTarget.value.substring(
                this.inputimageTarget.value.lastIndexOf('.'), this.inputimageTarget.value.lenght
                );
        // Vamos utilizar fileExtension para verificar si o ficheiro carregado e permitido.
        // --------------------------

        const inputImageURL = URL.createObjectURL(this.inputimageTarget.files[0]);
        this.cropimageTarget.src = inputImageURL;

        this.modalTarget.style.display = 'block';

        this.cropper = new Cropper(this.cropimageTarget, {
            aspectRatio: 1 / 1,
            zoomable: false
        });
    }

    cropImage() {

        const croppedImage = this.cropper.getCroppedCanvas();
        const roundedImage = getRoundedCanvas(croppedImage).toDataURL("image/png");

        // Upload roundedImage e guardar URL na DB

        this.uploadProfileImage();

        // -------------------------------------------


        this.profileimageTarget.src = roundedImage;

        this.modalTarget.style.display = 'none';
        this.cropper.destroy();

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
        this.cropper.destroy();
    }
});
