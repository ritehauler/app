import React, { Component } from "react";
import {
  StyleSheet,
  Text,
  View,
  TextInput,
  Animated,
  Platform
} from "react-native";
import PropTypes from "prop-types";

import { Metrics } from "../../theme";

class FloatingLabel extends Component {
  constructor(props) {
    super(props);

    let initialPadding = 9;
    let initialOpacity = 0;
    if (this.props.visible) {
      initialPadding = 0;
      initialOpacity = 1;
    }
    this.paddingTop = initialPadding;

    this.state = {
      paddingAnim: new Animated.Value(initialPadding),
      opacityAnim: new Animated.Value(initialOpacity)
    };
  }

  componentWillReceiveProps(newProps) {
    if (
      (newProps.visible && this.paddingTop !== 0) ||
      (!newProps.visible && this.paddingTop === 0)
    ) {
      this.paddingTop = newProps.visible ? 0 : 9;
      Animated.timing(this.state.paddingAnim, {
        toValue: newProps.visible ? 0 : 9,
        duration: 180
      }).start();

      return Animated.timing(this.state.opacityAnim, {
        toValue: newProps.visible ? 1 : 0,
        duration: 180
      }).start();
    }
  }

  render() {
    return (
      <Animated.View
        style={[
          styles.floatingLabel,
          {
            paddingTop: this.state.paddingAnim,
            opacity: this.state.opacityAnim
          }
        ]}
      >
        {this.props.children}
      </Animated.View>
    );
  }
}

class TextFieldHolder extends Component {
  constructor(props) {
    super(props);

    const marginAnimation = this.props.disableFloating ? 0 : 15;

    this.state = {
      marginAnim: new Animated.Value(this.props.withValue ? marginAnimation : 0)
    };

    this.withValue = this.props.withValue ? marginAnimation : 0;
  }

  componentWillReceiveProps(newProps) {
    if (!this.props.disableFloating) {
      if (
        (newProps.withValue && this.withValue !== 15) ||
        (!newProps.withValue && this.withValue === 15)
      ) {
        this.withValue = newProps.withValue ? 15 : 0;

        return Animated.timing(this.state.marginAnim, {
          toValue: newProps.withValue ? 15 : 0,
          duration: 180
        }).start();
      }
    }
  }

  render() {
    return (
      <Animated.View style={{ marginTop: this.state.marginAnim }}>
        {this.props.children}
      </Animated.View>
    );
  }
}

class FloatLabelTextField extends Component {
  constructor(props) {
    super(props);
    this.state = {
      focused: false,
      text: this.props.value
    };
  }

  componentWillReceiveProps(newProps) {
    if (
      newProps.hasOwnProperty("value") &&
      newProps.value !== this.state.text
    ) {
      this.setState({ text: newProps.value });
    }
  }

  leftPadding() {
    return { width: this.props.leftPadding || 0 };
  }

  withBorder() {
    if (!this.props.noBorder) {
      return styles.withBorder;
    }
  }

  //if (!this.props.disableFloating) {

  render() {
    const {
      floatingStyle = {},
      inputStyle = {},
      disableFloating = false
    } = this.props;
    return (
      <View style={styles.container}>
        <View style={styles.viewContainer}>
          <View style={[styles.paddingView, this.leftPadding()]} />
          <View style={[styles.fieldContainer, this.withBorder()]}>
            {!disableFloating && (
              <FloatingLabel visible={this.state.text}>
                <Text
                  style={[styles.fieldLabel, this.labelStyle(), floatingStyle]}
                >
                  {this.placeholderValue()}
                </Text>
              </FloatingLabel>
            )}

            <TextFieldHolder
              withValue={this.state.text}
              disableFloating={disableFloating}
            >
              <TextInput
                {...this.props}
                ref="input"
                underlineColorAndroid="transparent"
                style={[styles.valueText, inputStyle]}
                defaultValue={this.props.defaultValue}
                value={this.state.text}
                maxLength={this.props.maxLength}
                onFocus={() => this.setFocus()}
                onBlur={() => this.unsetFocus()}
                onChangeText={value => this.setText(value)}
              />
            </TextFieldHolder>
          </View>
        </View>
      </View>
    );
  }

  inputRef() {
    return this.refs.input;
  }

  focus() {
    this.inputRef().focus();
  }

  blur() {
    this.inputRef().blur();
  }

  isFocused() {
    return this.inputRef().isFocused();
  }

  clear() {
    this.inputRef().clear();
  }

  setFocus() {
    this.setState({
      focused: true
    });
    try {
      return this.props.onFocus();
    } catch (_error) {}
  }

  unsetFocus() {
    this.setState({
      focused: false
    });
    try {
      return this.props.onBlur();
    } catch (_error) {}
  }

  labelStyle() {
    if (this.state.focused) {
      return styles.focused;
    }
  }

  placeholderValue() {
    if (this.state.text) {
      return this.props.placeholder;
    }
  }

  getText() {
    return this.state.text;
  }

  setText(value) {
    this.setState({
      text: value
    });
    try {
      return this.props.onChangeTextValue(value);
    } catch (_error) {}
  }
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    height: Metrics.doubleBaseMargin * 1.8
  },
  viewContainer: {
    flex: 1,
    flexDirection: "row",
    top: Platform.OS === "ios" ? 1 : 0,
    left: Platform.OS === "ios" ? Metrics.smallMargin / 2 : 0
  },
  floatingLabel: {
    position: "absolute",
    top: Metrics.smallMargin * 0.5,
    left: Metrics.smallMargin * 0.625
  },
  fieldLabel: {
    height: Metrics.baseMargin,
    left: Platform.OS === "ios" ? Metrics.smallMargin * -0.6 : 0
  },
  fieldContainer: {
    flex: 1,
    justifyContent: "center",
    position: "relative"
  },
  withBorder: {
    borderBottomWidth: 1 / 2
  },
  valueText: {},
  focused: {}
});

export default FloatLabelTextField;
