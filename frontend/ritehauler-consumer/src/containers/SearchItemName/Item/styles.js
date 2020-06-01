// @flow
import { StyleSheet } from "react-native";
import { Colors, Metrics } from "../../../theme";

export default StyleSheet.create({
  container: {
    backgroundColor: Colors.background.secondary
  },
  itemContainer: {
    flexDirection: "row",
    padding: Metrics.baseMargin,
    justifyContent: "center",
    alignItems: "center"
  },
  title: {
    flex: 1,
    marginRight: Metrics.smallMargin
  },
  separator: {
    marginHorizontal: Metrics.baseMargin
  }
});
