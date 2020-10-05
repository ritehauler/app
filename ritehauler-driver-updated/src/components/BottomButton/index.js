// @flow
import PropTypes from "prop-types";
import React, { PureComponent } from "react";
import { Button, Image, View } from "react-native";
import { ButtonView, Text } from "../index";
import { Colors, Metrics } from "../../theme";
import LinearGradient from "react-native-linear-gradient";

export default class BottomButton extends PureComponent {
  static propTypes = {
    title: PropTypes.string.isRequired,
    style: PropTypes.oneOfType([PropTypes.number, PropTypes.object]),
    onPress: PropTypes.func,
    color: PropTypes.string,
    size: PropTypes.string,
    type: PropTypes.string
  };

  static defaultProps = {
    style: {},
    onPress: () => {},
    color: Colors.text.tertiary,
    size: "large",
    type: "dBold"
  };

  _onPress = onPress => {
    if (onPress) {
      onPress();
    }
  };

  renderOneTitle() {
    const { title, style, onPress, color, size, type, image } = this.props;
    return (
      <ButtonView
        onPress={() => this._onPress(onPress)}
        style={[
          {
            flexDirection: "row",
            flex: 1,
            backgroundColor: Colors.transparent,
            alignItems: "center",
            paddingHorizontal: Metrics.baseMargin
          }
        ]}
      >
        <Text
          style={{ textAlign: "center", flex: 1 }}
          title={title}
          type={type}
          background="accent"
          color={color}
          size={size}
        >
          {title}
        </Text>
        {image && <Image source={image} />}
      </ButtonView>
    );
  }

  renderTwoTitle() {
    const {
      title,
      title2,
      style,
      onPress,
      onPress2,
      color,
      size,
      type
    } = this.props;
    return (
      <View
        //onPress={onPress}
        style={[
          {
            flexDirection: "row",
            flex: 1,
            backgroundColor: Colors.transparent
          }
        ]}
      >
        <ButtonView
          onPress={() => this._onPress(onPress)}
          style={{ flex: 1, alignItems: "center", justifyContent: "center" }}
        >
          <Text
            style={{ textAlign: "center" }}
            title={title}
            type={type}
            background="accent"
            color={color}
            size={size}
          >
            {title}
          </Text>
        </ButtonView>
        <View
          alignSelf="center"
          style={{
            width: Metrics.ratio(0.8),
            height: Metrics.ratio(20),
            backgroundColor: "white"
          }}
        />
        <ButtonView
          onPress={() => this._onPress(onPress2)}
          style={{ flex: 1, alignItems: "center", justifyContent: "center" }}
        >
          <Text
            style={{ textAlign: "center" }}
            title={title}
            type={type}
            background="accent"
            color={color}
            size={size}
          >
            {title2}
          </Text>
        </ButtonView>
      </View>
    );
  }

  render() {
    const { style, title, title2, greyBackground } = this.props;
    return (
      <LinearGradient
        colors={greyBackground ? Colors.lgDarkGreyArray : Colors.lgColArray}
        start={{ x: 0.0, y: 0 }}
        end={{ x: 0.8, y: 0 }}
        style={[
          {
            flexDirection: "row",
            height: Metrics.bottomButtonHeight
          },
          style
        ]}
      >
        {title && title2 ? this.renderTwoTitle() : this.renderOneTitle()}
      </LinearGradient>
    );
  }
}
