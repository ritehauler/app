// @flow
import React, { Component } from "react";
import { ScrollView, Keyboard } from "react-native";
import { Actions } from "react-native-router-flux";
import { connect } from "react-redux";
import PropTypes from "prop-types";

import { Text, FloatLabelTextInput, Loader } from "../../components";
import { GradientButton } from "../../appComponents";
import { Strings } from "../../theme";
import styles from "./styles";
import Util from "../../util";

import {
  ENTITY_TYPE_ID_CUSTOMER,
  API_FORGOT_PASSWORD
} from "../../config/WebService";
import { request as forgotPasswordRequest } from "../../actions/UserActions";

class ForgotPassword extends Component {
  static propTypes = {
    forgotPasswordRequest: PropTypes.func.isRequired,
    user: PropTypes.object.isRequired
  };

  static defaultProps = {};

  constructor(props) {
    super(props);
    this.onResetPress = this.onResetPress.bind(this);
    this.onSuccessForgotPasswordRequest = this.onSuccessForgotPasswordRequest.bind(
      this
    );
  }

  componentWillMount() {
    Keyboard.dismiss();
  }

  componentWillReceiveProps(nextProps) {
    if (nextProps.user.url === API_FORGOT_PASSWORD) {
      this.loader.setLoading(nextProps.user.isFetching);
    }
  }

  shouldComponentUpdate(nextProps) {
    return false;
  }

  onSuccessForgotPasswordRequest(message) {
    Actions.pop();
    Util.alert(message, "success");
  }

  onResetPress() {
    const inputFields = [this.emailInput];
    if (Util.validateFields(inputFields)) {
      Keyboard.dismiss();
      this.sendForgotPasswordRequest();
    }
  }

  sendForgotPasswordRequest() {
    const payload = {
      entity_type_id: ENTITY_TYPE_ID_CUSTOMER,
      login_id: this.emailInput.getText(),
      mobile_json: 1
    };
    this.props.forgotPasswordRequest(
      payload,
      API_FORGOT_PASSWORD,
      this.onSuccessForgotPasswordRequest
    );
  }

  _renderHeader() {
    return (
      <Text textAlign="center" type="base" style={styles.header}>
        {Strings.headerForgot}
      </Text>
    );
  }

  _renderEmail() {
    return (
      <FloatLabelTextInput
        returnKeyType="done"
        ref={ref => {
          this.emailInput = ref;
        }}
        keyboardType="email-address"
        onSubmitEditing={this.onResetPress}
        placeholder={Strings.emailAddress}
        errorType="email"
        errorMessage={Strings.errorMessageEmail}
        errorMessageRequired={Strings.errorMessageEmailRequired}
      />
    );
  }

  _renderResetButton() {
    return (
      <GradientButton
        onPress={this.onResetPress}
        style={styles.resetButton}
        text={Strings.reset}
        inBottom={false}
        setKeyboardEvent={false}
      />
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
      <ScrollView style={styles.container} keyboardShouldPersistTaps="handled">
        {this._renderHeader()}
        {this._renderEmail()}
        {this._renderResetButton()}
        {this._renderLoading()}
      </ScrollView>
    );
  }
}

const mapStateToProps = store => ({
  user: store.user
});
const actions = { forgotPasswordRequest };

export default connect(
  mapStateToProps,
  actions
)(ForgotPassword);
