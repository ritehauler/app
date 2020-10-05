import Utils from "./";

//log in 
const isValidEmail = email => {
  // return  console.log(email.length)
  return (email  == undefined || !Utils.isEmailValid(email) ? false : true);
};

const isValidPass = pass => {
  return !pass.length || !Utils.isPasswordValid(pass) ? false : true;
};
//signup validation
// const isValidEmailSignup = emailSignup => {
//   return !emailSignup.length || !Utils.isValidEmailSignup(emailSignup) ? false : true;
// };
const isFirstNameValid = name => {
  return (name == undefined || !Utils.isFirstNameValid(name) ? false : true);
};
const isValidMobilNumber = number => {
  return (number == undefined|| !Utils.isValidMobilNumber(number) ? false : true);
};
const isValidPassInput = passInput => {
  return (passInput == undefined || !Utils.isValidPassInput(passInput) ? false : true);
};
const isValidConfirmPass = confirmPass => {
  return (confirmPass == undefined|| !Utils.isValidConfirmPass(confirmPass) ? false : true);
};

const isEmptyValid = numValid =>{
  return (numValid == undefined || !Utils.isValidMobileNumberDigit(numValid) ? false : true);
};

export default {
  isValidEmail,
  isValidPass,
  isFirstNameValid,
  isValidMobilNumber,
  isValidPassInput,
  isValidConfirmPass,
//  isValidEmailSignup,
 isEmptyValid
};

