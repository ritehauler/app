// @flow

import React, { Component } from "react";
import { View, ActivityIndicator } from "react-native";

import styles from "./styles";
import { Colors } from "../../theme";

export default class LoadingFooterRequest extends Component {
  render() {
    return (
      <View style={styles.container}>
        <ActivityIndicator size="small" color={Colors.background.quaternary} />
      </View>
    );
  }
}
