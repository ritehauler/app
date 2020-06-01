// @flow

import { connect } from "react-redux";
import React, { Component } from "react";
import { View } from "react-native";
import { Actions } from "react-native-router-flux";
import { Text, BottomButton, FloatLabelTextInput } from "../../components";
import { ApplicationStyles } from "../../theme";
import styles from "./styles";
import { request } from "../../actions/UserActions";
import { API_ENTITY_AUTH_FORGOT_PASS } from "../../config/WebService";
import WithLoader from "../HOC/WithLoader";
import helper from "../../util/helper";
import { USER_ENTITY_TYPE_ID } from "../../constant";

class ForgotPassword extends Component {
  constructor(props) {
    super(props);
    this.validate = this.validate.bind(this);
  }

  validate() {
    if (!helper.isValidEmail(this.email.getText())) {
      this.email.setError(true);
      this.email.focus();
    } else {
      this.props.request(API_ENTITY_AUTH_FORGOT_PASS, {
        entity_type_id: USER_ENTITY_TYPE_ID,
        login_id: this.email.getText()
      });
    }
  }

  render() {
    return (
      <View style={styles.container}>
        <Text
          style={[styles.resetText, ApplicationStyles.re16Black]}
          type="medium"
        >
          Enter your email and will send{"\n"} you instruction on how to reset
          it.
        </Text>

        <FloatLabelTextInput
          autoFocus
          ref={ref => (this.email = ref)}
          onSubmitEditing={this.validate}
          placeholder="Email address"
          errorType="email"
          errorMessage="Enter a valid email address"
          keyboardType="email-address"
        />

        <BottomButton
          title="Reset"
          style={styles.button}
          onPress={this.validate}
        />
      </View>
    );
  }
}

const mapStateToProps = ({ user }) => {
  const componentData = { ...user };
  return {
    componentData,
    modal: true
  };
};
const actions = { request };

export default connect(mapStateToProps, actions)(WithLoader(ForgotPassword));
