// @flow
import _ from "lodash";
import React, { Component } from "react";
import { View, Animated } from "react-native";
import PropTypes from "prop-types";
import { ButtonView, Text } from "../../components";
import styles from "./styles";
import { Metrics, Colors } from "../../theme";

const COLORS = [
  Colors.background.progressFillColor,
  Colors.background.progressFillColor
];

class PressHoldButton extends Component {
  static propTypes = {
    title: PropTypes.string,
    onPress: PropTypes.func,
    isClickable: PropTypes.bool,
    backgroundColor: PropTypes.string,
    color: PropTypes.string,
    size: PropTypes.string,
    type: PropTypes.string,
    pressTimer: PropTypes.number,
    customStyle: PropTypes.oneOfType([PropTypes.number, PropTypes.object])
  };

  static defaultProps = {
    title: "",
    onPress: () => {},
    isClickable: true,
    backgroundColor: "accent",
    color: "secondary",
    size: "normal",
    type: "medium",
    pressTimer: 3000,
    customStyle: {}
  };

  pressAction = new Animated.Value(0);
  _value = 0;

  state = {
    buttonWidth: 0,
    buttonHeight: 0,
    isCompleted: false
  };

  componentWillMount() {
    this.pressAction.addListener(v => {
      this._value = v.value;
    });
  }

  getProgressStyle() {
    const width = this.pressAction.interpolate({
      inputRange: [0, 1],
      outputRange: [0, this.state.buttonWidth]
    });

    const bgColor = this.pressAction.interpolate({
      inputRange: [0, 1],
      outputRange: COLORS
    });

    return {
      width,
      height: Metrics.ratio(5),
      backgroundColor: bgColor
    };
  }

  getButtonWidthLayout = e => {
    this.setState({
      buttonWidth: e.nativeEvent.layout.width,
      buttonHeight: e.nativeEvent.layout.height
    });
  };

  animationActionComplete = () => {
    if (this._value === 1) {
      this.state.isCompleted = true;
      this.props.onPress();
      this.clearAnimation();
    }
  };

  clearAnimation = () => {
    Animated.timing(this.pressAction, {
      duration: 200,
      toValue: 0
    }).start(() => {
      this.state.isCompleted = false;
    });
  };

  handlePressIn = () => {
    if (this.props.isClickable) {
      Animated.timing(this.pressAction, {
        duration: this.props.pressTimer,
        toValue: 1
      }).start(this.animationActionComplete);
    }
  };

  handlePressOut = () => {
    if (this.props.isClickable && !this.state.isCompleted) {
      Animated.timing(this.pressAction, {
        duration: this._value * this.props.pressTimer,
        toValue: 0
      }).start();
    }
  };

  render() {
    const { title, color, size, type, backgroundColor, customStyle } = this.props;
    const { containerStyle, titleStyle } = styles;

    return (
      <ButtonView
        style={[containerStyle, customStyle]}
        onPressIn={this.handlePressIn}
        onPressOut={this.handlePressOut}
      >
        <View style={styles.fillViewStyle} onLayout={this.getButtonWidthLayout}>
          <Animated.View style={this.getProgressStyle()} />
        </View>
        <View style={styles.textContainerStyle}>
          <Text
            type={type}
            title={title}
            background={backgroundColor}
            color={color}
            size={size}
            style={titleStyle}
          >
            {title}
          </Text>
        </View>
      </ButtonView>
    );
  }
}

export default PressHoldButton;
