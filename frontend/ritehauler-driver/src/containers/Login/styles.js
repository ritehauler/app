// @flow
import { StyleSheet } from "react-native";
import { Colors, Metrics } from "../../theme";

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
  },buttonWrapper:{
    marginTop: -Metrics.ratio(2)
  }
});
