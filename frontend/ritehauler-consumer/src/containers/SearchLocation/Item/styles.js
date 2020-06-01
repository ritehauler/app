// @flow
import { StyleSheet } from "react-native";
import { Colors, Metrics } from "../../../theme";

export default StyleSheet.create({
  container: {
    backgroundColor: Colors.background.secondary
  },
  itemContainer: {
    flexDirection: "row",
    marginHorizontal: Metrics.smallMargin * 1.5,
    paddingVertical: Metrics.baseMargin,
    alignItems: "center"
  },
  title: {
    marginHorizontal: Metrics.smallMargin * 1.5
  },
  separator: {
    marginHorizontal: Metrics.baseMargin
  }
});
