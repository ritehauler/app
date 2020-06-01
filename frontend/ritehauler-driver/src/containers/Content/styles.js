// @flow
import { StyleSheet } from "react-native";
import { Colors, Metrics } from "../../theme";

export default StyleSheet.create({
  scroll: {
    flex: 1,
    backgroundColor: Colors.background.primary
  },
  contentContainerStyle: {
    paddingHorizontal: Metrics.baseMargin,
    //paddingTop: Metrics.baseMargin,
    paddingVertical: Metrics.listBottomPadding
  },
  text: {
    lineHeight: Metrics.baseMargin * 1.5
  },
  webStyle: {
    marginTop: Metrics.smallMargin * 1.2,
    marginBottom: Metrics.smallMargin * 1.2
  }
});
