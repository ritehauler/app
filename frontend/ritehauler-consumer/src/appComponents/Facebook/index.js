// @flow
import React, { Component } from "react";
import { View, Image } from "react-native";
import { Actions } from "react-native-router-flux";
import PropTypes from "prop-types";
import { connect } from "react-redux";

import { Images, Strings } from "../../theme";
import styles from "./styles";
import { Text, ButtonView } from "../../components";
import FacebookService from "../../util/Facebook";
import DataHandler from "../../util/DataHandler";
import Util from "../../util";

import { request as socialLoginRequest } from "../../actions/UserActions";
import {
  ENTITY_TYPE_ID_CUSTOMER,
  API_SOCIAL_LOGIN,
  PLATFORM_TYPE_FACEBOOK
} from "../../config/WebService";

class Facebook extends Component {
  static propTypes = {
    socialLoginRequest: PropTypes.func.isRequired
  };

  static defaultProps = {};

  constructor(props) {
    super(props);
    this.onPressFacebookButton = this.onPressFacebookButton.bind(this);
    this.onSuccessFacebookRequest = this.onSuccessFacebookRequest.bind(this);
  }

  onSuccessFacebookRequest(message, data) {
    // check if user has added mobile number
    if (data.auth.mobile_no) {
      if (data.auth && data.auth.is_verified === 0) {
        Util.alert(Strings.invalidCredentials);
      } else {
        setTimeout(() => {
          Actions.home();
        }, 500);
      }
    } else {
      DataHandler.setIsChangeNumber(false);
      // if user does not have added mobile number
      Actions.updatePhoneNumber();
    }
  }

  onAuthorizeFacebook = result => {
    // set payload
    const payload = {
      name: result.name,
      email: result.email,
      first_name: result.first_name,
      last_name: result.last_name,
      platform_type: PLATFORM_TYPE_FACEBOOK,
      platform_id: result.id,
      entity_type_id: ENTITY_TYPE_ID_CUSTOMER,
      mobile_json: 1,
      device_token: "",
      device_type: Util.getPlatform()
    };

    // send request
    this.props.socialLoginRequest(
      payload,
      API_SOCIAL_LOGIN,
      this.onSuccessFacebookRequest
    );
  };

  onPressFacebookButton() {
    FacebookService.login(this.onAuthorizeFacebook);
  }

  render() {
    return (
      <ButtonView style={styles.container} onPress={this.onPressFacebookButton}>
        <Image source={Images.fb} />
        <View style={styles.midLine} />
        <Text size="medium" type="bold2" color="tertiary">
          {Strings.signInWithFacebook}
        </Text>
      </ButtonView>
    );
  }
}

const actions = { socialLoginRequest };

export default connect(
  null,
  actions
)(Facebook);
