// @flow
import React, { Component } from "react";
import PropTypes from "prop-types";
import { View } from "react-native";

import styles from "./styles";
import { Text } from "../";
import { Strings } from "../../theme";

export default class EmptyViewRequest extends Component {
  static propTypes = {
    message: PropTypes.string,
    style: PropTypes.oneOfType([
      PropTypes.array,
      PropTypes.object,
      PropTypes.number
    ])
  };

  static defaultProps = {
    message: Strings.noRecordFound,
    style: {}
  };

  render() {
    const { message, style } = this.props;
    return (
      <View style={[styles.container, style]}>
        <Text style={styles.message}>{message}</Text>
      </View>
    );
  }
}
