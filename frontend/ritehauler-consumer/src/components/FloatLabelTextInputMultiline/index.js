// @flow
import React from "react";
import PropTypes from "prop-types";
import { View, Image, ViewPropTypes } from "react-native";
import styles from "./styles";
import { Text, ButtonView } from "../../components";
import FloatLabelTextInputRN from "./multiline";
import { Colors } from "../../theme";
import Util from "../../util";

export default class FloatLabelTextInputMultiline extends React.PureComponent {
  static propTypes = {
    secureTextEntry: PropTypes.bool,
    initialValue: PropTypes.string,
    rightText: PropTypes.string,
    onRightTextPress: PropTypes.func,
    placeholder: PropTypes.string.isRequired,
    customContainerStyle: ViewPropTypes.style,
    rightImage: PropTypes.oneOfType([PropTypes.number, PropTypes.object]),
    errorMessage: PropTypes.string,
    valueText: PropTypes.string,
    fixedHeight: PropTypes.bool
  };

  static defaultProps = {
    secureTextEntry: false,
    initialValue: "",
    onRightTextPress: () => {},
    rightText: undefined,
    customContainerStyle: {},
    rightImage: undefined,
    errorMessage: "Field can not be empty",
    valueText: "",
    fixedHeight: true
  };

  constructor(props) {
    super(props);
    this.valueTextInput = props.valueText;
  }

  focus = () => {
    this.refs.inputRef.focus();
  };

  getText = () => {
    return this.refs.inputRef.getText();
  };

  setText = text => {
    this.refs.inputRef.setText(text);
    this.setTextInput(text);
  };

  setTextInput = text => {
    this.valueTextInput = text;
  };

  checkValidation = (
    isFormSubmit = false,
    setFocus = false,
    hideError = false
  ) => {
    const { errorType } = this.props;
    const { showError } = this.state;

    if (!errorType) {
      return true;
    }

    if (this.checkValidationWithText() || hideError) {
      if (showError) {
        this.setState({ showError: false });
      }
      return true;
    }
    if (setFocus) {
      this.refs.inputRef.focus();
    }

    if (isFormSubmit) {
      this.setState({ showError: true });
    }

    return false;
  };

  checkValidationWithText = () => {
    const { errorType, isEmptyValid } = this.props;
    const text = this.refs.inputRef.getText();
    if (isEmptyValid && (!text || text === "")) {
      return true;
    } else if (errorType === "required" && text) {
      return true;
    } else if (errorType === "email" && Util.validateEmail(text)) {
      return true;
    } else if (errorType === "password" && Util.validatePassword(text)) {
      return true;
    }
    return false;
  };

  state = {
    showError: false
  };

  valueTextInput = "";

  render() {
    const {
      rightImage,
      rightText,
      onRightTextPress,
      customContainerStyle,
      placeholder,
      errorMessage,
      secureTextEntry,
      ...reset
    } = this.props;

    const { showError } = this.state;

    const borderBottomColor = showError ? Colors.error : Colors.border;

    return (
      <View style={[customContainerStyle]}>
        <View style={[styles.inputContainer, { borderBottomColor }]}>
          <FloatLabelTextInputRN
            ref="inputRef"
            noBorder="true"
            autoCapitalize="none"
            autoCorrect={false}
            onBlur={() => this.checkValidation()}
            placeholderTextColor={Colors.text.secondary}
            onChangeTextValue={this._onChangeText}
            secureTextEntry={secureTextEntry}
            floatingStyle={styles.floatingStyle}
            inputStyle={styles.inputStyle}
            placeholder={placeholder}
            value={this.valueTextInput}
            onChangeTextValue={value => this.setTextInput(value)}
            selectionColor={Colors.text.accent}
            {...reset}
          />

          {rightText && (
            <ButtonView onPress={onRightTextPress}>
              <Text size="xxSmall" color="secondary" style={styles.rightText}>
                {rightText}
              </Text>
            </ButtonView>
          )}
          {rightImage && (
            <Image source={rightImage} style={styles.rightImage} />
          )}
        </View>
        {showError && (
          <Text size="xxxSmall" color="error">
            {errorMessage}
          </Text>
        )}
      </View>
    );
  }
}
