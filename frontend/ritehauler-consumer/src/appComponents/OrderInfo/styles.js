// @flow
import { StyleSheet, Platform } from "react-native";
import { Colors, Metrics } from "../../theme";

export default StyleSheet.create({
  container: {
    backgroundColor: Colors.background.secondary,
    padding: Metrics.baseMargin,
    marginTop: Metrics.baseMargin
  },
  row: {
    flexDirection: "row"
  },
  left: {
    flex: 0.5,
    marginRight: Metrics.smallMargin
  },
  right: {
    flex: 0.5
  },
  full: {
    flex: 1
  },
  separator: {
    marginVertical: Metrics.baseMargin
  },
  value: {
    marginTop: Platform.OS === "ios" ? Metrics.baseMargin * 0.2 : 1
  },
  image: {
    marginRight: Metrics.smallMargin * 1.2,
    marginTop: Metrics.smallMargin / 3,
    width: Metrics.baseMargin * 2,
    height: Metrics.baseMargin * 2
  },
  customImagePlaceholderDefaultStyle: {
    width: Metrics.baseMargin * 2,
    height: Metrics.baseMargin * 2
  },
  driverView: {
    flexDirection: "row"
  }
});
