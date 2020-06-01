// @flow
// ViewPropTypes
import _ from "lodash";
import React from "react";
import PropTypes from "prop-types";
import {
  View,
  StyleSheet,
  TouchableOpacity,
  ActivityIndicator,
  TouchableNativeFeedback
} from "react-native";

import Text from "../Text";
import Util from "../../util";
import { Fonts, Colors, Metrics } from "../../theme";

const styles = StyleSheet.create({
  button: {
    flexDirection: "row",
    alignSelf: "stretch",
    alignItems: "center",
    justifyContent: "center",
    height: Metrics.defaultUIHeight,
    borderRadius: Metrics.borderRadius
  },
  spinner: {
    alignSelf: "center"
  },
  opacity: {
    opacity: 0.5
  }
});

export default class Button extends React.PureComponent {
  static propTypes = {
    style: PropTypes.oneOfType([
      PropTypes.array,
      PropTypes.object,
      PropTypes.number
    ]),
    disabled: PropTypes.bool,
    isLoading: PropTypes.bool,
    textStyle: Text.propTypes.style,
    indicatorColor: PropTypes.string,
    title: PropTypes.string.isRequired,
    size: PropTypes.oneOf(_.keys(Fonts.size)),
    type: PropTypes.oneOf(_.keys(Fonts.type)),
    color: PropTypes.string,
    background: PropTypes.string
  };

  static defaultProps = {
    style: {},
    type: "base",
    size: "large",
    color: "primary",
    disabled: false,
    isLoading: false,
    indicatorColor: "black",
    background: "secondary",
    textStyle: Text.defaultProps.text
  };

  _renderInnerText(title, color, size, type, textStyle, isLoading) {
    if (isLoading) {
      return (
        <ActivityIndicator
          animating
          size="small"
          style={styles.spinner}
          color={Colors.accent}
        />
      );
    }

    return (
      <Text color={color} size={size} type={type} style={textStyle}>
        {title}
      </Text>
    );
  }

  render() {
    const {
      size,
      type,
      title,
      style,
      color,
      disabled,
      isLoading,
      textStyle,
      background,
      indicatorColor,
      ...rest
    } = this.props;

    const buttonStyle = StyleSheet.flatten([
      styles.button,
      {
        backgroundColor:
          background in Colors.background
            ? Colors.background[background]
            : background
      },
      style,
      disabled && styles.opacity
    ]);

    if (Util.isPlatformAndroid()) {
      return (
        <TouchableNativeFeedback disabled={disabled} {...rest}>
          <View style={buttonStyle}>
            {this._renderInnerText(
              title,
              color,
              size,
              type,
              textStyle,
              isLoading,
              indicatorColor
            )}
          </View>
        </TouchableNativeFeedback>
      );
    }

    return (
      <TouchableOpacity disabled={disabled} style={buttonStyle} {...rest}>
        {this._renderInnerText(
          title,
          color,
          size,
          type,
          textStyle,
          isLoading,
          indicatorColor
        )}
      </TouchableOpacity>
    );
  }
}
