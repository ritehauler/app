// @flow
import React, { Component } from "react";
import { View, Image } from "react-native";
import PropTypes from "prop-types";
import { ButtonView, Text } from "../../../components";
import styles from "./styles";
import { Images } from "../../../theme";

class NavigationView extends Component {
  static propTypes = {
    isPrevious: PropTypes.bool,
    isNext: PropTypes.bool,
    onPressNext: PropTypes.func,
    onPressPrevious: PropTypes.func,
    title: PropTypes.string
  };

  static defaultProps = {
    isPrevious: false,
    isNext: false,
    onPressNext: () => {},
    onPressPrevious: () => {},
    title: ""
  };

  renderNextButton() {
    const { isNext } = this.props;

    if (isNext) {
      return (
        <ButtonView onPress={this.props.onPressNext} style={styles.buttonStyle}>
          <Image source={Images.next} />
        </ButtonView>
      );
    }

    return <View style={styles.buttonStyle} />;
  }

  renderPreviousButton() {
    const { isPrevious } = this.props;

    if (isPrevious) {
      return (
        <ButtonView
          onPress={this.props.onPressPrevious}
          style={styles.buttonStyle}
        >
          <Image source={Images.iconPreviousNavigation} />
        </ButtonView>
      );
    }

    return <View style={styles.buttonStyle} />;
  }

  renderTitle() {
    return (
      <View style={styles.titleContainerStyle}>
        <Text type="medium">{this.props.title}</Text>
      </View>
    );
  }

  render() {
    return (
      <View style={styles.containerStyle}>
        {this.renderPreviousButton()}
        {this.renderTitle()}
        {this.renderNextButton()}
      </View>
    );
  }
}

export default NavigationView;
