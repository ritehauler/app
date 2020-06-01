// @flow
import _ from "lodash";
import React, { Component } from "react";
import PropTypes from "prop-types";
import { Text as TextRN, StyleSheet } from "react-native";

import { Fonts, Colors, ApplicationStyles } from "../../theme";

export default class TextWithState extends Component {
  static propTypes = {
    ...TextRN.propTypes,
    color: PropTypes.string,
    size: PropTypes.oneOfType([
      PropTypes.oneOf(_.keys(Fonts.size)),
      PropTypes.number
    ]),
    children: PropTypes.node.isRequired,
    type: PropTypes.oneOf(_.keys(Fonts.type)),
    textAlign: PropTypes.oneOf(["auto", "left", "right", "center", "justify"])
  };

  static defaultProps = {
    ...TextRN.defaultProps,
    size: "normal",
    type: "base",
    color: "primary",
    textAlign: "left"
  };

  state = {
    children: this.props.children
  };

  shouldComponentUpdate(nextProps, nextState) {
    return !_.isEqual(nextState.children, this.state.children);
  }

  setText = text => {
    this.setState({
      children: text
    });
  };

  render() {
    const { style, color, size, type, textAlign, ...rest } = this.props;
    const { children } = this.state;

    const textStyle = StyleSheet.flatten([
      {
        textAlign,
        fontFamily: Fonts.type.tBold,
        fontSize: Fonts.size.medium,
        color: Colors.text.tertiary,
        backgroundColor: Colors.transparent
      },
      style
    ]);

    return (
      <TextRN style={textStyle} {...rest}>
        {children}
      </TextRN>
    );
  }
}
