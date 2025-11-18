export default (displayTime = 5) => ({
  timer: null,
  countdown: 0,
  progress: 0,
  max: displayTime,
  stepInterval: 50,
  init() {
    this.startCountDown();
  },
  startCountDown() {
    const totalSteps = (this.max / (this.stepInterval / 1000));
    const stepWidth = 100 / totalSteps;

    this.timer = setInterval(() => {
      if (this.countdown < this.max) {
        this.countdown += this.stepInterval / 1000
        this.progress += stepWidth;
      } else {
        this.destroy();
      }
    }, this.stepInterval);
  },
  pauseCountdown() {
    clearInterval(this.timer);
  },
  resumeCountdown() {
    this.startCountDown();
  },
  destroy() {
    clearInterval(this.timer);
    this.$el.closest('.alert-component').remove();
  },
})
