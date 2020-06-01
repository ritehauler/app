// @flow
import { StyleSheet } from "react-native";
import { Colors, Metrics } from "../../theme";

export default StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: Colors.background.secondary
  },
  scroll: {
    flex: 1,
    paddingHorizontal: Metrics.baseMargin
  },
  contentContainerStyle: {
    paddingTop: Metrics.screenHeight / 12,
    paddingBottom: Metrics.baseMargin * 2
  },
  welcome: {
    marginTop: Metrics.baseMargin,
    marginBottom: Metrics.smallMargin * 1.25
  },
  signInText: {
    marginBottom: Metrics.screenHeight / 16
  },
  signInButton: {
    marginTop: Metrics.inputSpacing
  },
  statusBarHeight: {
    height: Metrics.statusBarHeight
  }
});
