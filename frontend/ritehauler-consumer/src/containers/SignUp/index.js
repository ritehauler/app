// @flow
import React, { Component } from "react";
import { View, ScrollView, Keyboard } from "react-native";
import { Actions } from "react-native-router-flux";
import { connect } from "react-redux";
import PropTypes from "prop-types";

import { Text, FloatLabelTextInput, Loader } from "../../components";
import { GradientButton, BottomViewLogin } from "../../appComponents";
import { Strings } from "../../theme";
import styles from "./styles";
import Util from "../../util";

import {
  ENTITY_TYPE_ID_CUSTOMER,
  ROLL_ID,
  API_USER_SIGN_UP
} from "../../config/WebService";
import { request as signUpRequest } from "../../actions/UserActions";

class SignUp extends Component {
  static propTypes = {
    signUpRequest: PropTypes.func.isRequired,
    user: PropTypes.object.isRequired
  };

  static defaultProps = {};

  constructor(props) {
    super(props);
    this.onSignInPress = this.onSignInPress.bind(this);
    this.onSignUpPress = this.onSignUpPress.bind(this);
    this.setFocusLastName = this.setFocusLastName.bind(this);
    this.setFocusEmail = this.setFocusEmail.bind(this);
    this.setFocusPhone = this.setFocusPhone.bind(this);
    this.setFocusPassword = this.setFocusPassword.bind(this);
    this.setFocusConfirmPassword = this.setFocusConfirmPassword.bind(this);
    this.onTermsPress = this.onTermsPress.bind(this);
    this.onPrivacyPress = this.onPrivacyPress.bind(this);
    this.getPasswordText = this.getPasswordText.bind(this);
    this.onSuccessSignUp = this.onSuccessSignUp.bind(this);
  }

  componentWillMount() {
    Keyboard.dismiss();
  }

  componentWillReceiveProps(nextProps) {
    if (nextProps.user.url === API_USER_SIGN_UP) {
      this.loader.setLoading(nextProps.user.isFetching);
    }
  }

  shouldComponentUpdate(nextProps) {
    return false;
  }

  onTermsPress() {
    Util.goToTermsOfServices(true);
  }

  onPrivacyPress() {
    Util.goToPrivacyPolicy(true);
  }

  onSignInPress() {
    Actions.pop();
  }

  onSignUpPress() {
    const inputFields = [
      this.firstNameInput,
      this.lastNameInput,
      this.emailInput,
      this.phoneInput,
      this.passInput,
      this.confirmPassInput
    ];
    if (Util.validateFields(inputFields)) {
      // set focus on first name due to keyboard hide issue if focus on password
      this.firstNameInput.focus();
      Keyboard.dismiss();
      this.sendSignUpRequest();
    }
  }

  onSuccessSignUp() {
    Actions.verifyPhone();
  }

  setFocusEmail() {
    this.emailInput.focus();
  }

  setFocusPhone() {
    this.phoneInput.focus();
  }

  setFocusPassword() {
    this.passInput.focus();
  }

  setFocusConfirmPassword() {
    this.confirmPassInput.focus();
  }

  getPasswordText() {
    return this.passInput.getText();
  }

  setFocusLastName() {
    this.lastNameInput.focus();
  }

  sendSignUpRequest() {
    const payload = {
      role_id: ROLL_ID,
      entity_type_id: ENTITY_TYPE_ID_CUSTOMER,
      is_auth_exists: 0,
      first_name: this.firstNameInput.getText(),
      last_name: this.lastNameInput.getText(),
      email: this.emailInput.getText(),
      mobile_no: Util.getFormattedMobileNumber(
        Strings.countryCode,
        this.phoneInput.getText()
      ),
      password: this.passInput.getText(),
      status: 1,
      is_notify: 1,
      system_notify: 1,
      device_token: "",
      device_type: Util.getPlatform(),
      mobile_json: 1
    };
    this.props.signUpRequest(payload, API_USER_SIGN_UP, this.onSuccessSignUp);
  }

  _renderName() {
    return (
      <View style={styles.name}>
        {this._renderFirstName()}
        {this._renderLastName()}
      </View>
    );
  }

  _renderFirstName() {
    return (
      <FloatLabelTextInput
        returnKeyType="next"
        ref={ref => {
          this.firstNameInput = ref;
        }}
        customContainerStyle={styles.firstName}
        autoCapitalize="sentences"
        onSubmitEditing={this.setFocusLastName}
        placeholder={Strings.firstName}
        errorType="required"
        onFocusSet={() =>
          Util.scrollToPosition(this.scrollView, this.firstNameInput)
        }
        errorMessage={Strings.errorMessageFirstName}
      />
    );
  }

