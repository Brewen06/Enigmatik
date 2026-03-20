import {
    Controller
} from '@hotwired/stimulus';

export default class extends Controller {
    static targets = [
        'card',
        'finalCodeInput',
        'finalMessage',
        'modal',
        'enigmaModal',
        'enigmaTitle',
        'enigmaDesc',
        'enigmaType',
        'enigmaVignette',
        'enigmaVignetteContainer',
        'enigmaLink',
        'timer',
    ];

    static values = {
        validateUrl: String,
        timerSeconds: Number,
    };

    connect() {
        console.log('Board controller connected');
        this.timerInterval = null;
        this.remainingSeconds = this.timerSecondsValue || 0;

        if (this.hasTimerTarget && this.remainingSeconds > 0) {
            this.startTimer();
        }
    }

    disconnect() {
        if (this.timerInterval) {
            clearInterval(this.timerInterval);
            this.timerInterval = null;
        }
    }

    startTimer() {
        this.updateTimerDisplay();

        this.timerInterval = setInterval(() => {
            this.remainingSeconds -= 1;
            this.updateTimerDisplay();

            if (this.remainingSeconds <= 0) {
                this.handleTimeUp();
            }
        }, 1000);
    }

    updateTimerDisplay() {
        if (!this.hasTimerTarget) {
            return;
        }

        const safeSeconds = Math.max(0, this.remainingSeconds);
        const minutes = String(Math.floor(safeSeconds / 60)).padStart(2, '0');
        const seconds = String(safeSeconds % 60).padStart(2, '0');

        this.timerTarget.textContent = minutes + ':' + seconds;

        if (safeSeconds <= 60) {
            this.timerTarget.classList.add('tw-text-red-600');
        }
    }

    isTimeOver() {
        return this.timerSecondsValue > 0 && this.remainingSeconds <= 0;
    }

    showTimeOverMessage() {
        if (this.hasFinalMessageTarget) {
            this.finalMessageTarget.innerHTML = '<span class="tw-text-red-400 tw-font-bold">Temps écoulé. La partie est terminée.</span>';
        }
    }

    handleTimeUp() {
        if (this.timerInterval) {
            clearInterval(this.timerInterval);
            this.timerInterval = null;
        }

        this.remainingSeconds = 0;
        this.updateTimerDisplay();
        this.showTimeOverMessage();

        if (this.hasFinalCodeInputTarget) {
            this.finalCodeInputTarget.disabled = true;
        }

        const validateButton = this.element.querySelector('[data-action~="click->board#checkFinalCode"]');
        if (validateButton) {
            validateButton.disabled = true;
        }

        const enigmaCards = this.element.querySelectorAll('[data-action~="click->board#openEnigma"]');
        enigmaCards.forEach((card) => {
            card.classList.add('tw-opacity-50', 'tw-pointer-events-none');
        });
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
        if (this.isTimeOver()) {
            this.showTimeOverMessage();
            return;
        }

        const params = event.currentTarget.dataset;
        this.enigmaTitleTarget.textContent = 'Énigme ' + params.boardIndexParam + ' : ' + params.boardTitleParam;
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
        if (this.isTimeOver()) {
            this.showTimeOverMessage();
            return;
        }

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
        event.stopPropagation();

        if (this.isTimeOver()) {
            this.showTimeOverMessage();
            return;
        }

        const code = this.finalCodeInputTarget.value;
        const messageDiv = this.finalMessageTarget;

        messageDiv.innerHTML = '<span class="tw-text-yellow-400">Vérification...</span>';

        fetch(this.validateUrlValue, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({
                    code: code
                }),
            })
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    messageDiv.innerHTML = '<span class="tw-text-green-400 tw-font-bold">' + data.message + '</span>';
                    this.finalCodeInputTarget.disabled = true;
                } else {
                    messageDiv.innerHTML = '<span class="tw-text-red-400 tw-font-bold">' + data.message + '</span>';
                }
            })
            .catch((error) => {
                console.error('Error:', error);
                messageDiv.innerHTML = '<span class="tw-text-red-400">Erreur de communication.</span>';
            });
    }
}
