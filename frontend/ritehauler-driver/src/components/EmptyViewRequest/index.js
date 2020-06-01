// @flow

import React, { Component } from "react";
import PropTypes from "prop-types";
import { View } from "react-native";

import styles from "./styles";
import { Text } from "../";

export default class EmptyViewRequest extends Component {
  static propTypes = {
    message: PropTypes.string
  };

  static defaultProps = {
    message: "No Records Found"
  };

  render() {
    const { message } = this.props;
    return (
      <View style={styles.container}>
        <Text style={styles.message}>{message}</Text>
      </View>
    );
  }
}
