import React, { Component } from "react";
import BackgroundTimer from "react-native-background-timer";
import { LOCATION_COLLECTION_PERIOD } from "../../constant";
// redux imports
import { connect, compose } from "react-redux";

const WithBackgroundTimer = Child => {
  return class WithBackgroundTimer extends Component {
    constructor(props) {
      super(props);
      this.state = {
        timerRunning: props.backgroundLocation
      };
      this.startBackgroundTimer = this.startBackgroundTimer.bind(this);
      this.stopBackgroundTimer = this.stopBackgroundTimer.bind(this);
      this.locationsArray = [];
    }

    static getDerivedStateFromProps(nextProps, prevState) {
      if (prevState.timerRunning !== nextProps.backgroundLocation) {
        return { timerRunning: nextProps.backgroundLocation };
      }

      // indicating no change in
      return null;
    }

    backgroundTimerID = undefined;

    startBackgroundTimer() {
      if (this.backgroundTimerID) {
        BackgroundTimer.clearInterval(this.backgroundTimerID);
        this.backgroundTimerID = undefined;
      }
      // Start a timer that runs continuous after X milliseconds
      this.backgroundTimerID = BackgroundTimer.setInterval(() => {
        // this will be executed every 1 sec
        // even when app is in the background

        this.locationsArray.push({
          latitude: this.props.location.latitude,
          longitude: this.props.location.longitude,
          timestamp: this.props.location.updateTime
        });
        if (this.locationsArray.length === 10) {
          this.props.orderDriverTrackingRequest({
            driver_id: this.props.user.entity_id,
            driver_location: this.locationsArray,
            order_id: this.props.orderEntityID
          });
          this.locationsArray = [];
        }
      }, LOCATION_COLLECTION_PERIOD);
    }

    stopBackgroundTimer() {
      BackgroundTimer.clearInterval(this.backgroundTimerID);
      this.backgroundTimerID = undefined;
      this.locationsArray = [];
    }

    componentWillUnmount() {
      if (this.backgroundTimerID) {
        BackgroundTimer.clearInterval(this.backgroundTimerID);
        this.backgroundTimerID = undefined;
      }
    }

    render() {
      return <Child {...this.props} />;
    }

    componentDidUpdate(prevProps, prevState) {
      if (!prevState.timerRunning && this.state.timerRunning) {
        this.startBackgroundTimer();
      }

      if (prevState.timerRunning && !this.state.timerRunning) {
        this.stopBackgroundTimer();
      }
    }
  };
};

export default WithBackgroundTimer;
