// @flow
import { StyleSheet } from "react-native";
import { Metrics } from "../../theme";

export default StyleSheet.create({
  container: {
    flexDirection: "row",
    justifyContent: "center",
    alignItems: "center"
  },
  button: {
    paddingVertical: Metrics.isIphoneX
      ? Metrics.baseMargin * 2
      : Metrics.baseMargin,
    paddingLeft: Metrics.smallMargin / 2,
    paddingRight: Metrics.baseMargin
  }
});
