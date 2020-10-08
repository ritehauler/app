// @flow
import { StyleSheet, Dimensions } from "react-native";
import { Colors, Metrics } from "../../theme";
var { width, height } = Dimensions.get('window');

export default StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: Colors.background.primary,
    padding: Metrics.baseMargin,
    paddingTop: Metrics.heightPercentageToDP(7),
  },
  logo: {
    marginBottom: Metrics.baseMargin * 1,
  },
  signinText: {
    marginTop: Metrics.smallMargin,
    marginBottom: Metrics.doubleBaseMargin * 1,
  },
  button: {
    marginBottom: Metrics.smallMargin,
    marginTop: Metrics.smallMargin,
  },
  bottomView: {
    flexDirection: "row",
    flex: 1,
    justifyContent: "center",
    alignItems: "flex-end",
    marginBottom: Metrics.smallMargin,
  },
  notMemberText: {
    marginBottom: Metrics.baseMargin * 0.5,
  },
  signUpText: {
    padding: Metrics.baseMargin * 0.5,
  },

  buttonWrapper:{
    marginTop: -Metrics.ratio(2),
    // borderWidth: 1
  },



  textInputView: {
    // paddingLeft: width * 0.02,
    borderWidth: 1,
    // borderColor: 'black',
    borderColor: Colors.border,
    marginVertical: height * 0.005,
    flexDirection: 'row',
    marginTop: height * 0.01,
  },
  textInput: {
    paddingVertical: width * 0.06,
    paddingLeft: width * 0.02,
    borderWidth: 1,
    borderColor: Colors.border,
    marginVertical: height * 0.005,
  },
  passtextInput: {
    paddingVertical: width * 0.06,
    paddingLeft: width * 0.02,
    // borderWidth: 1,
    // borderColor: Colors.border,
    width: width * 0.7
  },
  forgotText: {
    // borderWidth: 1,
    alignSelf: 'center',
    fontSize: width * 0.033,
    color: 'grey'
  },
  error: {
    color: 'red',
    fontSize: width * 0.03,
    paddingRight: 5,
    // borderWidth: 1
  },

  disableButton: {
    backgroundColor: '#a5a5a5',
    alignItems: 'center',
    justifyContent: 'center',
    marginVertical: height * 0.013,
    padding: height * 0.025,
  },

  disableTitleButton: {
    color: 'white',
    // fontFamily: Font.bold, 
    fontSize: width * 0.05,
    fontWeight: '700'
  },

});
