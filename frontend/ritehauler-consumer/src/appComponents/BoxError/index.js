// @flow
import React from "react";
import { View } from "react-native";
import PropTypes from "prop-types";

import { Text } from "../../components";
import { Strings } from "../../theme";
import styles from "./styles";

export default class BoxError extends React.PureComponent {
  static propTypes = {
    showError: PropTypes.bool,
    errorMessage: PropTypes.string,
    errorContainerStyle: PropTypes.oneOfType([
      PropTypes.array,
      PropTypes.object,
      PropTypes.number
    ]),
    containerStyle: PropTypes.oneOfType([
      PropTypes.array,
      PropTypes.object,
      PropTypes.number
    ])
  };

  static defaultProps = {
    errorContainerStyle: {},
    containerStyle: {},
    showError: false,
    errorMessage: Strings.errorMessageUploadImage
  };

  state = {
    showError: this.props.showError,
    errorMessage: this.props.errorMessage
  };

  setShowError = showError => {
    this.setState({ showError });
  };

  setMessageAndError = (showError, errorMessage) => {
    this.setState({ showError, errorMessage });
  };

  render() {
    const { showError, errorMessage } = this.state;
    const { errorContainerStyle, containerStyle } = this.props;
    if (showError) {
      return (
        <View style={[styles.errorContainer, errorContainerStyle]}>
          <View style={styles.line} />
          <Text size="xSmall" color="error" style={styles.errorMessage}>
            {errorMessage}
          </Text>
        </View>
      );
    }
    return <View style={[styles.container, containerStyle]} />;
  }
}
