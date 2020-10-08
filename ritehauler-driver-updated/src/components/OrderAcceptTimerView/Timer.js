import React, { Component } from "react";
import PropTypes from "prop-types";
//import BackgroundTimer from "react-native-background-timer";
import { Text } from "../../components";
import styles from "./styles";

export default class Timer extends Component {
  static propTypes = {
    timer: PropTypes.number,
    onTimeCompleted: PropTypes.func,
  };

  static defaultProps = {
    timer: 0,
    onTimeCompleted: {},
  };

  state = {
    timer: this.props.timer,
    stopTimer: false,
  };

  timer;

  componentDidMount() {
    this.startTimer();
  }

  componentWillUnmount() {
    if (this.timer) {
      this.timer = undefined;
    }
  }

  stopTimer() {
    this.setState({ stopTimer: true });
  }

  start = () => {
    this.startTimer();
  };

  startTimer() {
    if (!this.timer) {
      // this.timer = BackgroundTimer.setInterval(() => {
      //   this.setState({ timer: this.state.timer - 1000 }, () => {
      //     if (this.state.timer === 0) {
      //       BackgroundTimer.clearInterval(this.timer);
      //       this.timer = undefined;
      //       this.props.onTimeCompleted();
      //     } else if (this.state.stopTimer) {
      //       BackgroundTimer.clearInterval(this.timer);
      //       this.timer = undefined;
      //     }
      //   });
      // }, 1000);
    }
  }

  // startTimer() {
  //   this.timer = setTimeout(() => {
  //     this.setState({ timer: this.state.timer - 1000 }, () => {
  //       if (this.state.timer === 0) {
  //         this.props.onTimeCompleted();
  //       } else if (this.state.stopTimer) {
  //         clearTimeout(this.timer);
  //       } else {
  //         this.startTimer();
  //       }
  //     });
  //     this.timer = undefined;
  //   }, 1000);
  // }

  getShownTime() {
    let time = this.state.timer / 1000;
    let min = Math.floor(time / 60);
    let sec = time % 60;

    let minutes = min < 10 ? "0" + min : min;
    let seconds = sec < 10 ? "0" + sec : sec;

    return minutes + ":" + seconds;
  }

  render() {
    const time = this.getShownTime();
    return (
      <Text color="accent2" size="large" type="bold" style={styles.timerTextStyle}>
        {time}
      </Text>
    );
  }
}
