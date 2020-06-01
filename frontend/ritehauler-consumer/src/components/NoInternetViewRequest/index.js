// @flow
import React, { Component } from "react";
import PropTypes from "prop-types";
import { View } from "react-native";

import styles from "./styles";
import { Text } from "../";
import { Strings } from "../../theme";
import { GradientButton } from "../../appComponents";

export default class NoInternetViewRequest extends Component {
  static propTypes = {
    onRetryPress: PropTypes.func,
    message: PropTypes.string,
    retryText: PropTypes.string,
    style: PropTypes.oneOfType([
      PropTypes.array,
      PropTypes.object,
      PropTypes.number
    ])
  };

  static defaultProps = {
    onRetryPress: () => {},
    message: Strings.noInternetMessage,
    retryText: Strings.retry,
    style: {}
  };

  render() {
    const { onRetryPress, message, retryText, style } = this.props;
    return (
      <View style={[styles.container, style]}>
        <Text style={styles.text}>{message}</Text>
        <GradientButton
          onPress={onRetryPress}
          style={styles.buttonRetry}
          styleGradient={styles.styleGradient}
          text={retryText}
          inBottom={false}
          setKeyboardEvent={false}
        />
      </View>
    );
  }
}
