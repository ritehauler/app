// @flow
import React from "react";
import PropTypes from "prop-types";
import Modal from "react-native-modal";
import { View, StatusBar } from "react-native";

import styles from "./styles";
import { Colors } from "../../theme";
import { Text } from "../../components";

const Spinner = require("react-native-spinkit");

export default class Loader extends React.PureComponent {
  static propTypes = {
    loading: PropTypes.bool,
    textMessage: PropTypes.string
  };

  static defaultProps = {
    loading: false,
    textMessage: ""
  };

  state = {
    loading: this.props.loading,
    textMessage: this.props.textMessage
  };

  componentWillReceiveProps(nextProps) {
    this.setState({
      loading: nextProps.loading,
      textMessage: nextProps.textMessage
    });
  }

  setLoading = loading => {
    this.setState({ loading });
  };

  setTextMessage = textMessage => {
    this.setState({ textMessage });
  };

  render() {
    const { loading, textMessage } = this.state;
    return (
      <View>
        <StatusBar networkActivityIndicatorVisible={loading} />
        <Modal
          style={styles.modal}
          backdropOpacity={0.1}
          animationIn="fadeIn"
          animationOut="fadeOut"
          isVisible={loading}
        >
          <View style={styles.container}>
            {textMessage !== "" && (
              <Text size="normal" type="medium" style={styles.loadingMessage}>
                {textMessage}
              </Text>
            )}
            <Spinner
              isVisible
              size={50}
              type="ThreeBounce"
              color={Colors.accent}
            />
          </View>
        </Modal>
      </View>
    );
  }
}
