// @flow
import { StyleSheet } from "react-native";
import { Metrics, Colors, Fonts } from "../../theme";

export default StyleSheet.create({
  container: {
    marginTop: Metrics.smallMargin * 1.25,
    paddingBottom: Metrics.smallMargin,
    backgroundColor: Colors.background.primary
  },
  line: {
    backgroundColor: Colors.background.error,
    height: 1
  },
  errorContainerStyle: {
    backgroundColor: Colors.background.login
  },
  errorMessage: {
    marginTop: Metrics.smallMargin / 2,
    //fontFamily: Fonts.type.dLight,
    fontSize: Fonts.size.xxxSmall,
    color: Colors.error
  }
});
