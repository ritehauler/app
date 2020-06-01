// @flow
import React from "react";
import PropTypes from "prop-types";
import { Image, View } from "react-native";

import { Text, ButtonView } from "../../components";
import { Images, Strings } from "../../theme";
import styles from "./styles";

export default class PickUpLocation extends React.PureComponent {
  static propTypes = {
    showLine: PropTypes.bool,
    text: PropTypes.string,
    numberOfLines: PropTypes.number,
    title: PropTypes.string,
    onPress: PropTypes.func,
    disableRipple: PropTypes.bool
  };

  static defaultProps = {
    showLine: true,
    text: "",
    numberOfLines: 3,
    title: Strings.pickupLocation,
    onPress: () => {},
    disableRipple: false
  };

  _renderImageView() {
    const { showLine } = this.props;
    return (
      <View style={styles.imageContainer}>
        <Image source={Images.pickUp} style={styles.image} />
        {showLine && <View style={styles.line} />}
      </View>
    );
  }

  _renderContentView() {
    const { numberOfLines, text, title } = this.props;
    return (
      <View style={styles.contentContainer}>
        <Text size="small">{title}</Text>
        <Text
          color="secondary"
          size="xxSmall"
          style={styles.textPickUp}
          numberOfLines={numberOfLines}
        >
          {text}
        </Text>
      </View>
    );
  }

  render() {
    const { onPress, disableRipple } = this.props;
    return (
      <ButtonView
        style={styles.container}
        disableRipple={disableRipple}
        onPress={onPress}
      >
        {this._renderImageView()}
        {this._renderContentView()}
      </ButtonView>
    );
  }
}
