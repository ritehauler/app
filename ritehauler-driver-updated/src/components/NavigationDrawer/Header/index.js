// @flow

// TODO: Add props for description such as color, size, type, textAlign
import _ from "lodash";
import React from "react";
import PropTypes from "prop-types";
import { View, Image, Switch } from "react-native";
import { Actions } from "react-native-router-flux";

import styles from "./styles";
import { ButtonView, Text } from "../../";
import { Fonts, Colors, Images } from "../../../theme";

export default class Header extends React.Component {
  static propTypes = {
    title: PropTypes.string,
    onPress: PropTypes.func,
    rightImage: PropTypes.oneOfType([PropTypes.number, PropTypes.object]),
    color: PropTypes.oneOfType([
      PropTypes.oneOf(_.keys(Colors.text)),
      PropTypes.string
    ]),
    background: PropTypes.oneOfType([
      PropTypes.oneOf(_.keys(Colors.background)),
      PropTypes.string
    ]),
    size: PropTypes.oneOfType([
      PropTypes.oneOf(_.keys(Fonts.size)),
      PropTypes.number
    ]),
    //type: PropTypes.oneOf(_.keys(Fonts.type)),
    textAlign: PropTypes.oneOf(["auto", "left", "right", "center", "justify"]),
    numberOfLines: PropTypes.number
  };

  static defaultProps = {
    activeOpacity: 1,
    title: undefined,
    onPress: undefined,
    rightImage: undefined,
    background: Colors.background.primary,
    //type: "bold",
    size: "large",
    color: "primary",
    textAlign: "left",
    numberOfLines: 1
  };

  _onPress = () => {
    const { onPress } = this.props;
    if (onPress) {
      onPress();
    }
  };

  renderTitle() {
    const { title, type, size, color, textAlign, numberOfLines } = this.props;

    return (
      <View style={styles.title}>
        {title && (
          <Text
            color={color}
            style={{ fontFamily: type }}
            size={size}
            textAlign={textAlign}
            numberOfLines={numberOfLines}
          >
            {title}
          </Text>
        )}
      </View>
    );
  }

  renderRightImage() {
    return (
      <Image
        source={Images.arrowNext}
        style={{ marginLeft: 10 }}
        resizeMode="contain"
      />
    );
  }

  render() {
    const { background } = this.props;
    return (
      <ButtonView
        style={[styles.container, { backgroundColor: background }]}
        onPress={this._onPress}
      >
        {this.renderTitle()}
        {this.renderRightImage()}
      </ButtonView>
    );
  }
}
