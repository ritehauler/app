// @flow

// TODO: Add props for description such as color, size, type, textAlign
import _ from "lodash";
import React from "react";
import PropTypes from "prop-types";
import { View, Image, Switch, Platform, Animated } from "react-native";
import { Actions } from "react-native-router-flux";
import Utils from "../../../util";
import styles from "./styles";
import { ButtonView, Text } from "../../";
import { Fonts, Colors } from "../../../theme";

export default class ListItem extends React.PureComponent {
  static propTypes = {
    title: PropTypes.string,
    rightText: PropTypes.string,
    onPress: PropTypes.func,
    onToggle: PropTypes.func,
    toggle: PropTypes.object,
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
    rightTextcolor: PropTypes.oneOfType([
      PropTypes.oneOf(_.keys(Colors.text)),
      PropTypes.string
    ]),
    rightTextsize: PropTypes.oneOfType([
      PropTypes.oneOf(_.keys(Fonts.size)),
      PropTypes.number
    ]),
    rightTexttype: PropTypes.oneOf(_.keys(Fonts.type)),
    textAlign: PropTypes.oneOf(["auto", "left", "right", "center", "justify"]),
    numberOfLines: PropTypes.number
  };

  static defaultProps = {
    activeOpacity: 1,
    title: undefined,
    rightText: undefined,
    toggle: undefined,
    onPress: undefined,
    onToggle: undefined,
    navigate: undefined,
    rightImage: undefined,
    type: "base",
    size: "normal",
    color: "primary",
    rightTextcolor: "placeholder",
    rightTextsize: "xSmall",
    rightTexttype: "base",
    textAlign: "left",
    numberOfLines: 1
  };

  constructor(props) {
    super(props);
  }

  static onEnter() {
    Actions.refresh({ time: new Date() });
  }

  state = {
    toggleValue: (this.props.toggle && this.props.toggle.value) || false
  };

  _onPress = () => {
    const { onPress } = this.props;
    if (onPress) {
      onPress();
    }
  };

  _onToggle = toggleValue => {
    const { onToggle } = this.props;
    if (onToggle) {
      onToggle(toggleValue);
    }
  };

  handleToggleBtn = toggleValue => {
    this.setState({ toggleValue });
    this._onToggle(toggleValue);
  };

  renderTitle() {
    const { title, type, size, color, textAlign, numberOfLines } = this.props;

    return (
      <View style={styles.title}>
        {title && (
          <Text
            color={color}
            type={type}
            size={size}
            bold
            textAlign={textAlign}
            numberOfLines={numberOfLines}
          >
            {title}
          </Text>
        )}
      </View>
    );
  }

  renderRightText() {
    const {
      rightText,
      rightTexttype,
      rightTextsize,
      rightTextcolor
    } = this.props;

    if (rightText) {
      return (
        <Text color={rightTextcolor} type={rightTexttype} size={rightTextsize}>
          {rightText}
        </Text>
      );
    }

    return null;
  }

  renderRightImage() {
    const { rightImage } = this.props;
    if (rightImage) {
      return (
        <Image
          source={rightImage}
          style={styles.rightImage}
          resizeMode="contain"
        />
      );
    }

    return null;
  }

  renderSwitch() {
    const { toggle } = this.props;
    if (toggle) {
      return (
        <Switch
          style={{
            right: Platform.select({
              ios: 0,
              android: -6
            })
          }}
          value={this.state.toggleValue}
          onValueChange={this.handleToggleBtn}
          onTintColor={
            Utils.isPlatformAndroid() ? Colors.switchTint : Colors.text.accent
          }
        />
      );
    }

    return null;
  }

  render() {
    return (
      <ButtonView style={styles.container} onPress={this._onPress}>
        {this.renderTitle()}
        {this.renderSwitch()}
        {this.renderRightText()}
        {this.renderRightImage()}
      </ButtonView>
    );
  }
}
