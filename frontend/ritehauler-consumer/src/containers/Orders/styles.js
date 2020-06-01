// @flow
import { StyleSheet } from "react-native";
import { Colors, Metrics } from "../../theme";

export default StyleSheet.create({
  container: {},
  list: {
    flex: 1,
    backgroundColor: Colors.background.primary
  },
  contentContainerStyle: {
    paddingHorizontal: Metrics.baseMargin,
    paddingTop: Metrics.baseMargin,
    paddingBottom: Metrics.listBottomPadding
  },
  separator: {
    height: Metrics.baseMargin
  }
});
