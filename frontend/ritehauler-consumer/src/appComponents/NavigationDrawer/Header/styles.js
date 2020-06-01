// @flow
import { StyleSheet, Platform } from "react-native";
import { Metrics, Colors } from "../../../theme";

export default StyleSheet.create({
  container: {
    flex: 1,
    flexDirection: "row",
    alignItems: "center",
    backgroundColor: Colors.background.secondary,
    paddingTop:
      Platform.OS === "ios"
        ? Metrics.isIphoneX
          ? Metrics.statusBarHeight + Metrics.smallMargin
          : Metrics.statusBarHeight + Metrics.smallMargin * 1.5
        : Metrics.baseMargin * 1.2,
    paddingBottom: Metrics.baseMargin * 1.2,
    paddingHorizontal: Metrics.baseMargin
  },
  title: {
    flex: 1,
    marginRight: Metrics.baseMargin
  }
});
