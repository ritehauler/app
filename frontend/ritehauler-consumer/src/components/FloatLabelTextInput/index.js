// @flow
import React from "react";
import { View, Image, ViewPropTypes, Platform } from "react-native";
import PropTypes from "prop-types";

import styles from "./styles";
import { Text, ButtonView } from "../../components";
import FloatLabelTextInputRN from "./signleline";
import { Colors, Metrics } from "../../theme";
import Util from "../../util";

export default class FloatLabelTextInput extends React.PureComponent {
  static propTypes = {
    secureTextEntry: PropTypes.bool,
    initialValue: PropTypes.string,
    rightText: PropTypes.string,
    leftText: PropTypes.string,
    onRightTextPress: PropTypes.func,
    placeholder: PropTypes.string.isRequired,
    customContainerStyle: ViewPropTypes.style,
    rightImage: PropTypes.oneOfType([PropTypes.number, PropTypes.object]),
    errorType: PropTypes.string,
    errorMessage: PropTypes.string,
    errorMessageRequired: PropTypes.string,
    valueText: PropTypes.string,
    onChangeTextInput: PropTypes.func,
    onFocusSet: PropTypes.func,
    onPress: PropTypes.func,
    disableRipple: PropTypes.bool
  };

  static defaultProps = {
    secureTextEntry: false,
    initialValue: "",
    onRightTextPress: () => {},
    onFocusSet: () => {},
    rightText: undefined,
    leftText: undefined,
    errorMessageRequired: undefined,
    customContainerStyle: {},
    rightImage: undefined,
    showError: false,
    errorType: undefined,
    errorMessage: "Field can not be empty",
    valueText: "",
    onChangeTextInput: undefined,
    onPress: undefined,
    disableRipple: true
  };

  constructor(props) {
    super(props);
    this.valueTextInput = props.valueText;
  }

  focus = () => {
    if (this.refs.inputRef.isFocused()) {
      this.props.onFocusSet();
    } else {
      this.refs.inputRef.focus();
    }
  };

  getText = () => {
    return this.refs.inputRef.getText();
  };

  getValueTextInput = () => {
    return this.valueTextInput;
  };

  setText = text => {
    this.refs.inputRef.setText(text);
    this.setTextInput(text);
  };

  setTextInput = text => {
    this.valueTextInput = text;
    if (this.props.onChangeTextInput) {
      this.props.onChangeTextInput(text);
    }
  };

  blur = () => {
    if (this.refs.inputRef.isFocused()) {
      this.refs.inputRef.blur();
    }
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
      const { editable } = this.props;
      if (editable !== false) {
        this.focus();
        //this.refs.inputRef.focus();
      }
    }

    if (
      isFormSubmit ||
      (this.validationMessage !== this.state.validationMessage && showError)
    ) {
      this.setState({
        showError: true,
        validationMessage: this.validationMessage
      });
    }

    return false;
  };

  checkValidationWithText = () => {
    const { errorType, isEmptyValid, getNewPassword } = this.props;
    const text = this.refs.inputRef.getText();
    if (isEmptyValid && (!text || text === "")) {
      return true;
    } else if (errorType === "required" && text) {
      return true;
    } else if (errorType === "email") {
      if (!text || text === "") {
        this.validationMessage = "required";
        return false;
      } else if (!Util.validateEmail(text)) {
        this.validationMessage = "errorMessage";
        return false;
      }
      return true;
    } else if (errorType === "phone") {
      if (!text || text === "") {
        this.validationMessage = "required";
        return false;
      } else if (!Util.validatePhone(text)) {
        this.validationMessage = "errorMessage";
        return false;
      }
      return true;
    } else if (errorType === "password" && Util.validatePassword(text)) {
      return true;
    } else if (errorType === "numeric" && Util.validateNumeric(text)) {
      return true;
    } else if (errorType === "decimal" && Util.validateDecimal(text)) {
      return true;
    } else if (errorType === "confirm_password") {
      const newPasswordText = getNewPassword ? getNewPassword() : "";
      return newPasswordText === text;
    }
    return false;
  };

  state = {
    showError: false,
    validationMessage: "errorMessage"
  };

  valueTextInput = "";
  validationMessage = "errorMessage";

  render() {
    const {
      rightImage,
      rightText,
      onRightTextPress,
      customContainerStyle,
      placeholder,
      secureTextEntry,
      errorMessage,
      onPress,
      disableRipple,
      errorMessageRequired,
      leftText,
      ...reset
    } = this.props;
    const { showError, validationMessage } = this.state;

    const borderBottomColor = showError
      ? Colors.background.error
      : Colors.background.border;

    const messageError =
      validationMessage === "required" && errorMessageRequired
        ? errorMessageRequired
        : errorMessage;

    return (
      <ButtonView
        style={[customContainerStyle]}
        onPress={onPress || this.focus}
        disableRipple={disableRipple}
      >
        <View style={[styles.inputContainer, { borderBottomColor }]}>
          {leftText && (
            <Text
              size="small"
              color="secondary"
              style={{
                marginRight: Metrics.smallMargin,
                marginTop: Platform.OS === "ios" ? Metrics.smallMargin / 4 : 0
              }}
            >
              {leftText}
            </Text>
          )}

          <FloatLabelTextInputRN
            ref="inputRef"
            noBorder="true"
            autoCapitalize="none"
            autoCorrect={false}
            placeholderTextColor={Colors.text.secondary}
            secureTextEntry={secureTextEntry}
            floatingStyle={styles.floatingStyle}
            inputStyle={styles.inputStyle}
            placeholder={placeholder}
            value={this.valueTextInput}
            onBlur={() => this.checkValidation()}
            onFocus={this.props.onFocusSet}
            selectionColor={Colors.text.accent}
            onChangeTextValue={value => this.setTextInput(value)}
            blurOnSubmit={false}
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
          <Text size="xSmall" color="error">
            {messageError}
          </Text>
        )}
      </ButtonView>
    );
  }
}
