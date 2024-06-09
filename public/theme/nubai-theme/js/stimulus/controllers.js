import { Application, Controller } from "https://unpkg.com/@hotwired/stimulus/dist/stimulus.js"
        window.Stimulus = Application.start();

// Main menu controller
Stimulus.register("mainmenu", class extends Controller {
    static targets = [ "modal" ];

    open() {
//        this.modalTarget.style.display = 'block';
        document.getElementById('menu-modal').style.display = 'block';
    }

    close() {
        this.modalTarget.style.display = 'none';
//        document.getElementById('menu-modal').style.display = 'none';
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





// Content-loader controller
Stimulus.register("content-loader", class extends Controller {

    static values = {url: String, refreshInterval: Number}

    connect() {
        this.load();

        if (this.hasRefreshIntervalValue) {
            this.startRefreshing();
        }
    }

    load() {
        fetch(this.urlValue)
                .then(response => response.text())
                .then(html => this.element.innerHTML = html);
    }

    startRefreshing() {
        this.refreshTimer = setInterval(() => {
            this.load();
        }, this.refreshIntervalValue);
    }

    stopRefreshing() {
        if (this.refreshTimer) {
            clearInterval(this.refreshTimer);
        }
    }

    disconnect() {
        this.stopRefreshing();
    }

});


// Slideshow controller
Stimulus.register("slideshow", class extends Controller {

    static targets = ["slide"]
    static values = {index: {type: Number, default: 2}}

    previous() {
        this.indexValue--;
    }

    next() {
        this.indexValue++;
    }

    indexValueChanged() {
        this.showCurrentSlide();
    }

    showCurrentSlide() {
        this.slideTargets.forEach((element, index) =>
            element.hidden = index !== this.indexValue);
    }
});

// Greet controller
Stimulus.register("greet", class extends Controller {
    static targets = ["name"]

    greet() {
        console.log(`Hello, ${this.name}!`);
    }

    get name() {
        return this.nameTarget.value;
    }
});

// Clipboard controller
Stimulus.register("clipboard", class extends Controller {
    static targets = ["source"]
    static classes = ["supported"]

    copy() {
        navigator.clipboard.writeText(this.sourceTarget.value);
    }

    connect() {
        if ("clipboard" in navigator) {
            this.element.classList.add(this.supportedClass);
        }
    }
});

// Selector controller
Stimulus.register("selector", class extends Controller {
    static targets = ["selectfield", "output"]

    change() {
        this.outputTarget.innerHTML = this.selectfield;
    }

    get selectfield() {
        return this.selectfieldTarget.value;
    }
});