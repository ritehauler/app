// @flow
import { StyleSheet } from "react-native";
import { Metrics } from "../../../theme";

export default StyleSheet.create({
  list: {
    marginHorizontal: Metrics.baseMargin,
    marginVertical: Metrics.baseMargin,
    flex: 1
  },
  emptyView: {
    marginVertical: Metrics.baseMargin * 3
  }
});
