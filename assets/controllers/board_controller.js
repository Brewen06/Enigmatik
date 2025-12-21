import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ["card", "finalCodeInput", "finalMessage", "modal", "enigmaModal", "enigmaTitle", "enigmaDesc", "enigmaType", "enigmaVignette", "enigmaVignetteContainer", "enigmaLink"];
    static values = {
        validateUrl: String
    }

    connect() {
        console.log('Board controller connected');
    }

    closeModal(event) {
        event.preventDefault();
        this.modalTarget.classList.add('tw-hidden');
    }

    closeEnigmaModal(event) {
        event.preventDefault();
        this.enigmaModalTarget.classList.add('tw-hidden');
    }

    openEnigma(event) {
        const params = event.currentTarget.dataset;
        this.enigmaTitleTarget.textContent = `Énigme ${params.boardIndexParam} : ${params.boardTitleParam}`;
        this.enigmaDescTarget.textContent = params.boardDescParam;
        this.enigmaTypeTarget.textContent = params.boardTypeParam;
        
        if (params.boardVignetteParam) {
            this.enigmaVignetteTarget.textContent = params.boardVignetteParam;
            this.enigmaVignetteContainerTarget.classList.remove('tw-hidden');
        } else {
            this.enigmaVignetteContainerTarget.classList.add('tw-hidden');
        }

        this.enigmaLinkTarget.href = params.boardUrlParam;
        this.enigmaModalTarget.classList.remove('tw-hidden');
    }

    flipCard(event) {
        // Prevent flipping if clicking on input or button inside the card
        if (event.target.tagName === 'INPUT' || event.target.tagName === 'BUTTON') {
            return;
        }
        
        const card = event.currentTarget;
        const inner = card.querySelector('.tw-transform-style-3d');
        if (inner) {
            inner.classList.toggle('tw-rotate-y-180');
        }
    }

    checkFinalCode(event) {
        event.preventDefault();
        event.stopPropagation(); // Prevent card flip
        
        const code = this.finalCodeInputTarget.value;
        const messageDiv = this.finalMessageTarget;
        
        messageDiv.innerHTML = '<span class="tw-text-yellow-400">Vérification...</span>';
        
        fetch(this.validateUrlValue, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ code: code })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                messageDiv.innerHTML = `<span class="tw-text-green-400 tw-font-bold">${data.message}</span>`;
                this.finalCodeInputTarget.disabled = true;
                // Optional: Trigger some success animation or redirect
            } else {
                messageDiv.innerHTML = `<span class="tw-text-red-400 tw-font-bold">${data.message}</span>`;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            messageDiv.innerHTML = '<span class="tw-text-red-400">Erreur de communication.</span>';
        });
    }
}
