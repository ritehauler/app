// @flow
import React, { Component } from "react";
import PropTypes from "prop-types";
import { View, Image } from "react-native";

import styles from "./styles";
import { Text, ButtonView } from "../";
import { Images, Strings } from "../../theme";

export default class NoInternetViewBottomRequest extends Component {
  static propTypes = {
    onRetryPress: PropTypes.func
  };

  static defaultProps = {
    onRetryPress: () => {}
  };

  render() {
    const { onRetryPress } = this.props;
    return (
      <ButtonView style={styles.container} onPress={onRetryPress}>
        <Text type="light" size="small" style={styles.text}>
          {Strings.noInternetMessage}
        </Text>
        <Image source={Images.retryUpload} style={styles.image} />
      </ButtonView>
    );
  }
}
