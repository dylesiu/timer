const STATE_STOPPED = 'stopped';
const STATE_RUNNING = 'running';
const STATE_PAUSED = 'paused';

class Clock {
  seconds = 0;
  startedDateTime = null;
  state = STATE_STOPPED;

  _interval = null;

  onTick = () => {};

  constructor(startedDateTime, onTick) {
    this.startedDateTime = startedDateTime;
    if (onTick && typeof onTick === 'function') {
      this.onTick = onTick;
    }
  }

  start() {
    this.seconds = Math.abs(((new Date()).getTime() - this.startedDateTime.getTime()) / 1000);
    this.onTick(this.seconds);

    this._interval = setInterval(() => {
      this.seconds = Math.abs(((new Date()).getTime() - this.startedDateTime.getTime()) / 1000);
      this.onTick(this.seconds);
    }, 1000);

    this.state = STATE_RUNNING;
  }

  pause() {
    if (this._interval) {
      clearInterval(this._interval);
    }

    this.state = STATE_PAUSED;
  }

  clear() {
    const tempTime = this.seconds;
    this.pause();
    this.seconds = 0;
    this.state = STATE_STOPPED;

    return tempTime;
  }
}

export default Clock;
