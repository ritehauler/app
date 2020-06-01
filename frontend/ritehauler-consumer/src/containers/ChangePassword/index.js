// @flow
import React, { Component } from "react";
import { View, Keyboard, ScrollView } from "react-native";
import { connect } from "react-redux";
import PropTypes from "prop-types";

import { FloatLabelTextInput, Loader } from "../../components";
import { Strings } from "../../theme";
import { GradientButton } from "../../appComponents";
import styles from "./styles";
import Util from "../../util";

import {
  ENTITY_TYPE_ID_CUSTOMER,
  API_CHANGE_PASSWORD
} from "../../config/WebService";
import { changePasswordRequest } from "../../actions/UserActions";

class ChangePassword extends Component {
  static propTypes = {
    changePasswordRequest: PropTypes.func.isRequired,
    user: PropTypes.object.isRequired
  };

  static defaultProps = {};

  constructor(props) {
    super(props);
    this.onPressDone = this.onPressDone.bind(this);
    this.handleSubmitEditing = this.handleSubmitEditing.bind(this);
  }

  componentWillMount() {
    Keyboard.dismiss();
  }

  componentWillReceiveProps(nextProps) {
    if (nextProps.user.url === API_CHANGE_PASSWORD) {
      this.loader.setLoading(nextProps.user.isFetching);
    }
  }

  shouldComponentUpdate(nextProps) {
    return false;
  }

  onPressDone() {
    if (Util.validateFields(this.refsArray)) {
      Keyboard.dismiss();
      this.sendChangePasswordRequest();
    }
  }

  getNewPasswordText = () => {
    return this.refsArray[1].getText();
  };

  sendChangePasswordRequest = () => {
    const { entity_id } = this.props.user.data;
    const payload = {
      entity_type_id: ENTITY_TYPE_ID_CUSTOMER,
      entity_id,
      current_password: this.refsArray[0].getText(),
      new_password: this.refsArray[1].getText(),
      confirm_password: this.refsArray[2].getText(),
      mobile_json: 1
    };
    this.props.changePasswordRequest(payload, API_CHANGE_PASSWORD);
  };

  handleSubmitEditing(index) {
    if (index === this.refsArray.length - 1) {
      this.onPressDone();
    } else {
      this.refsArray[index + 1].focus();
    }
  }

  refsArray = [];

  _renderPassword(index, rest) {
    const returnKeyType = index === 2 ? "done" : "next";
    return (
      <FloatLabelTextInput
        secureTextEntry
        returnKeyType={returnKeyType}
        ref={ref => {
          this.refsArray[index] = ref;
        }}
        onSubmitEditing={() => this.handleSubmitEditing(index)}
        {...rest}
      />
    );
  }

  _renderButton() {
    return (
      <GradientButton
        onPress={this.onPressDone}
        text={Strings.changePassword}
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
      <View style={styles.container}>
        <ScrollView
          style={styles.scroll}
          contentContainerStyle={styles.contentContainerStyle}
        >
          {this._renderPassword(0, {
            placeholder: Strings.oldPassword,
            errorType: "required",
            errorMessage: Strings.errorMessageOldPassword
          })}
          {this._renderPassword(1, {
            placeholder: Strings.newPassword,
            errorType: "password",
            errorMessage: Strings.errorMessagePassword
          })}
          {this._renderPassword(2, {
            newPassword: this.refsArray[1],
            getNewPassword: this.getNewPasswordText,
            placeholder: Strings.confirmPassword,
            errorType: "confirm_password",
            errorMessage: Strings.errorMessageNewConfirmPassword
          })}
        </ScrollView>
        {this._renderButton()}
        {this._renderLoading()}
      </View>
    );
  }
}

const mapStateToProps = store => ({
  user: store.user
});
const actions = { changePasswordRequest };

export default connect(
  mapStateToProps,
  actions
)(ChangePassword);
