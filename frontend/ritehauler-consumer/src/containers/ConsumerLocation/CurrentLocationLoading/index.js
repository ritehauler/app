// @flow
import { View } from "react-native";
import PropTypes from "prop-types";
import React from "react";

import { Colors } from "../../../theme";
import styles from "./styles";

const Spinner = require("react-native-spinkit");

export default class CurrentLocationLoading extends React.PureComponent {
  static propTypes = {
    hide: PropTypes.bool
  };

  static defaultProps = {
    hide: true
  };

  render() {
    const { hide } = this.props;
    if (hide) {
      return null;
    }
    return (
      <View style={styles.container}>
        <Spinner
          size={60}
          type="ThreeBounce"
          color={Colors.background.secondary}
        />
      </View>
    );
  }
}
