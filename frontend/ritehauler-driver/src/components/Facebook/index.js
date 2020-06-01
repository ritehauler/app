// @flow
import { Image, ViewPropTypes } from "react-native";
import { connect } from "react-redux";
import React, { Component } from "react";
import PropTypes from "prop-types";
import {
  LoginManager,
  AccessToken,
  GraphRequest,
  GraphRequestManager
} from "react-native-fbsdk";

import {
  FACEBOOK_PERMISSIONS,
  profileRequestConfig
} from "../../config/SocialLogin";
import { ButtonView, Text, Button } from "../";
import { Images, Metrics, Colors } from "../../theme";
import { request } from "../../actions/UserActions";
import Util from "../../util";

import styles from "./styles";

class Facebook extends Component {
  static propTypes = {
    request: PropTypes.func.isRequired,
    enableUserInteraction: PropTypes.bool,
    title: PropTypes.string,
    onFBLoggedIn: PropTypes.func,
    style: ViewPropTypes.style
  };

  static defaultProps = {
    enableUserInteraction: true,
    title: "Sign in with facebook",
    onFBLoggedIn: undefined,
    style: {}
  };

  _onPress = () => {
    LoginManager.logInWithReadPermissions(FACEBOOK_PERMISSIONS).then(
      login => {
        if (!login.isCancelled) {
          const { onFBLoggedIn } = this.props;
          AccessToken.getCurrentAccessToken().then(data => {
            const responseCallback = (error, result) => {
              if (error) {
                //TODO: callback; false
              } else {
                const payload = {
                  name: result.name,
                  email: result.email,
                  platform_id: result.id,
                  facebook_id: result.id,
                  social_image:
                    result.picture &&
                    result.picture.data &&
                    result.picture.data.url
                      ? result.picture.data.url
                      : "http://images.firstcovers.com/covers/i/its_easy_if_you_try-5332.jpg",
                  platform_type: "facebook",
                  device_type: Util.getPlatform(),
                  facebook_link: "www.facebook.com/" + result.id
                };

                if (onFBLoggedIn) {
                  onFBLoggedIn(payload);
                }
              }
            };

            const profileRequest = new GraphRequest(
              "/me",
              profileRequestConfig(data.accessToken.toString()),
              responseCallback
            );

            new GraphRequestManager().addRequest(profileRequest).start();
          });
        }
      },
      error => {}
    );
  };

  render() {
    const { title, enableUserInteraction, style } = this.props;

    return (
      <Button
        icon="facebook"
        background={Colors.themeColors.buttonColor2}
        color="tertiary"
        type={"brush"}
        size={"large"}
        onPress={enableUserInteraction && this._onPress}
        style={style}
      >
        {title}
      </Button>
    );
  }
}

const mapStateToProps = () => ({});

const actions = {
  request
};

export default connect(mapStateToProps, actions)(Facebook);
