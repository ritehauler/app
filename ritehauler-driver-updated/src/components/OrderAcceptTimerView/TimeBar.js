/**
 * Name: MessageBar
 * Description: A Message Bar Component displayed at the top of screen
 * https://github.com/talor-a/react-native-message-bar
 */
"use strict";

import React, { Component } from "react";
import { View, Animated } from "react-native";
//import BackgroundTimer from 'react-native-background-timer';
import { Text, ButtonView } from "../../components";
import Timer from "./Timer";
import styles from "./styles";
import { Metrics } from "../../theme";

class TimeBar extends Component {
  constructor(props) {
    super(props);

    this.animatedValue = new Animated.Value(0);
    this.notifyAlertHiddenCallback = null;
    this.alertShown = false;
    this.timeoutHide = null;

    this.state = this.getStateByProps(props);
    this.defaultState = this.getStateByProps(props);
  }

  componentDidMount() {
    // Configure the offsets prior to recieving updated props or recieving the first alert
    // This ensures the offsets are set properly at the outset based on the initial position.
    // This prevents the bar from appearing  and covering half of the screen when the
    // device is started in landscape and then rotated to portrait.
    // This does not happen after the first alert appears, as setNewState() is called on each
    // alert and calls _changeOffsetByPosition()
  }

  componentWillReceiveProps(nextProps) {
    if (nextProps && Object.keys(nextProps).length > 0) {
      this.setNewState(nextProps);
    }
  }

  setNewState(state) {
    // Set the new state, this is triggered when the props of this MessageBar changed
    this.setState(this.getStateByProps(state));
  }

  getStateByProps(props) {
    const def = this.defaultState || {};
    return {
      // Default values, will be overridden
      animationTypeTransform: "SlideFromTop", // default value

      /* Cusomisation of the alert: Title, Message, Icon URL, Alert alertType (error, success, warning, info), Duration for Alert keep shown */
      title: props.title,
      rightText: props.rightText,
      duration: props.duration || def.duration || 3000,

      /* Hide setters */
      shouldHideAfterDelay:
        props.shouldHideAfterDelay == undefined && def.shouldHideAfterDelay == undefined
          ? true
          : props.shouldHideAfterDelay || def.shouldHideAfterDelay,
      shouldHideOnTap:
        props.shouldHideOnTap == undefined && def.shouldHideOnTap == undefined
          ? true
          : props.shouldHideOnTap || def.shouldHideOnTap,

      /* Callbacks method on Alert Tapped, on Alert Show, on Alert Hide */
      onPressRightText: props.onPressRightText || def.onPressRightText,
      onTimerCompleted: props.onTimerCompleted || def.onTimerCompleted,
      onShow: props.onShow || def.onShow,
      onHide: props.onHide || def.onHide,

      /* Duration of the animation */
      durationToShow: props.durationToShow || def.durationToShow || 350,
      durationToHide: props.durationToHide || def.durationToHide || 350,

      /* Position of the alert and Animation Type the alert is shown */
      position: props.position || def.position || "top",
      animationType: props.animationType || def.animationType,
    };
  }

  /* 
  * On Time out Completed
  */
  onTimeoutCompleted = () => {
    this.hideMessageBarAlert();
    this.state.onTimerCompleted();
  };

  /*
  * Show the alert
  */
  showMessageBarAlert() {
    // If an alert is already shonw or doesn't have a title or a message, do nothing
    if (this.alertShown || (this.state.title == null && this.state.rightText == null)) {
      return;
    }

    // Set the data of the alert in the state
    this.alertShown = true;

    // Display the alert by animating it from the top of the screen
    // Auto-Hide it after a delay set in the state
    Animated.timing(this.animatedValue, {
      toValue: 1,
      duration: this.state.durationToShow,
    }).start(this._showMessageBarAlertComplete());
  }

  /*
  * Hide the alert after a delay, typically used for auto-hidding
  */
  _showMessageBarAlertComplete() {
    // Execute onShow callback if any
    this._onShow();

    // If the duration is null, do not hide the
    /* if (this.state.shouldHideAfterDelay) {
      this.timeoutHide = BackgroundTimer.setTimeout(() => {
        this.hideMessageBarAlert();
        //this.state.onTimerCompleted();
      }, this.state.duration);
    } */
  }

  /*
  * Return true if the MessageBar is currently displayed, otherwise false
  */
  isMessageBarShown() {
    return this.alertShown;
  }

