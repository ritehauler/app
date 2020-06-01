// @flow
import React, { Component } from "react";
import PropTypes from "prop-types";
import { View, Image } from "react-native";

import styles from "./styles";
import { Text, ButtonView } from "../";
import { Images } from "../../theme";

export default class NoInternetViewRequest extends Component {
  static propTypes = {
    onRetryPress: PropTypes.func,
    message: PropTypes.string,
    retryText: PropTypes.string
  };

  static defaultProps = {
    onRetryPress: () => {},
    message:
      "No internet connection. Make sure wi-fi or celluar data is turned on, then try again",
    retryText: "Try Again"
  };

  render() {
    const { onRetryPress, message, retryText } = this.props;
    return (
      <View style={styles.container}>
        <Image source={Images.noInternet} style={styles.noInternetImage} />
        <Text size="xxLarge" color="secondary" type="bold" style={styles.ops}>
          Ooops!
        </Text>
        <Text color="secondary" style={styles.message}>
          {message}
        </Text>
        <ButtonView style={styles.leftButtonView} onPress={onRetryPress}>
          <Text color="secondary" type="medium" size="xSmall">
            {retryText}
          </Text>
        </ButtonView>
      </View>
    );
  }
}

/*
return (
      <View style={styles.container}>
        <Text size="medium" type="light" style={styles.text}>
          {message}
        </Text>
        <ButtonView onPress={onRetryPress} style={styles.buttonRetry}>
          <Text size="medium" type="light">
            {retryText}
          </Text>
        </ButtonView>
      </View>
    );
*/

/*

*/
