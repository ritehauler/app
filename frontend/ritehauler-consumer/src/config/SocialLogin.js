// @flow
// Facebook Settings
export const FACEBOOK_APP_ID = "";
export const FACEBOOK_PERMISSIONS = ["email", "public_profile"];
export const PROFILE_REQUEST_PARAMS = {
  fields: {
    string:
      "id, name, email, first_name, last_name, verified, picture.type(large)"
  }
};
export function profileRequestConfig(accessToken) {
  return {
    accessToken,
    parameters: PROFILE_REQUEST_PARAMS
  };
}
