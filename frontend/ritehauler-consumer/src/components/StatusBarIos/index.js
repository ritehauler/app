// @flow
import React from "react";
import { View } from "react-native";

import styles from "./styles";
import Utils from "../../util";

export default class StatusBarIos extends React.Component {
  render() {
    if (Utils.isPlatformAndroid()) {
      return null;
    }
    return <View style={styles.iosStatusBarHeight} />;
  }
}
