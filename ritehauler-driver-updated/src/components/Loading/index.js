// @flow

import React from "react";
import PropTypes from "prop-types";
import Modal from "react-native-modal";
import { View, StatusBar, ActivityIndicator } from "react-native";

import styles from "./styles";
import { Colors } from "../../theme";

export default class Loading extends React.Component {
  static propTypes = {
    loading: PropTypes.bool
  };

  static defaultProps = {
    loading: false
  };

  render() {
    const { loading } = this.props;
    return (
      <Modal
        style={{ margin: 0 }}
        backdropOpacity={0.4}
        animationIn="fadeIn"
        animationOut="fadeOut"
        isVisible={loading}
      >
        <ActivityIndicator
          animating
          size="large"
          color={Colors.background.accent}
        />
      </Modal>
    );
  }
}
