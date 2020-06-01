import Utils from "./";

const isValidEmail = email => {
  return !email.length || !Utils.isEmailValid(email) ? false : true;
};

const isValidPass = pass => {
  return !pass.length || !Utils.isPasswordValid(pass) ? false : true;
};

export default {
  isValidEmail,
  isValidPass
};
