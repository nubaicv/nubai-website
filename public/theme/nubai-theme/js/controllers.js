import { Application, Controller } from "https://unpkg.com/@hotwired/stimulus/dist/stimulus.js"
        window.Stimulus = Application.start();


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