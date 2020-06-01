// @flow
import { StyleSheet, Platform } from "react-native";
import { Metrics } from "../../theme";

export default StyleSheet.create({
  navBar: {
    height: Metrics.navBarHeight,
    flexDirection: "row",
    alignItems: "flex-end"
  },
  back: {
    paddingLeft: Metrics.baseMargin,
    paddingRight: Metrics.baseMargin * 2,
    paddingVertical: Metrics.baseMargin
  },
  myProfile: {
    marginBottom:
      Platform.OS === "ios"
        ? Metrics.baseMargin * 0.9
        : Metrics.smallMargin * 1.5
  }
});
