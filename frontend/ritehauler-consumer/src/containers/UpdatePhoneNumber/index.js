// @flow
import React, { Component } from "react";
import { ScrollView, Keyboard } from "react-native";
import { Actions } from "react-native-router-flux";
import { connect } from "react-redux";
import PropTypes from "prop-types";

import { Text, FloatLabelTextInput, Loader } from "../../components";
import { GradientButton } from "../../appComponents";
import { Strings, Colors } from "../../theme";
import styles from "./styles";
import Util from "../../util";

import {
  ENTITY_TYPE_ID_CUSTOMER,
  API_UPDATE_PHONE_NUMBER,
  API_VERIFICATION_MODE_CHANGE_NUMBER
} from "../../config/WebService";
import { request as updatePhoneNumberRequest } from "../../actions/UserActions";

class UpdatePhoneNumber extends Component {
  static propTypes = {
    updatePhoneNumberRequest: PropTypes.func.isRequired,
    user: PropTypes.object.isRequired,
    message: PropTypes.string,
    isUpdate: PropTypes.bool
  };

  static defaultProps = { message: Strings.headerAddNumber, isUpdate: false };

  constructor(props) {
    super(props);
    this.onButtonPress = this.onButtonPress.bind(this);
    this.onSuccessRequest = this.onSuccessRequest.bind(this);
  }

  componentWillMount() {
    Keyboard.dismiss();
  }

  componentWillReceiveProps(nextProps) {
    if (nextProps.user.url === API_UPDATE_PHONE_NUMBER) {
      this.loader.setLoading(nextProps.user.isFetching);
    }
  }

  shouldComponentUpdate(nextProps) {
    return false;
  }

  onSuccessRequest() {
    Actions.verifyPhone({
      verification_mode: API_VERIFICATION_MODE_CHANGE_NUMBER,
      isUpdate: this.props.isUpdate
    });
  }

  onButtonPress() {
    const inputFields = [this.phoneInput];
    if (Util.validateFields(inputFields)) {
      Keyboard.dismiss();
      this.sendRequest();
    }
  }

  sendRequest() {
    // get user entity id
    const { entity_id } = this.props.user.data;

    // get mobile number
    const mobileNumber = Util.getFormattedMobileNumber(
      Strings.countryCode,
      this.phoneInput.getText()
    );

    // set payload
    const payload = {
      entity_type_id: ENTITY_TYPE_ID_CUSTOMER,
      entity_id,
      new_login_id: mobileNumber,
      mobile_json: 1
    };

    // send request
    this.props.updatePhoneNumberRequest(
      payload,
      API_UPDATE_PHONE_NUMBER,
      this.onSuccessRequest
    );
  }

  _renderHeader() {
    const { message } = this.props;
    return (
      <Text textAlign="center" type="base" style={styles.header}>
        {message}
      </Text>
    );
  }

  _renderPhoneNumber() {
    return (
      <FloatLabelTextInput
        returnKeyType="next"
        ref={ref => {
          this.phoneInput = ref;
        }}
        keyboardType="phone-pad"
        onSubmitEditing={this.onButtonPress}
        placeholder={Strings.phoneNumber}
        leftText={Strings.countryCode}
        errorMessage={Strings.errorMessagePhone}
        errorType="required"
        disableFloating
      />
    );
  }

  _renderButton() {
    return (
      <GradientButton
        onPress={this.onButtonPress}
        style={styles.button}
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
    const { isUpdate } = this.props;
    const backgroundColor = isUpdate
      ? Colors.background.primary
      : Colors.background.secondary;
    return (
      <ScrollView
        style={[styles.container, { backgroundColor }]}
        keyboardShouldPersistTaps="handled"
      >
        {this._renderHeader()}
        {this._renderPhoneNumber()}
        {this._renderButton()}
        {this._renderLoading()}
      </ScrollView>
    );
  }
}

const mapStateToProps = store => ({
  user: store.user
});
const actions = { updatePhoneNumberRequest };

export default connect(
  mapStateToProps,
  actions
)(UpdatePhoneNumber);
