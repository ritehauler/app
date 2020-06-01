// @flow
import { StyleSheet } from "react-native";
import { Colors, Metrics } from "../../theme";

export default StyleSheet.create({
  container: {
    flex: 1
  },
  contentContainerStyle: {
    paddingHorizontal: Metrics.baseMargin,
    paddingBottom: Metrics.baseMargin
  },
  header: {
    paddingTop: Metrics.defaultUIHeight,
    lineHeight: Metrics.baseMargin * 1.6
  },
  verificationContainer: {
    marginHorizontal: Metrics.baseMargin,
    marginTop: Metrics.screenHeight / 11,
    alignItems: "center",
    justifyContent: "center"
  },
  enterDigit: {
    marginBottom: Metrics.baseMargin
  },
  resendCode: {
    padding: Metrics.baseMargin
  }
});
