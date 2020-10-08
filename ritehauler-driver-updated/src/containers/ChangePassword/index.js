// @flow

import { connect } from "react-redux";
import React, { Component } from "react";
import { View } from "react-native";
import { Actions } from "react-native-router-flux";
import _ from "lodash";
import { Text, BottomButton, FloatLabelTextInput } from "../../components";
import { ApplicationStyles } from "../../theme";
import styles from "./styles";
import helper from "../../util/helper";
import WithLoader from "../HOC/WithLoader";
import { request } from "../../actions/UserActions";
import { USER_ENTITY_TYPE_ID } from "../../constant";
import { API_ENTITY_AUTH_CHANGE_PASS } from "../../config/WebService";
import {
  selectLoginUser,
  selectCachedLoginUser
} from "../../reducers/reduxSelectors";

class ChangePassword extends Component {
  constructor(props) {
    super(props);
    this.validate = this.validate.bind(this);
  }

  shouldComponentUpdate(nextProps, nextState) {
    return (
      !_.isEqual(this.props.data, nextProps.data) ||
      !_.isEqual(this.state, nextState)
    );
  }

  validate = () => {
    if (
      helper.isValidPass(this.oldPass.getText()) &&
      helper.isValidPass(this.newPass.getText()) &&
      helper.isValidPass(this.cPass.getText()) &&
      this.newPass.getText() === this.cPass.getText()
    ) {
      const { entity_id } = this.props.data;
      this.props.request(API_ENTITY_AUTH_CHANGE_PASS, {
        entity_id: entity_id,
        entity_type_id: USER_ENTITY_TYPE_ID,
        current_password: this.oldPass.getText(),
        new_password: this.newPass.getText(),
        confirm_password: this.cPass.getText()
      });
    } else {
      if (!helper.isValidPass(this.oldPass.getText()))
        this.oldPass.setError(true);

      if (!helper.isValidPass(this.newPass.getText()))
        this.newPass.setError(true);

      if (!helper.isValidPass(this.cPass.getText())) this.cPass.setError(true);

      if (!helper.isValidPass(this.oldPass.getText())) {
        this.oldPass.focus();
      } else if (!helper.isValidPass(this.newPass.getText())) {
        if (!this.oldPass.getError()) this.newPass.focus();
      } else if (!helper.isValidPass(this.cPass.getText())) {
        if (!this.oldPass.getError() && !this.newPass.getError())
          this.cPass.focus();
      } else if (this.newPass.getText() !== this.cPass.getText()) {
        this.cPass.setError(true);
        this.cPass.focus();
      }
    }
  };

  renderOldPass() {
    return (
      <FloatLabelTextInput
        secureTextEntry
        autoFocus
        ref={ref => (this.oldPass = ref)}
        onSubmitEditing={() => this.newPass.focus()}
        placeholder="Old password"
        errorType="email"
        blurOnSubmit={false}
        errorMessage="Enter a valid old password"
        keyboardType="default"
        returnKeyType="next"
      />
    );
  }

  renderNewPass() {
    return (
      <FloatLabelTextInput
        secureTextEntry
        ref={ref => (this.newPass = ref)}
        onSubmitEditing={() => this.cPass.focus()}
        placeholder="New password"
        errorType="email"
        blurOnSubmit={false}
        errorMessage="New password must be more then 6 characters"
        keyboardType="default"
        returnKeyType="next"
      />
    );
  }

  renderCPass() {
    return (
      <FloatLabelTextInput
        secureTextEntry
        ref={ref => (this.cPass = ref)}
        onSubmitEditing={this.validate}
        placeholder="Confirm password"
        errorType="email"
        errorMessage="New password and confirm password must match"
        keyboardType="default"
        returnKeyType="done"
      />
    );
  }

  render() {
    return (
      <View style={styles.container}>
        {this.renderOldPass()}
        {this.renderNewPass()}
        {this.renderCPass()}
        <BottomButton
          title="Change Password"
          style={styles.button}
          onPress={this.validate}
        />
      </View>
    );
  }
}

const mapStateToProps = ({ user }) => {
  const componentData = {
    ...user,
    data: selectCachedLoginUser(user.data)
  };
  return {
    componentData,
    modal: true
  };
};
const actions = { request };

export default connect(mapStateToProps, actions)(WithLoader(ChangePassword));
