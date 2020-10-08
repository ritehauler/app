// @flow
import { StyleSheet } from "react-native";
import { Colors, Metrics } from "../../theme";

export default StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: Colors.background.login,
    paddingBottom: Metrics.doubleBaseMargin * 1.7
  },
  scroll: {
    flex: 1
  },
  contentContainer: {
    marginHorizontal: Metrics.baseMargin
  },

  button: {
    marginBottom: Metrics.smallMargin,
    marginTop: Metrics.smallMargin
  },
  customContainerStyle: {
    paddingBottom: 0
  },
  bottomButtonWrapper: { marginBottom: -Metrics.ratio(0.5) },
  errorContainer: {
    marginBottom: 0
  },
  containerStyle: { marginTop: 0 },
  separator: {
    height: Metrics.baseMargin,
    backgroundColor: Colors.background.login
  }
});
