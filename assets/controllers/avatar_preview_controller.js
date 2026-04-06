import {
    Controller
} from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['select', 'wrapper', 'image'];
    static values = {
        assetBase: String,
    };

    connect() {
        this.update();
    }

    update() {
        if (!this.hasSelectTarget || !this.hasWrapperTarget || !this.hasImageTarget) {
            return;
        }

        const selectedOption = this.selectTarget.options[this.selectTarget.selectedIndex];
        const rawImagePath = selectedOption ? selectedOption.dataset.image : '';
        const imageUrl = this.buildImageUrl(rawImagePath);

        if (!imageUrl) {
            this.hidePreview();
            return;
        }

        this.imageTarget.onerror = () => this.hidePreview();
        this.imageTarget.onload = () => {
            this.wrapperTarget.classList.remove('d-none');
        };
        this.imageTarget.src = imageUrl;
    }

    buildImageUrl(rawPath) {
        if (!rawPath) {
            return '';
        }

        if (/^(?:https?:)?\/\//i.test(rawPath) || rawPath.startsWith('data:')) {
            return rawPath;
        }

        let cleanedPath = rawPath.trim().replace(/^\/+/, '');
        cleanedPath = cleanedPath.replace(/^public\//, '');

        if (!cleanedPath) {
            return '';
        }

        const basePath = this.assetBaseValue || '/';
        const normalizedBasePath = basePath.endsWith('/') ? basePath : `${basePath}/`;

        return `${normalizedBasePath}${cleanedPath}`;
    }

    hidePreview() {
        this.wrapperTarget.classList.add('d-none');
        this.imageTarget.removeAttribute('src');
    }
}
