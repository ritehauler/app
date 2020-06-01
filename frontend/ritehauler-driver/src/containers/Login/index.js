// @flow
import { connect } from "react-redux";
import { View, Image, Keyboard, NativeModules } from "react-native";
import React, { Component } from "react";
import { Actions } from "react-native-router-flux";
import { Text, BottomButton, FloatLabelTextInput } from "../../components";
import styles from "./styles";
import { Images, ApplicationStyles, Metrics, Colors } from "../../theme";
import Utils from "../../util";
import helper from "../../util/helper";
import { API_ENTITY_AUTH_EMAIL_LOGIN } from "../../config/WebService";
import { request as userRequest } from "../../actions/UserActions";
import {
  WithKeyboardSubscription,
  WithKeyboardUnSubscription
} from "../HOC/WithKeyboardListener";
import { USER_ENTITY_TYPE_ID } from "../../constant";
import WithLoader from "../HOC/WithLoader";

const GPSTracker = NativeModules.GPSTracker;

class Login extends Component {
  state = {
    isKeyboardVisible: false,
    errorEmail: false,
    errorPass: false
  };

  componentWillMount() {
    WithKeyboardSubscription(this);
  }

  componentWillUnmount() {
    WithKeyboardUnSubscription(this);
  }

  forgotScreen() {
    Keyboard.dismiss();
    setTimeout(() => {
      Actions.forgot();
    }, 200);
  }

  loginUser = () => {
    const { errorEmail, errorPass } = this.state;

    if (
      helper.isValidEmail(this.emailInput.getText()) &&
      helper.isValidPass(this.passInput.getText())
    ) {
      Keyboard.dismiss();
      this.props.userRequest(API_ENTITY_AUTH_EMAIL_LOGIN, {
        entity_type_id: USER_ENTITY_TYPE_ID,
        login_id: this.emailInput.getText(),
        password: this.passInput.getText()
        // device_udid: "aaaaa",
        // device_token: "11112222333"
      });
    } else {
      let validEmail = helper.isValidEmail(this.emailInput.getText());
      let validPass = helper.isValidPass(this.passInput.getText());

      if (!validEmail) {
        this.emailInput.setError(true);
        this.emailInput.focus();
      }
      if (!validPass) {
        this.passInput.setError(true);
        if (validEmail) this.passInput.focus();
      }
    }
  };

  renderHeader() {
    const { isKeyboardVisible } = this.state;
    if (!isKeyboardVisible) {
      return (
        <View style={{ paddingBottom: Metrics.ratio(50) }}>
          <Image source={Images.logo} style={styles.logo} />
          <Text
            style={[
              ApplicationStyles.dBold26,
              {
                paddingBottom: Utils.isPlatformAndroid()
                  ? Metrics.ratio(6)
                  : Metrics.ratio(8)
              }
            ]}
          >
            Rite Hauler Driver
          </Text>
          <Text style={ApplicationStyles.re16Black}>Sign in to continue</Text>
        </View>
      );
    }
    return null;
  }

  renderEmail() {
    return (
      <FloatLabelTextInput
        returnKeyType="next"
        ref={ref => {
          this.emailInput = ref;
        }}
        errorType="email"
        blurOnSubmit={false}
        errorMessage="Enter valid email address"
        keyboardType="email-address"
        onSubmitEditing={() => this.passInput.focus()}
        placeholder="Email address"
      />
    );
  }

  renderPassword() {
    return (
      <FloatLabelTextInput
        secureTextEntry
        returnKeyType="done"
        rightText="Forgot?"
        errorMessage="Password length must be greater then 6"
        onRightTextPress={this.forgotScreen}
        ref={ref => {
          this.passInput = ref;
        }}
        onSubmitEditing={this.loginUser}
        placeholder="Password"
      />
    );
  }

  render() {
    return (
      <View style={styles.container}>
        {this.renderHeader()}
        {this.renderEmail()}
        {this.renderPassword()}
        <View style={styles.buttonWrapper}>
          <BottomButton
            title="Sign in"
            style={styles.button}
            onPress={this.loginUser}
          />
        </View>
      </View>
    );
  }
}

const mapStateToProps = ({ user }) => {
  const componentData = { ...user };
  return { componentData, modal: true };
};

const actions = {
  userRequest
};

export default connect(mapStateToProps, actions)(WithLoader(Login));
