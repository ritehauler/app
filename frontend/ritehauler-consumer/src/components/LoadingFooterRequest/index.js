// @flow

import React, { Component } from "react";
import { View, ActivityIndicator } from "react-native";

import styles from "./styles";
import { Colors } from "../../theme";

const Spinner = require("react-native-spinkit");

export default class LoadingFooterRequest extends Component {
  render() {
    return (
      <View style={styles.container}>
        <Spinner isVisible size={50} type="ThreeBounce" color={Colors.accent} />
      </View>
    );
  }
}
