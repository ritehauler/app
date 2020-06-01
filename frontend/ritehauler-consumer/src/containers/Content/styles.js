// @flow
import { StyleSheet } from "react-native";
import { Colors, Metrics } from "../../theme";

export default StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: Colors.background.primary
  },
  containerLogin: {
    flex: 1,
    backgroundColor: Colors.background.secondary
  },
  scroll: {
    flex: 1
  },
  contentContainerStyle: {
    paddingHorizontal: Metrics.baseMargin,
    paddingTop: Metrics.smallMargin,
    paddingBottom: Metrics.listBottomPadding
  },
  text: {
    lineHeight: Metrics.baseMargin * 1.5
  }
});
