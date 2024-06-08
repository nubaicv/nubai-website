import { Application, Controller } from "https://unpkg.com/@hotwired/stimulus/dist/stimulus.js"
        window.Stimulus = Application.start();


// Cropper Controller
Stimulus.register("cropper", class extends Controller {

    uploadImage() {

        const image = document.getElementById('photo-to-crop');
        const cropper = new Cropper(image, {
            aspectRatio: 1 / 1,
            zoomable: false
        });

        document.querySelector('#button-crop').addEventListener('click', function () {
            var croppedImage = cropper.getCroppedCanvas();
            var roundedImage = getRoundedCanvas(croppedImage).toDataURL("image/png");
            document.getElementById('profile-photo').src = roundedImage;
        });
        
        open_cropper_modal();

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
    }

    cropImage() {
        
        close_cropper_modal();
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