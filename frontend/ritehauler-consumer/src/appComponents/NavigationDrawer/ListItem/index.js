// @flow
import _ from "lodash";
import React from "react";
import PropTypes from "prop-types";
import { Image } from "react-native";

import styles from "./styles";
import { ButtonView, Text } from "../../../components";
import { Fonts, Colors } from "../../../theme";

export default class ListItem extends React.PureComponent {
  static propTypes = {
    title: PropTypes.string.isRequired,
    onPress: PropTypes.func,
    rightText: PropTypes.string,
    rightImage: PropTypes.oneOfType([PropTypes.number, PropTypes.object]),
    color: PropTypes.oneOfType([
      PropTypes.oneOf(_.keys(Colors.text)),
      PropTypes.string
    ]),
    size: PropTypes.oneOfType([
      PropTypes.oneOf(_.keys(Fonts.size)),
      PropTypes.number
    ]),
    type: PropTypes.oneOf(_.keys(Fonts.type)),
    rightTextColor: PropTypes.oneOfType([
      PropTypes.oneOf(_.keys(Colors.text)),
      PropTypes.string
    ]),
    rightTextSize: PropTypes.oneOfType([
      PropTypes.oneOf(_.keys(Fonts.size)),
      PropTypes.number
    ]),
    rightTextType: PropTypes.oneOf(_.keys(Fonts.type)),
    textAlign: PropTypes.oneOf(["auto", "left", "right", "center", "justify"]),
    numberOfLines: PropTypes.number,
    customStyle: PropTypes.oneOfType([PropTypes.number, PropTypes.object])
  };

  static defaultProps = {
    onPress: () => {},
    rightText: undefined,
    rightImage: undefined,
    size: "small",
    type: "light",
    color: "primary",
    rightTextColor: "accent",
    rightTextSize: "xxxSmall",
    rightTextType: "base",
    textAlign: "left",
    numberOfLines: 1,
    customStyle: {}
  };

  _renderTitle() {
    const { title, type, size, color, textAlign, numberOfLines } = this.props;
    return (
      <Text
        color={color}
        type={type}
        size={size}
        textAlign={textAlign}
        numberOfLines={numberOfLines}
        style={styles.title}
      >
        {title}
      </Text>
    );
  }

  _renderRightText() {
    const {
      rightText,
      rightTextType,
      rightTextSize,
      rightTextColor
    } = this.props;

    if (rightText) {
      return (
        <Text color={rightTextColor} type={rightTextType} size={rightTextSize}>
          {rightText}
        </Text>
      );
    }
    return null;
  }

  _renderRightImage() {
    const { rightImage } = this.props;
    if (rightImage) {
      return <Image source={rightImage} style={styles.rightImage} />;
    }
    return null;
  }

  render() {
    const { onPress, customStyle } = this.props;
    return (
      <ButtonView style={[styles.container, customStyle]} onPress={onPress}>
        {this._renderTitle()}
        {this._renderRightText()}
        {this._renderRightImage()}
      </ButtonView>
    );
  }
}
