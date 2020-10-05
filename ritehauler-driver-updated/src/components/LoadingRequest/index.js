// @flow

import React, { Component } from "react";
import { View, ActivityIndicator } from "react-native";

import styles from "./styles";
import { Colors } from "../../theme";

export default class LoadingRequest extends Component {
  render() {
    return (
      <View style={styles.container}>
        <ActivityIndicator size="large" color={Colors.accent} />
      </View>
    );
  }
}
