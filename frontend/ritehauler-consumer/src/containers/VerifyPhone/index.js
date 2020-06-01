// @flow
import React, { Component } from "react";
import { View, Keyboard, ScrollView } from "react-native";
import { Actions } from "react-native-router-flux";
import { connect } from "react-redux";
import PropTypes from "prop-types";

import { Text, VerificationBox, ButtonView, Loader } from "../../components";
import IQKeyboardManager from "../../config/IQKeyboardManager";
import DataHandler from "../../util/DataHandler";
import { Strings, Colors } from "../../theme";
import styles from "./styles";
import Util from "../../util";

import {
  ENTITY_TYPE_ID_CUSTOMER,
  API_VERIFICATION_MODE_SIGN_UP,
  API_VERIFY_NUMBER,
  API_RESEND_CODE
} from "../../config/WebService";
import { request } from "../../actions/UserActions";

class VerifyPhone extends Component {
  static propTypes = {
    request: PropTypes.func.isRequired,
    user: PropTypes.object.isRequired,
    verification_mode: PropTypes.string,
    isUpdate: PropTypes.bool
  };

  static defaultProps = {
    verification_mode: API_VERIFICATION_MODE_SIGN_UP,
    isUpdate: false
  };

  constructor(props) {
    super(props);
    this.onResendPress = this.onResendPress.bind(this);
    this.onCompleteVerificationCode = this.onCompleteVerificationCode.bind(
      this
    );
    this.onSuccessResendCode = this.onSuccessResendCode.bind(this);
    this.onSuccessVerification = this.onSuccessVerification.bind(this);
  }

  componentWillMount() {
    Keyboard.dismiss();
    IQKeyboardManager.setEnable(false);
  }

  componentWillReceiveProps(nextProps) {
    // check if url is verify or resend
    const isUrlVerifyOrResend =
      nextProps.user.url === API_VERIFY_NUMBER ||
      nextProps.user.url === API_RESEND_CODE;

    // set loader if url match
    if (isUrlVerifyOrResend) {
      this.loader.setLoading(nextProps.user.isFetching);
    }

    // check verification service has error
    const isErrorInVerification =
      nextProps.user.url === API_VERIFY_NUMBER &&
      this.props.user.isFetching &&
      !nextProps.user.isFetching &&
      nextProps.user.failure;

    // reset verification box if verification service has error
    if (isErrorInVerification) {
      this.verificationBox.clear();
    }
  }

  shouldComponentUpdate(nextProps) {
    return false;
  }

  componentWillUnmount() {
    IQKeyboardManager.setEnable(true);
  }

  onSuccessResendCode(message) {
    Util.alert(message, "success");
  }

  onSuccessVerification() {
    if (DataHandler.isChangeNumber()) {
      Actions.popTo("profile");
      Util.alert(Strings.changeNumberSuccess, "success");
    } else {
      setTimeout(() => {
        Actions.home();
      }, 500);
    }
  }

  onResendPress() {
    // hide keyboard
    Keyboard.dismiss();

    // reset verification box
    this.verificationBox.clear();

    // get data
    const { user, verification_mode } = this.props;
    const { entity_id } = user.data;
    const mobile_no = Util.getLogId(user, verification_mode);

    // set payload
    const payload = {
      entity_type_id: ENTITY_TYPE_ID_CUSTOMER,
      mode: verification_mode,
      mobile_no,
      mobile_json: 1
    };
    if (verification_mode !== API_VERIFICATION_MODE_SIGN_UP) {
      payload.entity_id = entity_id;
    }

    // send request
    this.props.request(payload, API_RESEND_CODE, this.onSuccessResendCode);
  }

  onCompleteVerificationCode = verificationCode => {
    // hide keyboard
    Keyboard.dismiss();

    // get data
    const { user, verification_mode } = this.props;
    const mobile_no = Util.getLogId(user, verification_mode);

    // set payload
    const payload = {
      entity_type_id: ENTITY_TYPE_ID_CUSTOMER,
      verification_mode,
      mobile_no,
      authy_code: verificationCode,
      verification_token: user.data.auth.verification_token,
      mobile_json: 1
    };

    // send request
    this.props.request(payload, API_VERIFY_NUMBER, this.onSuccessVerification);
  };

  _renderHeader() {
    return (
      <Text textAlign="center" style={styles.header}>
        {Strings.headerVerification}
      </Text>
    );
  }

  _renderDigitsTitle() {
    return (
      <Text style={styles.enterDigit} size="medium">
        {Strings.verifyHeader}
      </Text>
    );
  }

  _renderVerificationBox() {
    return (
      <VerificationBox
        ref={ref => {
          this.verificationBox = ref;
        }}
        numberOfInputBoxs={4}
        onComplete={this.onCompleteVerificationCode}
      />
    );
  }

  _renderResendCode() {
    return (
      <ButtonView style={styles.resendCode} onPress={this.onResendPress}>
        <Text size="xSmall" color="accent">
          {Strings.resendCode}
        </Text>
      </ButtonView>
    );
  }

  _renderVerificationContainer() {
    return (
      <View style={styles.verificationContainer}>
        {this._renderDigitsTitle()}
        {this._renderVerificationBox()}
        {this._renderResendCode()}
      </View>
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
    const { isUpdate } = this.props;
    const backgroundColor = isUpdate
      ? Colors.background.primary
      : Colors.background.secondary;
    return (
      <ScrollView
        style={[styles.container, { backgroundColor }]}
        contentContainerStyle={styles.contentContainerStyle}
        keyboardShouldPersistTaps="handled"
      >
        {this._renderHeader()}
        {this._renderVerificationContainer()}
        {this._renderLoading()}
      </ScrollView>
    );
  }
}

const mapStateToProps = store => ({
  user: store.user
});
const actions = { request };

export default connect(
  mapStateToProps,
  actions
)(VerifyPhone);
