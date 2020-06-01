import React from "react";
import PropTypes from "prop-types";
import Modal from "react-native-modal";
import { View } from "react-native";

import { Text, ButtonView } from "../../components";
import { GradientButton } from "../../appComponents";
import styles from "./styles";
import { Strings } from "../../theme";

export default class Message extends React.PureComponent {
  static propTypes = {
    onPress: PropTypes.func.isRequired,
    description: PropTypes.string.isRequired,
    rightButtonTitle: PropTypes.string,
    leftButtonTitle: PropTypes.string,
    isCancelable: PropTypes.bool
  };

  static defaultProps = {
    rightButtonTitle: Strings.ok,
    leftButtonTitle: Strings.cancel,
    isCancelable: false
  };

  state = {
    isVisible: false
  };

  show() {
    this.setState({ isVisible: true });
  }

  hide = () => {
    this.setState({
      isVisible: false
    });
  };

  _renderDescription() {
    const { description } = this.props;
    return (
      <Text style={styles.descriptionStyle} type="medium" numberOfLines={3}>
        {description}
      </Text>
    );
  }

  _renderCancelButton() {
    const { leftButtonTitle, isCancelable } = this.props;

    if (isCancelable) {
      return (
        <ButtonView style={styles.cancelButton} onPress={this.hide}>
          <Text type="medium" size="small">
            {leftButtonTitle}
          </Text>
        </ButtonView>
      );
    }
    return null;
  }

  _renderGradientButton() {
    const { onPress, rightButtonTitle } = this.props;
    return (
      <GradientButton
        onPress={onPress}
        style={styles.gradientContainer}
        styleGradient={styles.gradientButton}
        text={rightButtonTitle}
        inBottom={false}
      />
    );
  }

  _renderButtons() {
    return (
      <View style={styles.flexRow}>
        {this._renderCancelButton()}
        {this._renderGradientButton()}
      </View>
    );
  }

  render() {
    const { isVisible } = this.state;

    return (
      <Modal
        isVisible={isVisible}
        style={styles.modal}
        animationIn="fadeIn"
        animationOut="fadeOut"
        onBackdropPress={this.hide}
        onBackButtonPress={this.hide}
      >
        <View style={styles.body}>
          {this._renderDescription()}
          {this._renderButtons()}
        </View>
      </Modal>
    );
  }
}
