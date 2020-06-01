// @flow
import { StyleSheet } from "react-native";
import { Colors, Metrics } from "../../theme";

export default StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: Colors.background.primary
  },
  scroll: {
    padding: Metrics.baseMargin,
    backgroundColor: Colors.background.secondary,
    marginHorizontal: Metrics.baseMargin,
    marginTop: Metrics.baseMargin
  },
  header: {
    paddingHorizontal: Metrics.baseMargin,
    paddingTop: Metrics.smallMargin,
    lineHeight: Metrics.baseMargin * 1.2
  }
});
