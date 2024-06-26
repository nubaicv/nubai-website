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
    static targets = ["modal", "cropimage", "profileimage", "inputimage", "messagemodal", "errormessage", "spinner"]
    static values = {url: String}

    initialize() {

        this.cropper;
        this.roundedImage;
        this.uploadProfileImage = function (image) {

            const xhttp = new XMLHttpRequest();
            const url = this.urlValue;
            const profileImageTarget = this.profileimageTarget; // Por agora que estamos colocando a imagen directo do cropper.
            const roundedImage = this.roundedImage; // Por agora
            const messageModal = this.messagemodalTarget;
            const errorMessage = this.errormessageTarget;
            const spinner = this.spinnerTarget;

            xhttp.onreadystatechange = function () {
                if (this.readyState === 1) {
                    spinner.classList.remove('w3-hide');
                }
                if (this.readyState === 2) {
                }
                if (this.readyState === 3) {
                }
                if (this.readyState === 4) {
                    spinner.classList.add('w3-hide');
                    switch (this.status) {
                        case 200:
                            profileImageTarget.src = roundedImage;
                            break;
                        case 400:
                            errorMessage.innerHTML = this.responseText;
                            messageModal.style.display = 'block';
                            break;
                        case 401:
                            location.reload();
                            break;
                        default:
                            errorMessage.innerHTML = this.responseText;
                            messageModal.style.display = 'block';
                            break;
                    }
                }
            };

            const formData = new FormData();
            formData.append("profileimage", image);

            xhttp.open('post', url, true);
            xhttp.send(formData);
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
        const roundedImageToUpload = getRoundedCanvas(croppedImage);
        this.roundedImage = getRoundedCanvas(croppedImage).toDataURL("image/png");
        const dataFile = dataURLtoFile(this.roundedImage, "imageName.png");
        
        this.modalTarget.style.display = 'none';
        this.cropper.destroy();

        // Upload roundedImage as File
        this.uploadProfileImage(dataFile);
        // --------------------------        

        function getRoundedCanvas(sourceCanvas) {
            const canvas = document.createElement('canvas');
            const context = canvas.getContext('2d');
            const width = 400;
            const height = 400;

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

        function dataURLtoFile(dataurl, filename) {
            var arr = dataurl.split(','), mime = arr[0].match(/:(.*?);/)[1],
                    bstr = atob(arr[1]), n = bstr.length, u8arr = new Uint8Array(n);
            while (n--) {
                u8arr[n] = bstr.charCodeAt(n);
            }
            return new File([u8arr], filename, {type: mime});
        }
    }

    closeModal() {

        this.modalTarget.style.display = 'none';
        this.cropper.destroy();
    }
    
    closeMessageModal() {

        this.messagemodalTarget.style.display = 'none';
    }
});
