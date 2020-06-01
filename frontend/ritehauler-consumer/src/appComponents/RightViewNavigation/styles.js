// @flow
import { StyleSheet } from "react-native";
import { Metrics, Colors } from "../../theme";

export default StyleSheet.create({
  container: {
    paddingVertical: Metrics.smallMargin,
    paddingHorizontal: Metrics.baseMargin
  },
  badgeContainer: {
    position: "absolute",
    backgroundColor: Colors.accent,
    width: Metrics.baseMargin * 1.5,
    height: Metrics.baseMargin * 1.5,
    borderRadius: Metrics.baseMargin * 0.75,
    left: Metrics.smallMargin / 2,
    alignItems: "center",
    justifyContent: "center"
  }
});
