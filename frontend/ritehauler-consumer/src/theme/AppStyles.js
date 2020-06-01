// // @flow
import { StyleSheet } from "react-native";
import Metrics from "./Metrics";

export default StyleSheet.create({
  flex: {
    flex: 1
  },
  sectionList: {
    paddingHorizontal: Metrics.baseMargin,
    width: Metrics.screenWidth
  },
  contentContainerStyle: {
    paddingBottom: Metrics.listBottomPadding
  }
});