  _renderLastName() {
    return (
      <FloatLabelTextInput
        returnKeyType="next"
        ref={ref => {
          this.lastNameInput = ref;
        }}
        customContainerStyle={styles.lastName}
        autoCapitalize="sentences"
        onSubmitEditing={this.setFocusEmail}
        placeholder={Strings.lastName}
        errorType="required"
        onFocusSet={() =>
          Util.scrollToPosition(this.scrollView, this.lastNameInput)
        }
        errorMessage={Strings.errorMessageLastName}
      />
    );
  }

  _renderEmail() {
    return (
      <FloatLabelTextInput
        returnKeyType="next"
        ref={ref => {
          this.emailInput = ref;
        }}
        keyboardType="email-address"
        onSubmitEditing={this.setFocusPhone}
        placeholder={Strings.emailAddress}
        errorType="email"
        errorMessage={Strings.errorMessageEmail}
        onFocusSet={() =>
          Util.scrollToPosition(this.scrollView, this.emailInput)
        }
        errorMessageRequired={Strings.errorMessageEmailRequired}
      />
    );
  }

  _renderPhone() {
    return (
      <FloatLabelTextInput
        returnKeyType="next"
        ref={ref => {
          this.phoneInput = ref;
        }}
        keyboardType="phone-pad"
        onSubmitEditing={this.setFocusPassword}
        placeholder={Strings.phoneNumber}
        leftText={Strings.countryCode}
        onFocusSet={() =>
          Util.scrollToPosition(this.scrollView, this.phoneInput)
        }
        errorMessage={Strings.errorMessagePhone}
        errorMessageRequired={Strings.errorMessagePhoneRequired}
        errorType="phone"
        disableFloating
      />
    );
  }

  _renderPassword() {
    return (
      <FloatLabelTextInput
        secureTextEntry
        returnKeyType="next"
        ref={ref => {
          this.passInput = ref;
        }}
        placeholder={Strings.password}
        errorType="password"
        onFocusSet={() =>
          Util.scrollToPosition(this.scrollView, this.passInput)
        }
        errorMessage={Strings.errorMessagePassword}
        onSubmitEditing={this.setFocusConfirmPassword}
      />
    );
  }

  _renderConfirmPassword() {
    return (
      <FloatLabelTextInput
        secureTextEntry
        returnKeyType="done"
        ref={ref => {
          this.confirmPassInput = ref;
        }}
        placeholder={Strings.confirmPassword}
        onSubmitEditing={this.onSignUpPress}
        errorType="confirm_password"
        onFocusSet={() =>
          Util.scrollToPosition(this.scrollView, this.confirmPassInput)
        }
        errorMessage={Strings.errorMessageConfirmPassword}
        newPassword={this.passInput}
        getNewPassword={this.getPasswordText}
      />
    );
  }

  _renderSignUpButton() {
    return (
      <GradientButton
        onPress={this.onSignUpPress}
        style={styles.signUpButton}
        text={Strings.signUp}
        inBottom={false}
        setKeyboardEvent={false}
      />
    );
  }

  _renderConfirmView() {
    return (
      <Text
        size="xSmall"
        type="light"
        textAlign="center"
        style={styles.confirmView}
      >
        {Strings.bySignUp}{" "}
        <Text size="xSmall" type="medium" onPress={this.onTermsPress}>
          {Strings.termsOfUse}{" "}
        </Text>
        <Text size="xSmall" type="light">
          {" "}
          {Strings.and}{" "}
        </Text>
        <Text size="xSmall" type="medium" onPress={this.onPrivacyPress}>
          {" "}
          {Strings.privacyPolicy}
        </Text>
      </Text>
    );
  }

  _renderBottomSignInView() {
    return (
      <BottomViewLogin
        onPress={this.onSignInPress}
        text={Strings.alreadyHaveAccount}
        clickableText={Strings.signIn}
      />
    );
  }

  _renderForm() {
    return (
      <ScrollView
        keyboardShouldPersistTaps="handled"
        style={styles.scroll}
        contentContainerStyle={styles.contentContainerStyle}
        ref={ref => {
          this.scrollView = ref;
        }}
      >
        {this._renderName()}
        {this._renderEmail()}
        {this._renderPhone()}
        {this._renderPassword()}
        {this._renderConfirmPassword()}
        {this._renderSignUpButton()}
        {this._renderConfirmView()}
      </ScrollView>
    );
  }

  _renderLoading() {
    return (
      <Loader
        ref={ref => {
          this.loader = ref;
        }}
      />
    );
  }

  render() {
    return (
      <View style={styles.container}>
        {this._renderForm()}
        {this._renderBottomSignInView()}
        {this._renderLoading()}
      </View>
    );
  }
}

const mapStateToProps = store => ({
  user: store.user
});
const actions = { signUpRequest };

export default connect(
  mapStateToProps,
  actions
)(SignUp);
