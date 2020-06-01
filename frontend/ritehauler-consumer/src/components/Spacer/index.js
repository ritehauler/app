// @flow
import React from "react";
import { View } from "react-native";

import styles from "./styles";

export default class Separator extends React.Component {
  render() {
    const { ...rest } = this.props;
    return <View style={styles.container} {...rest} />;
  }
}
