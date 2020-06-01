// @flow
import React from "react";
import { View, ViewPropTypes } from "react-native";

import styles from "./styles";

export default class Separator extends React.Component {
  static propTypes = {
    style: ViewPropTypes.style
  };

  static defaultProps = {
    style: {}
  };

  render() {
    const { style } = this.props;

    return <View style={[styles.container, style]} />;
  }
}
