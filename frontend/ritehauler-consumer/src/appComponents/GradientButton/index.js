// @flow
import React from "react";
import LinearGradient from "react-native-linear-gradient";
import PropTypes from "prop-types";
import { View, Keyboard, ActivityIndicator } from "react-native";

import { Colors, Strings } from "../../theme";
import styles from "./styles";
import { ButtonView, Text } from "../../components";
import { GRADIENT_START, GRADIENT_END } from "../../constants";
import Utils from "../../util";

export default class GradientButton extends React.PureComponent {
  static propTypes = {
    style: PropTypes.oneOfType([
      PropTypes.array,
      PropTypes.object,
      PropTypes.number
    ]),
    styleGradient: PropTypes.oneOfType([
      PropTypes.array,
      PropTypes.object,
      PropTypes.number
    ]),
    text: PropTypes.string,
    onPress: PropTypes.func,
    inBottom: PropTypes.bool,
    isDisable: PropTypes.bool,
    customView: PropTypes.func,
    disableRipple: PropTypes.bool,
    setKeyboardEvent: PropTypes.bool
  };

  static defaultProps = {
    style: {},
    styleGradient: {},
    text: Strings.continueButton,
    onPress: () => {},
    inBottom: true,
    isDisable: false,
    customView: undefined,
    disableRipple: false,
    setKeyboardEvent: true
  };

  state = {
    isDisable: this.props.isDisable,
    isKeyboardVisible: false,
    text: this.props.text,
    showLoader: false
  };

  componentWillMount() {
    if (Utils.isPlatformAndroid() && this.props.setKeyboardEvent) {
      this.keyboardDidShowListener = Keyboard.addListener(
        "keyboardDidShow",
        this._keyboardDidShow
      );
      this.keyboardDidHideListener = Keyboard.addListener(
        "keyboardDidHide",
        this._keyboardDidHide
      );
    }
  }

  componentWillReceiveProps(nextProps) {
    this.setState({ text: nextProps.text, isDisable: nextProps.isDisable });
  }

  componentWillUnmount() {
    if (Utils.isPlatformAndroid() && this.props.setKeyboardEvent) {
      this.keyboardDidShowListener.remove();
      this.keyboardDidHideListener.remove();
    }
  }

  setDisable = isDisable => {
    this.setState({ isDisable });
  };

  setLoader = showLoader => {
    this.setState({ showLoader });
  };

  setText = text => {
    this.setState({ text });
  };

  _keyboardDidShow = () => {
    this.setState({ isKeyboardVisible: true });
  };

  _keyboardDidHide = () => {
    this.setState({ isKeyboardVisible: false });
  };

  _renderText() {
    const { text, showLoader } = this.state;
    if (showLoader) {
      return (
        <ActivityIndicator
          animating
          size="small"
          color={Colors.background.secondary}
        />
      );
    }
    return (
      <Text size="medium" type="bold2" color="tertiary">
        {text}
      </Text>
    );
  }

  _renderGradientView() {
    const {
      style,
      onPress,
      inBottom,
      customView,
      disableRipple,
      styleGradient
    } = this.props;
    const containerStyle = inBottom ? styles.bottom : style;

    return (
      <ButtonView
        onPress={onPress}
        style={containerStyle}
        disableRipple={disableRipple}
      >
        <LinearGradient
          colors={Colors.background.gradient}
          style={[styles.linearGradient, styleGradient]}
          start={GRADIENT_START}
          end={GRADIENT_END}
        >
          {customView ? customView() : this._renderText()}
        </LinearGradient>
      </ButtonView>
    );
  }

  _renderDisableView() {
    const { text } = this.state;
    const { style, inBottom } = this.props;
    const containerStyle = inBottom ? styles.bottom : style;
    return (
      <View style={[styles.disableView, containerStyle]}>
        {this._renderText(text)}
      </View>
    );
  }

  render() {
    const { isDisable, isKeyboardVisible } = this.state;
    if (isKeyboardVisible) {
      return null;
    } else if (isDisable) {
      return this._renderDisableView();
    }
    return this._renderGradientView();
  }
}
