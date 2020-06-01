// @flow
import { StyleSheet } from "react-native";
import { Colors, Metrics } from "../../../theme";

export default StyleSheet.create({
  container: {
    marginHorizontal: Metrics.baseMargin,
    marginBottom: Metrics.listBottomPadding,
    paddingHorizontal: Metrics.baseMargin,
    paddingVertical: Metrics.baseMargin * 1.5,
    backgroundColor: Colors.background.secondary
  },
  separatorList: {
    height: Metrics.baseMargin
  },
  list: {
    marginTop: Metrics.baseMargin
  },
  title: {
    marginBottom: Metrics.baseMargin
  }
});
