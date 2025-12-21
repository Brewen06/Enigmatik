import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ["input", "result", "nextButton"];
    static values = {
        checkUrl: String,
        id: Number
    }

    connect() {
        console.log('Game controller connected');
    }

    check(event) {
        event.preventDefault();
        let answer = '';
        
        const inputs = this.inputTargets;
        if (inputs.length > 0 && inputs[0].type === 'radio') {
            const checked = inputs.find(input => input.checked);
            if (checked) {
                answer = checked.value;
            } else {
                // No option selected
                this.resultTarget.innerHTML = '<div class="alert alert-warning">Veuillez sélectionner une réponse.</div>';
                return;
            }
        } else {
            answer = this.inputTarget.value;
        }
        
        fetch(this.checkUrlValue, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ answer: answer })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                let message = '<div class="alert alert-success">Bravo ! Bonne réponse.</div>';
                if (data.codeReponse) {
                    message += `<div class="alert alert-info mt-2"><strong>Indice pour le code final :</strong> <span class="badge bg-warning text-dark fs-4">${data.codeReponse}</span></div>`;
                }
                this.resultTarget.innerHTML = message;
                this.inputTargets.forEach(input => input.disabled = true);
                if (this.hasNextButtonTarget) {
                    this.nextButtonTarget.classList.remove('d-none');
                }
            } else {
                this.resultTarget.innerHTML = '<div class="alert alert-danger">Mauvaise réponse, essayez encore.</div>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            this.resultTarget.innerHTML = '<div class="alert alert-warning">Une erreur est survenue.</div>';
        });
    }
}
