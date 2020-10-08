// @flow
import { StyleSheet } from "react-native";
import { Colors, Metrics } from "../../theme";

export default StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: Colors.background.login
  },
  scrollView: {
    flex: 1,
    paddingHorizontal: Metrics.baseMargin
  },
  contentContainerStyle: {
    paddingBottom: Metrics.baseMargin,
    paddingTop: Metrics.smallMargin * 1.5
  },
  input: {
    paddingVertical: Metrics.smallMargin / 2
  }
});
