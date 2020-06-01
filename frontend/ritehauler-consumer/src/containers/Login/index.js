// @flow
import React, { Component } from "react";
import { View, Image, ScrollView, Keyboard } from "react-native";
import { connect } from "react-redux";
import { Actions } from "react-native-router-flux";
import PropTypes from "prop-types";

import { Text, FloatLabelTextInput, Loader } from "../../components";
import { Facebook, GradientButton, BottomViewLogin } from "../../appComponents";
import { removeAllNotifications } from "../../util/NotificationListener";
import { Images, Strings } from "../../theme";
import styles from "./styles";
import Util from "../../util";

import {
  ENTITY_TYPE_ID_CUSTOMER,
  API_USER_SIGN_IN,
  API_SOCIAL_LOGIN
} from "../../config/WebService";
import { request as signInRequest } from "../../actions/UserActions";

class Login extends Component {
  static propTypes = {
    signInRequest: PropTypes.func.isRequired,
    user: PropTypes.object.isRequired
  };

  static defaultProps = {};

  constructor(props) {
    super(props);
    this.onForgotPress = this.onForgotPress.bind(this);
    this.onSignInPress = this.onSignInPress.bind(this);
    this.onSignUpPress = this.onSignUpPress.bind(this);
    this.setFocusPassword = this.setFocusPassword.bind(this);
    this.onSuccessSignIn = this.onSuccessSignIn.bind(this);
  }

  componentWillMount() {
    Keyboard.dismiss();
  }

  componentDidMount() {
    // reset notification if not login
    const { data } = this.props.user;
    const isLogin =
      data.auth &&
      data.auth.mobile_no &&
      data.auth.mobile_no !== "" &&
      data.auth.is_verified === 1;
    if (!isLogin) {
      removeAllNotifications();
    }
  }

  componentWillReceiveProps(nextProps) {
    // check url is login or social login
    const isUrlLoginOrFacebook =
      nextProps.user.url === API_USER_SIGN_IN ||
      nextProps.user.url === API_SOCIAL_LOGIN;

    // set loader
    if (isUrlLoginOrFacebook) {
      this.loader.setLoading(nextProps.user.isFetching);
    }
  }

  shouldComponentUpdate(nextProps) {
    return false;
  }

  onForgotPress() {
    Actions.forgotPassword();
  }

  onSignInPress() {
    const inputFields = [this.emailInput, this.passInput];
    if (Util.validateFields(inputFields)) {
      // set focus on email due to keyboard hide issue if focus on password
      this.emailInput.focus();
      Keyboard.dismiss();
      this.sendSignInRequest();
    }
  }

  onSignUpPress() {
    Actions.signUp();
  }

  onSuccessSignIn(message, data) {
    setTimeout(() => {
      if (data.auth && data.auth.is_verified === 0) {
        Util.alert(Strings.invalidCredentials);
      } else {
        Actions.home();
      }
    }, 500);
  }

  setFocusPassword() {
    this.passInput.focus();
  }

  sendSignInRequest() {
    const payload = {
      entity_type_id: ENTITY_TYPE_ID_CUSTOMER,
      login_id: this.emailInput.getText(),
      password: this.passInput.getText(),
      device_type: Util.getPlatform(),
      device_token: "",
      mobile_json: 1
    };
    this.props.signInRequest(payload, API_USER_SIGN_IN, this.onSuccessSignIn);
  }

  _renderTruckImage() {
    return <Image source={Images.truckLogo} />;
  }

  _renderWelcome() {
    return (
      <Text size="largeBig" type="bold2" style={styles.welcome}>
        {Strings.welcomeToApp}
      </Text>
    );
  }

  _renderSignInText() {
    return (
      <Text type="base2" style={styles.signInText}>
        {Strings.signInToContinue}
      </Text>
    );
  }

  _renderEmail() {
    return (
      <FloatLabelTextInput
        returnKeyType="next"
        ref={ref => {
          this.emailInput = ref;
        }}
        errorType="email"
        errorMessage={Strings.errorMessageEmail}
        errorMessageRequired={Strings.errorMessageEmailRequired}
        keyboardType="email-address"
        onSubmitEditing={this.setFocusPassword}
        placeholder={Strings.emailAddress}
      />
    );
  }

  _renderPassword() {
    return (
      <FloatLabelTextInput
        secureTextEntry
        rightText={Strings.forgot}
        returnKeyType="done"
        ref={ref => {
          this.passInput = ref;
        }}
        errorType="required"
        errorMessage={Strings.errorMessagePasswordRequired}
        placeholder={Strings.password}
        onFocusSet={() =>
          Util.scrollToPosition(this.scrollView, this.passInput)
        }
        onRightTextPress={this.onForgotPress}
        onSubmitEditing={this.onSignInPress}
      />
    );
  }

  _renderFacebookButton() {
    return <Facebook />;
  }

  _renderSignInButton() {
    return (
      <GradientButton
        onPress={this.onSignInPress}
        style={styles.signInButton}
        text={Strings.signIn}
        inBottom={false}
        setKeyboardEvent={false}
      />
    );
  }

  _renderBottomSignUpView() {
    return (
      <BottomViewLogin
        onPress={this.onSignUpPress}
        text={Strings.dontHaveAccountYet}
        clickableText={Strings.signUp}
      />
    );
  }

  _renderStatusBar() {
    return <View style={styles.statusBarHeight} />;
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
        {this._renderTruckImage()}
        {this._renderWelcome()}
        {this._renderSignInText()}
        {this._renderEmail()}
        {this._renderPassword()}
        {this._renderSignInButton()}
        {this._renderFacebookButton()}
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
        {this._renderStatusBar()}
        {this._renderForm()}
        {this._renderBottomSignUpView()}
        {this._renderLoading()}
      </View>
    );
  }
}

const mapStateToProps = store => ({
  user: store.user
});
const actions = { signInRequest };

export default connect(
  mapStateToProps,
  actions
)(Login);
