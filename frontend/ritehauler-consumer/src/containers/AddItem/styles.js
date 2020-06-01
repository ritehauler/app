// @flow
import { StyleSheet } from "react-native";
import { Colors, Metrics } from "../../theme";

export default StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: Colors.background.primary
  },
  scrollView: {
    flex: 1,
    paddingHorizontal: Metrics.baseMargin
  },
  contentContainerStyle: {
    paddingBottom: Metrics.baseMargin,
    paddingTop: Metrics.smallMargin
  },
  input: {
    paddingVertical: Metrics.addItemInputSpacing
  }
});
