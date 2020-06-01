// @flow
import React, { Component } from "react";
import { View, ViewPropTypes } from "react-native";
import PropTypes from "prop-types";
import styles from "./styles";

export default class Separator extends Component {
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
