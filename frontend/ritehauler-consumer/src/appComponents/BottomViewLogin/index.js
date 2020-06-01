// @flow
import React from "react";
import { View, Keyboard } from "react-native";
import PropTypes from "prop-types";

import styles from "./styles";
import { Text, ButtonView } from "../../components";
import Utils from "../../util";

export default class BottomViewLogin extends React.PureComponent {
  static propTypes = {
    onPress: PropTypes.func,
    text: PropTypes.string,
    clickableText: PropTypes.string
  };

  static defaultProps = {
    onPress: () => {},
    text: "",
    clickableText: ""
  };

  state = {
    isKeyboardVisible: false
  };

  componentWillMount() {
    if (Utils.isPlatformAndroid()) {
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

  componentWillUnmount() {
    if (Utils.isPlatformAndroid()) {
      this.keyboardDidShowListener.remove();
      this.keyboardDidHideListener.remove();
    }
  }

  _keyboardDidShow = () => {
    this.setState({ isKeyboardVisible: true });
  };

  _keyboardDidHide = () => {
    this.setState({ isKeyboardVisible: false });
  };

  render() {
    const { isKeyboardVisible } = this.state;
    const { onPress, text, clickableText } = this.props;
    if (isKeyboardVisible) {
      return null;
    }
    return (
      <View style={styles.container}>
        <Text size="xSmall" type="light">
          {text}
        </Text>
        <ButtonView
          isBackgroundBorderLess
          style={styles.button}
          onPress={onPress}
        >
          <Text size="xSmall" type="medium" color="accent">
            {clickableText}
          </Text>
        </ButtonView>
      </View>
    );
  }
}
