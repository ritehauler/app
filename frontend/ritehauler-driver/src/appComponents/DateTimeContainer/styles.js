// @flow
import { StyleSheet, Platform } from "react-native";
import { Colors, Metrics } from "../../theme";

export default StyleSheet.create({
  container: {
    padding: Metrics.baseMargin,
    backgroundColor: Colors.background.secondary,
    flexDirection: "row",
    alignItems: "center"
  },
  title: {
    flex: 1
  },
  value: {
    marginRight: Metrics.smallMargin
  },
  navigation: {
    marginTop:
      Platform.OS === "ios"
        ? Metrics.smallMargin * 0.125
        : Metrics.smallMargin / 2
  }
});
