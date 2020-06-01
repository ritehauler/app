// @flow

import {
  LoginManager,
  AccessToken,
  GraphRequest,
  GraphRequestManager
} from "react-native-fbsdk";

import {
  FACEBOOK_PERMISSIONS,
  profileRequestConfig
} from "../config/SocialLogin";

const LOG = false;

class FacebookLogin {
  login(cb) {
    LoginManager.logOut();
    //LoginManager.setLoginBehavior('browser');
    LoginManager.logInWithReadPermissions(FACEBOOK_PERMISSIONS).then(
      login => {
        if (!login.isCancelled) {
          AccessToken.getCurrentAccessToken().then(data => {
            const responseCallback = (error, result) => {
              if (error) {
                if (LOG) {
                  // eslint-disable-next-line no-console
                  console.log(error);
                }
              } else {
                if (LOG) {
                  // eslint-disable-next-line no-console
                  console.log(result);
                }
                if (cb) {
                  cb(result);
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
      error => {
        console.log("Login fail with error: ", error);
      }
    );
  }
}

export default new FacebookLogin();