  /*
  * Hide the alert, typically used when user tap the alert
  */
  hideMessageBarAlert() {
    // Hide the alert after a delay set in the state only if the alert is still visible
    if (!this.alertShown) {
      return;
    }

    // BackgroundTimer.clearTimeout(this.timeoutHide);

    // Animate the alert to hide it to the top of the screen
    Animated.timing(this.animatedValue, {
      toValue: 0,
      duration: this.state.durationToHide,
    }).start(this._hideMessageBarAlertComplete());
  }

  _hideMessageBarAlertComplete() {
    // The alert is not shown anymore
    this.alertShown = false;

    this._notifyAlertHidden();

    // Execute onHide callback if any
    this._onHide();
  }

  /*
  * Callback executed to tell the observer the alert is hidden
  */
  _notifyAlertHidden() {
    if (this.notifyAlertHiddenCallback) {
      this.notifyAlertHiddenCallback();
    }
  }

  /*
  * Callback executed when the user tap the alert
  */
  _alertTapped() {
    // Hide the alert
    if (this.state.shouldHideOnTap) {
      this.hideMessageBarAlert();
    }

    // Execute the callback passed in parameter
    if (this.state.onPressRightText) {
      this.state.onPressRightText();
    }
  }

  /*
  * method to clear timer text shown on top
  */
  stopTimer() {
    this.timerRef.stopTimer();
  }

  /*
  * Callback executed when alert is shown
  */
  _onShow() {
    if (this.state.onShow) {
      this.state.onShow();
    }
  }

  /*
  * Callback executed when alert is hidden
  */
  _onHide() {
    if (this.state.onHide) {
      this.state.onHide();
    }
  }

  /*
  * Set the animation transformation depending on the chosen animationType, or depending on the state's position if animationType is not overridden
  */
  _applyAnimationTypeTransformation() {
    let position = this.state.position;
    let animationType = this.state.animationType;

    if (animationType === undefined) {
      if (position === "bottom") {
        animationType = "SlideFromBottom";
      } else {
        // Top by default
        animationType = "SlideFromTop";
      }
    }

    switch (animationType) {
      case "SlideFromTop":
        var animationY = this.animatedValue.interpolate({
          inputRange: [0, 1],
          outputRange: [-Metrics.screenHeight, 0],
        });
        this.animationTypeTransform = [{ translateY: animationY }];
        break;
      case "SlideFromBottom":
        var animationY = this.animatedValue.interpolate({
          inputRange: [0, 1],
          outputRange: [Metrics.screenHeight, 0],
        });
        this.animationTypeTransform = [{ translateY: animationY }];
        break;
      case "SlideFromLeft":
        var animationX = this.animatedValue.interpolate({
          inputRange: [0, 1],
          outputRange: [-Metrics.screenWidth, 0],
        });
        this.animationTypeTransform = [{ translateX: animationX }];
        break;
      case "SlideFromRight":
        var animationX = this.animatedValue.interpolate({
          inputRange: [0, 1],
          outputRange: [Metrics.screenWidth, 0],
        });
        this.animationTypeTransform = [{ translateX: animationX }];
        break;
      default:
        var animationY = this.animatedValue.interpolate({
          inputRange: [0, 1],
          outputRange: [-Metrics.screenHeight, 0],
        });
        this.animationTypeTransform = [{ translateY: animationY }];
        break;
    }
  }

  renderTimer() {
    if (this.state.title != null) {
      return (
        <Timer
          ref={ref => {
            this.timerRef = ref;
          }}
          timer={this.state.duration}
          onTimeCompleted={this.onTimeoutCompleted}
        />
      );
    }

    return null;
  }

  renderDeclineButton() {
    if (this.state.rightText != null) {
      return (
        <ButtonView onPress={this.state.onPressRightText} style={styles.declineButtonStyle}>
          <Text color="accent" size="xxxSmall" type="medium">
            {this.state.rightText}
          </Text>
        </ButtonView>
      );
    }

    return null;
  }

  /*
  * Alert Rendering Methods
  */

  render() {
    // Set the animation transformation depending on the chosen animationType, or depending on the state's position if animationType is not overridden
    this._applyAnimationTypeTransformation();

    return (
      <Animated.View
        style={[styles.timerAnimatedContainerStyle, { transform: this.animationTypeTransform }]}
      >
        <View style={styles.timerViewContainerStyle}>
          {this.renderTimer()}
          {this.renderDeclineButton()}
        </View>
      </Animated.View>
    );
  }
}

module.exports = TimeBar;
