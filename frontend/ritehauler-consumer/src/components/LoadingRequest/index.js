// @flow

import React, { Component } from "react";
import { View } from "react-native";
import PropTypes from "prop-types";

import styles from "./styles";
import { Colors } from "../../theme";

const Spinner = require("react-native-spinkit");

export default class LoadingRequest extends Component {
  static propTypes = {
    style: PropTypes.oneOfType([
      PropTypes.array,
      PropTypes.object,
      PropTypes.number
    ])
  };

  static defaultProps = {
    style: {}
  };
  render() {
    const { style } = this.props;
    return (
      <View style={[styles.container, style]}>
        <Spinner isVisible size={50} type="ThreeBounce" color={Colors.accent} />
      </View>
    );
  }
}
